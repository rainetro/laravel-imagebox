<?php

namespace Rainet\ImageBox\Box;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use GuzzleHttp\RequestOptions;
use \Illuminate\Http\UploadedFile;
use Rainet\ImageBox\Box\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Rainet\ImageBox\Box\Jobs\ProcessConversionsJob;
use Rainet\ImageBox\Box\Exceptions\FileUnacceptableForCollection;

/**
 * @property \Rainet\ImageBox\Box\ImageBoxTrait $model
 */
class FileAdder
{
    protected Model $model;
    protected UploadedFile $uploadedFile;

    protected bool $preserveOriginal = false;

    public static function createFromRequest(Model $model, UploadedFile $file): self
    {
        $fileAdder = new static();

        return $fileAdder
            ->setModel($model)
            ->setUploadedFile($file);
    }

    public static function createFromPath(Model $model, string $path): self
    {
        $fileAdder = new static();

        return $fileAdder
            ->setModel($model)
            ->setFilePath($path);
    }

    public static function createFromUrl(Model $model, string $url): self
    {
        $fileAdder = new static();

        return $fileAdder
            ->setModel($model)
            ->setFileUrl($url);
    }

    public static function createFromBase64(Model $model, string $content): self
    {
        $fileAdder = new static();

        $tmpFile = tempnam(sys_get_temp_dir(), 'imagebox_');

        file_put_contents($tmpFile, base64_decode($content));

        return $fileAdder
            ->setModel($model)
            ->setFilePath($tmpFile);
    }

    public function setModel(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function setUploadedFile(UploadedFile $file): self
    {
        $this->uploadedFile = $file;

        return $this;
    }

    public function setFilePath(string $path): self
    {
        $this->uploadedFile = new UploadedFile(
            $path,
            basename($path),
            mime_content_type($path),
            filesize($path),
            UPLOAD_ERR_OK,
            true
        );

        return $this;
    }

    public function setFileUrl(string $url): self
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'imagebox_');

        $client = new Client();

        $client->get($url, [
            RequestOptions::SINK => $tmpFile,
        ]);

        $this->uploadedFile = new UploadedFile(
            $tmpFile,
            basename($tmpFile),
            mime_content_type($tmpFile),
            filesize($tmpFile),
            UPLOAD_ERR_OK,
            true
        );

        return $this;
    }

    public function preservingOriginal(bool $preserveOriginal = true): self
    {
        $this->preserveOriginal = $preserveOriginal;

        return $this;
    }

    public function toCollection(string $collectionName = 'default'): Image
    {
        $this->model->registerImageCollections();

        $this->model->registerImageCollectionConversions();

        /** @var ImageCollection $imageCollection */
        $imageCollection = $this->model->getImageCollection($collectionName);

        if (!empty($imageCollection->acceptsMimeTypes) && !in_array($this->uploadedFile->getMimeType(), $imageCollection->acceptsMimeTypes)) {

            throw FileUnacceptableForCollection::create($this->uploadedFile, $imageCollection);
        }

        $imageClass = config('image-box.model');

        /** @var Image $image */
        $image = new $imageClass();

        $image->name = $this->fileNameSanitizer($this->uploadedFile->getClientOriginalName());

        $image->file_name = $this->randomFileName();

        $image->mime_type = $this->uploadedFile->getMimeType();

        $image->size = $this->uploadedFile->getSize();

        $image->collection = $collectionName;

        $image->disk = $imageCollection->getDiskName();

        $image->options = $this->model->getOptions($collectionName);

        $image->model()->associate($this->model);

        $image->save();

        $this->uploadedFile->storeAs(
            $image->getFolderPath(),
            $image->file_name,
            ['disk' => $image->disk],
        );

        if ($image->options['conversions']) {

            $this->processConversions($image);
        }

        return $image;
    }

    public function processConversions(Image $image): void
    {
        $job = (new ProcessConversionsJob($image, $this->preserveOriginal))
            ->onConnection(config('image-box.queue_connection_name'))
            ->onQueue(config('image-box.queue_name'));

        dispatch($job);
    }

    public function fileNameSanitizer(string $fileName): string
    {
        $fileName = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $fileName);

        $fileName = preg_replace('/\s+/', '-', $fileName);

        return $fileName;
    }

    public function randomFileName(): string
    {
        return Str::random(32) . '.' . $this->uploadedFile->getClientOriginalExtension();
    }
}
