<?php

return [

    'disk' => env('IMAGE_BOX_DISK', 'public'),

    'queue_connection_name' => env('QUEUE_CONNECTION', 'sync'),

    'queue_name' => '',

    'model' => \Rainet\ImageBox\Box\Models\Image::class,

    'folder_generator' => \Rainet\ImageBox\Box\FolderGenerator\DefaultFolderGenerator::class,
];
