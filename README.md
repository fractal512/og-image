# Open Graph Image Generator

Open Graph image generator based on Intervention Image - PHP image processing library.

![OG image preview](/docs/assets/og-image-example.png?raw=true)

## Features:

- Image generation with fully customizable text, logo, background and overlay
- Background image effects, like a blur, grayscale, etc.
- Custom fading overlay over background
- Easy switch between GD and Imagick drivers

## Requirements

- PHP 8.1 or higher
- [Intervention Image](https://image.intervention.io/v3) 3.0 or higher

## Installation

The package can be installed via composer:

```bash
composer require fractal512/og-image
```

For the [Laravel](https://laravel.com/) framework use [fractal512/laravel-og-image](https://github.com/fractal512/laravel-og-image) package.

## Usage

Use `make()` method to create Open Graph image, overriding default settings with needed in `$config` array before:
```php
<?php

use Fractal512\OpenGraphImage\OpenGraphImage;

require __DIR__ . '/vendor/autoload.php';

$config = [
    'logo_path' => __DIR__ . '/path/to/logo.png',
    'background_path' => __DIR__ . '/path/to/background.png',
    // override other default settings from the configuration (see below all list)
];

$text = 'Text to be printed on the Open Graph Image';
$output = __DIR__ . '/generated-image.png';

$image = new OpenGraphImage($config);
$image->make($text)->save($output);
```

Custom background can be passed as a second parameter in `make()` method:
```php
// ...
$background = __DIR__ . '/background-image.png';
$image = new OpenGraphImage($config);
$image->make($text, $background)->save($output);
```

## Configuration

Built-in default configuration settings list:
```php
$config = [
    /*
    |--------------------------------------------------------------------------
    | Image Processing Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "gd" or "imagick"
    |
    */
    'driver' => 'gd',

    /*
    |--------------------------------------------------------------------------
    | Image Width
    |--------------------------------------------------------------------------
    |
    */
    'image_width' => 1200,

    /*
    |--------------------------------------------------------------------------
    | Image Height
    |--------------------------------------------------------------------------
    |
    */
    'image_height' => 630,

    /*
    |--------------------------------------------------------------------------
    | Image Background Color
    |--------------------------------------------------------------------------
    |
    */
    'background_color' => '#444444',

    /*
    |--------------------------------------------------------------------------
    | Background Image Path
    |--------------------------------------------------------------------------
    |
    | Path to the image used for background.
    |
    | Supported: "absolute/path/to/your/background/image.png", null
    |
    */
    'background_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Background Fill
    |--------------------------------------------------------------------------
    |
    | Scale the image to fill the background.
    |
    */
    'background_fill' => true,

    /*
    |--------------------------------------------------------------------------
    | Background Image Effects
    |--------------------------------------------------------------------------
    |
    | An associative array of image effects, where an element's key is
    | the effect method's name in the Intervention library.
    | Different methods accept different numbers of arguments.
    | If no arguments, set null as an element's value, if more than one
    | argument needs to be passed, wrap them into an array.
    | Read more: https://image.intervention.io/v3/modifying/effects
    |
    | Available effects are:
    |     "brightness" - 1 argument, integer in range -100..100
    |     "contrast" - 1 argument, integer in range -100..100
    |     "gamma" - 1 argument, float
    |     "colorize" - 3 arguments, all integers in range -100..100
    |     "greyscale" - no arguments, set null
    |     "flop" - no arguments, set null
    |     "flip" - no arguments, set null
    |     "blur" - 1 argument, integer in range 0..100
    |     "sharpen" - 1 argument, integer in range 0..100
    |     "invert" - no arguments, set null
    |     "pixelate" - 1 argument, integer
    |     "reduceColors" - 2 arguments, integer, string
    |
    */
    'background_effects' => [],

    /*
    |--------------------------------------------------------------------------
    | Overlay Color
    |--------------------------------------------------------------------------
    |
    */
    'overlay_color' => '#000000',

    /*
    |--------------------------------------------------------------------------
    | Overlay Transparency Alpha Channel
    |--------------------------------------------------------------------------
    |
    | Supported: float
    |
    */
    'overlay_alpha' => 0.35,

    /*
    |--------------------------------------------------------------------------
    | Logo Image Path
    |--------------------------------------------------------------------------
    |
    | Path to the image used for the logo.
    |
    | Supported: "absolute/path/to/your/logo/image.png", null
    |
    */
    'logo_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Logo Position
    |--------------------------------------------------------------------------
    |
    | Supported: "top-left", "top", "top-right", "left", "center", "right",
    | "bottom-left", "bottom", "bottom-right"
    |
    */
    'logo_position' => 'bottom-right',

    /*
    |--------------------------------------------------------------------------
    | Logo Offset on X-axis
    |--------------------------------------------------------------------------
    |
    */
    'logo_pos_x' => 100,

    /*
    |--------------------------------------------------------------------------
    | Logo Offset on Y-axis
    |--------------------------------------------------------------------------
    |
    */
    'logo_pos_y' => 50,

    /*
    |--------------------------------------------------------------------------
    | Logo Opacity
    |--------------------------------------------------------------------------
    |
    */
    'logo_opacity' => 100,

    /*
    |--------------------------------------------------------------------------
    | Text X Position
    |--------------------------------------------------------------------------
    |
    | Coordinate on X-axis defining the base point of the first character.
    |
    */
    'text_pos_x' => 600,

    /*
    |--------------------------------------------------------------------------
    | Text Y Position
    |--------------------------------------------------------------------------
    |
    | Coordinate on Y-axis defining the base point of the first character.
    |
    */
    'text_pos_y' => 315,

    /*
    |--------------------------------------------------------------------------
    | Text Horizontal Alignment
    |--------------------------------------------------------------------------
    |
    | Supported: "left", "center", "right"
    |
    */
    'text_horizontal_align' => 'center',

    /*
    |--------------------------------------------------------------------------
    | Text Vertical Alignment
    |--------------------------------------------------------------------------
    |
    | Supported: "top", "middle", "bottom"
    |
    */
    'text_vertical_align' => 'middle',

    /*
    |--------------------------------------------------------------------------
    | Text Wrap Width
    |--------------------------------------------------------------------------
    |
    */
    'text_wrap_width' => 1000,

    /*
    |--------------------------------------------------------------------------
    | Line Height
    |--------------------------------------------------------------------------
    |
    | Supported: float
    |
    */
    'text_line_height' => 1.5,

    /*
    |--------------------------------------------------------------------------
    | Text Color
    |--------------------------------------------------------------------------
    |
    */
    'text_color' => '#ffffff',

    /*
    |--------------------------------------------------------------------------
    | Text Font Size
    |--------------------------------------------------------------------------
    |
    */
    'text_font_size' => 48,

    /*
    |--------------------------------------------------------------------------
    | Font Path
    |--------------------------------------------------------------------------
    |
    | If null, preset font (Roboto Regular) will be used.
    |
    | Supported: "absolute/path/to/your/font.ttf", null
    |
    */
    'text_font_path' => null,
];
```

## Credits

- Denys Vashchuk ([fractal512](https://github.com/fractal512))
- [All Contributors](https://github.com/fractal512/og-image/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
