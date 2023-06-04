<?php

namespace Rainet\ImageBox\Box\FolderGenerator;

use Rainet\ImageBox\Box\Models\Image;

interface FolderGenerator
{
    /**
     * Get the path for the given image.
     * @param Image $image 
     * @return string 
     */
    public function getFolderPath(Image $image): ?string;
}
