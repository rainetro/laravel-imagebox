<?php

namespace Rainet\ImageBox;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Http\UploadedFile;
use Rainet\ImageBox\Box\FileAdder;
use Rainet\ImageBox\Box\ImageCollection;
use Rainet\ImageBox\Box\ImageConversion;

trait ImageBoxTrait
{
    /** @var \Rainet\ImageBox\Box\ImageCollection[] */
    public array $imageCollections = [];

    public function image(): MorphOne
    {
        return $this->morphOne(config('image-box.model'), 'model');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(config('image-box.model'), 'model');
    }

    public function addImage(string|UploadedFile $uploadedFile): FileAdder
    {
        return app(FileAdder::class)->createFromRequest($this, $uploadedFile);
    }

    public function registerImageCollections(): void
    {
    }

    public function registerImageCollectionConversions(): void
    {
    }

    public function addImageCollection(string $name): ImageCollection
    {
        $imageCollection = ImageCollection::create($name);

        $this->imageCollections[$name] = $imageCollection;

        return $imageCollection;
    }

    public function getImageCollection(string $name): ImageCollection
    {
        return $this->imageCollections[$name];
    }

    public function addConversion(string $name): void
    {
        $this->imageCollections[$name]->addConversion($name);
    }

    public function addFromRequest(string $key): FileAdder
    {
        $uploadedFile = request()->file($key);

        return app(FileAdder::class)->createFromRequest($this, $uploadedFile);
    }

    public function addFromPath(string $path): FileAdder
    {
        return app(FileAdder::class)->createFromPath($this, $path);
    }

    public function addFromURL(string $url): FileAdder
    {
        return app(FileAdder::class)->createFromURL($this, $url);
    }

    public function addFromBase64(string $content): FileAdder
    {
        return app(FileAdder::class)->createFromBase64($this, $content);
    }

    public function getOptions(string $collectionName = 'default'): array
    {
        return [
            'conversions' => array_map(fn (ImageConversion $conversion) => $conversion->toArray(), $this->imageCollections[$collectionName]->conversions),
        ];
    }
}
