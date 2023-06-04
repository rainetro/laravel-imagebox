<?php

namespace Rainet\ImageBox\Box\FolderGenerator;

use Rainet\ImageBox\Box\Models\Image;

class DefaultFolderGenerator implements FolderGenerator
{
    /**
     * Get the path for the given image.
     * @param Image $image 
     * @return string 
     */
    public function getFolderPath(Image $image): ?string
    {
        if ($image->id) {

            $pathID = str_pad((string) $image->id, 10, '0', STR_PAD_LEFT);

            if (strlen($pathID) === 10) {

                $pieces = [];

                $pieces[] = substr($pathID, 0, 1);
                $pieces[] = substr($pathID, 1, 2);
                $pieces[] = substr($pathID, 3, 3);
                $pieces[] = (int)$pathID;

                return $image->model_type . '/' . $image->collection . '/' . implode('/', $pieces);
            }
        }

        return NULL;
    }
}
