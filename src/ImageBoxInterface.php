<?php

namespace Rainet\ImageBox;

use Illuminate\Http\UploadedFile;
use Rainet\ImageBox\Box\FileAdder;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface ImageBoxInterface
{
    public function image(): MorphOne;

    public function images(): MorphMany;

    public function addImage(string|UploadedFile $uploadedFile): FileAdder;

    public function registerImageCollections(): void;

    public function registerImageCollectionConversions(): void;
}
