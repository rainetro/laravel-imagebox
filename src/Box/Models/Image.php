<?php

namespace Rainet\ImageBox\Box\Models;

use Illuminate\Database\Eloquent\Model;
use Rainet\ImageBox\Box\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Rainet\ImageBox\Box\Models\Concerns\IsSorted;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Rainet\ImageBox\Box\FolderGenerator\FolderGeneratorFactory;

class Image extends Model
{
    use HasUuid;
    use IsSorted;

    protected $table = 'box_images';

    protected $guarded = [];

    protected $appends = [
        //'url',
        //'thumbnail_url',
        //'responsive_images',
    ];

    protected $casts = [
        'options' => 'json',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    protected function extension(): Attribute
    {
        return Attribute::get(fn () => pathinfo($this->file_name, PATHINFO_EXTENSION));
    }

    public function getFolderPath(): ?string
    {
        $folderGenerator = FolderGeneratorFactory::create();

        return $folderGenerator->getFolderPath($this);
    }

    public function getUrlAttribute(): ?string
    {
        $folder = $this->getFolderPath();

        return url('storage/' . $folder . '/' . $this->file_name);
    }

    public function getUrlsAttribute(): ?object
    {
        $folder = $this->getFolderPath();

        $file_name = pathinfo($this->file_name, PATHINFO_FILENAME);
        $file_ext = pathinfo($this->file_name, PATHINFO_EXTENSION);

        $data = [
            'original' => url('storage/' . $folder . '/' . $this->file_name),
        ];

        if (count($this->options['conversions'] ?? []) > 0) {

            foreach ($this->options['conversions'] as $conversion) {

                if ($conversion['executed'] ?? NULL) {

                    $data[$conversion['name']] = url('storage/' . $folder . '/' . $file_name . '-' . $conversion['name'] . '.' . $file_ext);
                } else {

                    $data[$conversion['name']] = $data['original'];
                }
            }
        }

        return (object)$data;
    }

    public function getPathAttribute(): ?string
    {
        $folder = $this->getFolderPath();

        return storage_path('app/public/' . $folder . '/' . $this->file_name);
    }

    public function getPathForConversion(string $conversionName): ?string
    {
        $folder = $this->getFolderPath();

        $file_name = pathinfo($this->file_name, PATHINFO_FILENAME);
        $file_ext = pathinfo($this->file_name, PATHINFO_EXTENSION);

        return storage_path('app/public/' . $folder . '/' . $file_name . '-' . $conversionName . '.' . $file_ext);
    }
}
