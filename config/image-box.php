<?php

return [

    'disk' => env('IMAGE_BOX_DISK', 'public'),

    'queue_connection_name' => env('QUEUE_CONNECTION', 'sync'),

    'queue_name' => '',

    'queue_conversions_by_default' => env('QUEUE_CONVERSIONS_BY_DEFAULT', true),

    'model' => \Rainet\ImageBox\Box\Models\Image::class,

    'folder_generator' => \Rainet\ImageBox\Box\FolderGenerator\DefaultFolderGenerator::class,
];
