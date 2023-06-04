<?php

namespace Rainet\ImageBox\Box\Exceptions;

use Exception;
use Rainet\ImageBox\Box\FolderGenerator\FolderGenerator;

class InvalidFolderGenerator extends Exception
{
    public static function doesntExist(string $folderGeneratorClass): self
    {
        return new static("Class {$folderGeneratorClass} does not exist");
    }

    public static function doesNotImplementFolderGenerator(string $folderGeneratorClass): self
    {
        return new static("Class {$folderGeneratorClass} does not implement " . FolderGenerator::class);
    }
}
