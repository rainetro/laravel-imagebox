<?php

namespace Rainet\ImageBox\Box;

use InvalidArgumentException;
use Illuminate\Support\Traits\Macroable;

class ImageCollection
{
    use Macroable;

    public string $disk = 'public';

    /** @var callable */
    public $acceptsFile;

    public array $acceptsMimeTypes = [];

    /** @var bool|int */
    public $collectionSizeLimit = false;

    public array $conversions = [];

    public bool $singleFile = false;

    /** @var array<string, string> */
    public array $fallbackUrls = [];

    /** @var array<string, string> */
    public array $fallbackPaths = [];

    public function __construct(
        public string $name,
    ) {

        $this->acceptsFile = fn () => true;
    }

    public static function create($name): self
    {
        return new static($name);
    }

    public function useDisk($disk): self
    {
        $this->disk = $disk;

        return $this;
    }

    public function getDiskName(): string
    {
        return $this->disk ?: config('imagebox.disk');
    }

    public function acceptsFile(callable $acceptsFile): self
    {
        $this->acceptsFile = $acceptsFile;

        return $this;
    }

    public function acceptsMimeTypes(array $mimeTypes): self
    {
        $this->acceptsMimeTypes = $mimeTypes;

        return $this;
    }

    public function singleFile(): self
    {
        return $this->onlyKeepLatest(1);
    }

    public function onlyKeepLatest(int $maximumNumberOfItemsInCollection): self
    {
        if ($maximumNumberOfItemsInCollection < 1) {

            throw new InvalidArgumentException('Maximum number of items in collection must be at least 1.');
        }

        $this->singleFile = ($maximumNumberOfItemsInCollection === 1);

        $this->collectionSizeLimit = $maximumNumberOfItemsInCollection;

        return $this;
    }

    public function addConversion(string $name): ImageConversion
    {
        $conversion = ImageConversion::create($name);

        $this->conversions[] = $conversion;

        return $conversion;
    }

    public function useFallbackUrl(string $url, string $conversionName = ''): self
    {
        $conversionName = $conversionName ?: 'default';

        $this->fallbackUrls[$conversionName] = $url;

        return $this;
    }

    public function useFallbackPath(string $path, string $conversionName = ''): self
    {
        $conversionName = $conversionName ?: 'default';

        $this->fallbackPaths[$conversionName] = $path;

        return $this;
    }
}
