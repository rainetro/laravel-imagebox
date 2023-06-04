<?php

namespace Rainet\ImageBox\Box\Models\Observers;

use Rainet\ImageBox\Box\Models\Image;

class ImageObserver
{
    public function creating(Image $image)
    {
        if ($image->shouldSortWhenCreating()) {

            if (is_null($image->order_number)) {

                $image->setHighestOrderNumber();
            }
        }
    }

    public function updating(Image $image)
    {
        //  TODO: 
    }

    public function updated(Image $image)
    {
        if (is_null($image->getOriginal('model_id'))) {

            return NULL;
        }

        //  TODO: 
    }

    public function deleted(Image $image)
    {
        //  TODO: 

        $image->deleteAllFiles($image);
    }
}
