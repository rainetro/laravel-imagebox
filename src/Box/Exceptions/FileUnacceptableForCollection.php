<?php

namespace Rainet\ImageBox\Box\Exceptions;

use Exception;
use Illuminate\Http\UploadedFile;
use Rainet\ImageBox\Box\ImageCollection;

class FileUnacceptableForCollection extends Exception
{
    public static function create(UploadedFile $file, ImageCollection $imageCollection): self
    {
        return new static("The file with properties `{$file}` was not accepted into the collection named `{$imageCollection->name}`");
    }
}
