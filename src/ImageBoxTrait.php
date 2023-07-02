<?php

namespace Rainet\ImageBox;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Rainet\ImageBox\Box\FileAdder;
use Rainet\ImageBox\Box\ImageCollection;
use Rainet\ImageBox\Box\ImageConversion;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait ImageBoxTrait
{
    /** @var \Rainet\ImageBox\Box\ImageCollection[] */
    public array $imageCollections = [];

    /** @var \Rainet\ImageBox\Box\ImageConversion[] */
    public array $imageConversions = [];

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
        if (!$this->imageCollections) {

            $this->registerImageCollections();
        }

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

    public function addFromUrl(string $url): FileAdder
    {
        return app(FileAdder::class)->createFromUrl($this, $url);
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

    public function hasImage(string $collectionName = 'default', array $filters = []): bool
    {
        if ($this->image) {

            return true;
        }

        return false;   //  TODO: Implement hasImage() method.
    }

    public function getRegisteredImageCollections(): Collection
    {
        $this->registerImageCollections();

        return collect($this->imageCollections);
    }

    public function getRegisteredImageCollectionConversions(): Collection
    {
        $this->registerImageCollectionConversions();

        return collect($this->imageConversions);
    }

    public function getImageUrl(string $collectionName = 'default', string $conversionName = ''): string
    {
        if ($this->image) {

            return $this->image->getUrl($conversionName);
        }

        return $this->getFallbackMediaUrl($collectionName, $conversionName);
    }

    public function getFallbackMediaUrl(string $collectionName = 'default', string $conversionName = ''): string
    {
        $fallbackUrls = optional($this->getImageCollection($collectionName))->fallbackUrls;

        if (in_array($conversionName, ['default', ''], true)) {

            return $fallbackUrls[$conversionName] ?? '';
        }

        return $fallbackUrls[$conversionName] ?? $fallbackUrls['default'] ?? '';
    }

    public function getFallbackMediaPath(string $collectionName = 'default', string $conversionName = ''): string
    {
        $fallbackPaths = optional($this->getImageCollection($collectionName))->fallbackPaths;

        if (in_array($conversionName, ['default', ''], true)) {

            return $fallbackPaths[$conversionName] ?? '';
        }

        return $fallbackPaths[$conversionName] ?? $fallbackPaths['default'] ?? '';
    }
}
