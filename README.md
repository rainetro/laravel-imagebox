# Laravel Image Box

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rainetro/laravel-imagebox.svg?style=flat-square)](https://packagist.org/packages/rainetro/laravel-imagebox)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/rainetro/laravel-imagebox/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/rainetro/laravel-imagebox/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/rainetro/laravel-imagebox/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/rainetro/laravel-imagebox/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/rainetro/laravel-imagebox.svg?style=flat-square)](https://packagist.org/packages/rainetro/laravel-imagebox)

<!--delete-->

Use images with Eloquent models in Laravel. This package provides a simple way to attach images to Eloquent models to use in your web applications.

> This package is inspired by [spatie/laravel-medialibrary](https://github.com/spatie/laravel-medialibrary).

## Installation

You can install the package via composer:

```bash
composer require rainetro/laravel-imagebox
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Rainet\ImageBox\ImageBoxServiceProvider" --tag="migrations"
php artisan migrate
```

You can (optionally) publish the config file with:

```bash
php artisan vendor:publish --provider="Rainet\ImageBox\ImageBoxServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [
    'disk' => env('IMAGE_BOX_DISK', 'public'),
    'queue_connection_name' => env('QUEUE_CONNECTION', 'sync'),
    'queue_name' => '',
    'model' => \Rainet\ImageBox\Box\Models\Image::class,
    'folder_generator' => \Rainet\ImageBox\Box\FolderGenerator\DefaultFolderGenerator::class,
];
```

## Usage

### Attach images to your models

Ensure that your model implements the `ImageBoxInterface`.
Add the `ImageBoxTrait` trait to the model.
Implement the `registerImageCollections()` method. This method is used to define the image collections for your model. You can define one or more image collections by chaining the addImageCollection() method.
Implement the `registerImageCollectionConversions()` method. This method is used to define the conversions for your image collections. Inside this method, you can chain the addConversion() method to add conversions to a specific image collection.

```php
use Illuminate\Database\Eloquent\Model;
use Rainet\ImageBox\ImageBoxInterface;
use Rainet\ImageBox\ImageBoxTrait;

class Post extends Model implements ImageBoxInterface
{
    use ImageBoxTrait;

    //  ...

    public function registerImageCollections(): void
    {
        $this
            ->addImageCollection('image')
            ->acceptsMimeTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp']);
    }

    public function registerImageCollectionConversions(): void
    {
        $this
            ->getImageCollection('image')
            ->addConversion('thumb')
            ->width(480)
            ->quality(90);

        // $this
        //     ->getImageCollection('image')
        //     ->addConversion('medium')
        //     ->width(600)
        //     ->height(600)
        //     ->quality(90);
    }
}
```

Use the `image` method to attach images to your model.

```php
$post = Post::find(1);
$post
    ->addImage($request->file('image'))
    ->toCollection('image');
```

You can also use the `image()` or `images()` methods to retrieve the image(s).

```php
$post->load('image');
$post->load('images');
```

## Testing (Coming Soon)

We are actively working on implementing comprehensive testing for this package. Stay tuned for updates as we continue to improve the testing coverage.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
