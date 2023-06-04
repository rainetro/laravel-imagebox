<?php

namespace Rainet\ImageBox\Box;

/** @mixin \Intervention\Image\ImageManager */
class ImageConversion
{
    public ?int $width;
    public ?int $height;

    public int $quality = 100;

    public function __construct(
        public string $name,
    ) {
    }

    public static function create(string $name): self
    {
        return new static($name);
    }

    public function width(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function height(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function quality(int $quality): self
    {
        $this->quality = $quality;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'width' => $this->width ?? NULL,
            'height' => $this->height ?? NULL,
            'quality' => $this->quality,
            'executed' => false,
        ];
    }
}
