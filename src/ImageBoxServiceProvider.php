<?php

namespace Rainet\ImageBox;

use Illuminate\Support\ServiceProvider;
use Rainet\ImageBox\Box\Models\Observers\ImageObserver;

class ImageBoxServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/image-box.php' => config_path('image-box.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations/create_box_images_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_box_images_table.php'),
        ], 'migrations');

        $imageClass = config('image-box.model');

        $imageClass::observe(ImageObserver::class);
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/image-box.php', 'image-box');
    }
}
