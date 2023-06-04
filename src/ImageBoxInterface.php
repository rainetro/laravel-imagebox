<?php

namespace Rainet\ImageBox;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Http\UploadedFile;
use Rainet\ImageBox\Box\FileAdder;

interface ImageBoxInterface
{
    public function image(): MorphOne;

    public function images(): MorphMany;

    public function addImage(string|UploadedFile $uploadedFile): FileAdder;

    public function registerImageCollections(): void;

    public function registerImageCollectionConversions(): void;
}
