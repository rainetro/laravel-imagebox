<?php

namespace Rainet\ImageBox\Box\FolderGenerator;

use Rainet\ImageBox\Box\Exceptions\InvalidFolderGenerator;

class FolderGeneratorFactory
{
    public static function create(): FolderGenerator
    {
        $className = config('image-box.folder_generator');

        static::guardAgainstInvalidFolderGenerator($className);

        return app($className);
    }

    protected static function guardAgainstInvalidFolderGenerator(string $folderGeneratorClass): void
    {
        if (!class_exists($folderGeneratorClass)) {

            throw InvalidFolderGenerator::doesntExist($folderGeneratorClass);
        }

        if (!is_subclass_of($folderGeneratorClass, FolderGenerator::class)) {

            throw InvalidFolderGenerator::doesNotImplementFolderGenerator($folderGeneratorClass);
        }
    }
}
