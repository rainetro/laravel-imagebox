<?php

namespace Rainet\ImageBox\Box\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Rainet\ImageBox\Box\Models\Image;

class ProcessConversionsJob implements ShouldQueue
{
    use InteractsWithQueue;
    use SerializesModels;
    use Queueable;

    public function __construct(
        protected Image $image,
        protected bool $preserveOriginal = false,
    ) {
    }

    public function handle(): bool
    {
        if ($this->image->exists && $this->image->options && count($this->image->options['conversions'] ?? []) > 0) {

            try {

                $image = \Intervention\Image\Facades\Image::make($this->image->path);

                $options = $this->image->options;
                $changes = false;

                foreach ($options['conversions'] as $i => $conversion) {

                    $image->resize($conversion['width'], $conversion['height'], function ($constraint) {

                        $constraint->aspectRatio();
                    });

                    $path = $this->image->getPathForConversion($conversion['name']);

                    $image->save($path, $conversion['quality'] ?? 100);

                    $options['conversions'][$i]['executed'] = true;
                    $changes = true;
                }

                $image->destroy();

                if ($changes) {

                    $this->image->options = $options;
                    $this->image->save();

                    return true;
                }
            } catch (\Exception $e) {

                $this->fail($e);
            }
        }

        return false;
    }
}
