<?php

namespace Rainet\ImageBox\Box;

use Illuminate\Support\Traits\Macroable;

class ImageCollection
{
    use Macroable;

    public string $disk = 'public';

    /** @var callable */
    public $acceptsFile;

    public array $acceptsMimeTypes = [];

    public array $conversions = [];

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

    public function acceptsMimeTypes(array $mimeTypes): self
    {
        $this->acceptsMimeTypes = $mimeTypes;

        return $this;
    }

    public function addConversion(string $name): ImageConversion
    {
        $conversion = ImageConversion::create($name);

        $this->conversions[] = $conversion;

        return $conversion;
    }
}
