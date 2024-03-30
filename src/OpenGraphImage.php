<?php

declare(strict_types=1);

namespace Fractal512\OpenGraphImage;

use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Typography\FontFactory;

class OpenGraphImage
{
    protected string $driver;

    protected int $imageWidth;
    protected int $imageHeight;
    protected string $backgroundColor;
    protected ?string $backgroundPath;
    protected bool $backgroundFill;
    protected array $backgroundEffects;

    protected string $overlayColor;
    protected float $overlayAlpha;

    protected ?string $logoPath;
    protected string $logoPosition;
    protected int $logoPosX;
    protected int $logoPosY;
    protected int $logoOpacity;

    protected int $textPosX;
    protected int $textPosY;

    protected string $textHorizontalAlign;
    protected string $textVerticalAlign;
    protected int $textWrapWidth;

    protected float $textLineHeight;

    protected string $textColor;
    protected int $textFontSize;
    protected ?string $textFontPath;

    private ImageInterface $image;

    private string $font = __DIR__ . '/../fonts/Roboto/Roboto-Regular.ttf';

    private array $config = [
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

    public function __construct(array $config = [])
    {
        $config = array_merge($this->config, $config);

        $this->driver = $config['driver'];
        $this->imageWidth = $config['image_width'];
        $this->imageHeight = $config['image_height'];
        $this->backgroundColor = $config['background_color'];
        $this->backgroundPath = $config['background_path'];
        $this->backgroundFill = $config['background_fill'];
        $this->backgroundEffects = $config['background_effects'];
        $this->overlayColor = $config['overlay_color'];
        $this->overlayAlpha = $config['overlay_alpha'];
        $this->logoPath = $config['logo_path'];
        $this->logoPosition = $config['logo_position'];
        $this->logoPosX = $config['logo_pos_x'];
        $this->logoPosY = $config['logo_pos_y'];
        $this->logoOpacity = $config['logo_opacity'];
        $this->textHorizontalAlign = $config['text_horizontal_align'];
        $this->textVerticalAlign = $config['text_vertical_align'];
        $this->textWrapWidth = $config['text_wrap_width'];
        $this->textPosX = $config['text_pos_x'];
        $this->textPosY = $config['text_pos_y'];
        $this->textLineHeight = $config['text_line_height'];
        $this->textColor = $config['text_color'];
        $this->textFontSize = $config['text_font_size'];
        $this->textFontPath = $config['text_font_path'];

        if (!is_null($this->textFontPath) && file_exists($this->textFontPath)) {
            $this->font = $this->textFontPath;
        }
    }

    public function make(string $text, ?string $backgroundPath = null): self
    {
        if (!is_null($backgroundPath)) {
            $this->backgroundPath = $backgroundPath;
        }

        $this->createImage();

        $this->drawBackground();

        $this->drawOverlay();

        $this->drawText($text);

        $this->drawLogo();

        return $this;
    }

    protected function createImage(): void
    {
        $manager = $this->getImageManager();

        $this->image = $manager->create($this->imageWidth, $this->imageHeight)->fill($this->backgroundColor);
    }

    protected function getImageManager(): ImageManager
    {
        switch ($this->driver) {
            case 'imagick':
                $driver = ImagickDriver::class;
                break;
            default:
                $driver = GdDriver::class;
        }

        return ImageManager::withDriver($driver);
    }

    protected function drawBackground(): self
    {
        if (!is_null($this->backgroundPath) && file_exists($this->backgroundPath)) {
            $manager = $this->getImageManager();
            $image = $manager->read($this->backgroundPath);

            if ($this->backgroundFill) {
                $backgroundWidth = $image->width();
                $backgroundHeight = $image->height();

                $backgroundRatio = $backgroundWidth / $backgroundHeight;
                $imgRatio = $this->imageWidth / $this->imageHeight;

                if ($this->imageWidth != $backgroundWidth && $this->imageHeight != $backgroundHeight) {
                    if ($backgroundRatio <= $imgRatio) {
                        $image->scale($this->imageWidth);
                    } else {
                        $image->scale(height: $this->imageHeight);
                    }
                }
            }

            if (!empty($this->backgroundEffects)) {
                $image = $this->applyEffects($image);
            }

            $this->image->place($image, 'center');
        }

        return $this;
    }

    private function applyEffects(ImageInterface $image): ImageInterface
    {
        $availableEffects = [
            'brightness',
            'contrast',
            'gamma',
            'colorize',
            'greyscale',
            'flop',
            'flip',
            'blur',
            'sharpen',
            'invert',
            'pixelate',
            'reduceColors',
        ];

        $effects = array_filter(
            $this->backgroundEffects,
            fn(string $effect) => in_array($effect, $availableEffects),
            ARRAY_FILTER_USE_KEY
        );

        foreach ($effects as $effectMethod => $argument) {
            if (is_null($argument)) {
                $image->{$effectMethod}();
            } elseif (is_array($argument)) {
                $image->{$effectMethod}(...$argument);
            } else {
                $image->{$effectMethod}($argument);
            }

        }

        return $image;
    }

    protected function drawOverlay(): self
    {
        if ($this->overlayAlpha > 0 && $this->overlayAlpha < 1) {
            if (strlen($this->overlayColor) === 4) {
                $this->overlayColor = '#' . implode('', array_map('str_repeat', str_split(str_replace('#', '', $this->overlayColor)), [2, 2, 2]));
            }

            list($red, $green, $blue) = sscanf($this->overlayColor, "#%2x%2x%2x");

            $this->image->drawRectangle(0, 0, function (RectangleFactory $rectangle) use ($red, $green, $blue) {
                $rectangle->size($this->imageWidth, $this->imageHeight);
                $rectangle->background("rgba($red, $green, $blue, $this->overlayAlpha)");
            });
        }

        return $this;
    }

    protected function drawText(string $text): self
    {
        $this->image->text($text, $this->textPosX, $this->textPosY, function (FontFactory $font) {
            $font->filename($this->font);
            $font->color($this->textColor);
            $font->size($this->textFontSize);
            $font->align($this->textHorizontalAlign);
            $font->valign($this->textVerticalAlign);
            $font->lineHeight($this->textLineHeight);
            $font->wrap($this->textWrapWidth);
        });

        return $this;
    }

    protected function drawLogo(): self
    {
        if (!is_null($this->logoPath) && file_exists($this->logoPath)) {
            $this->image->place(
                $this->logoPath,
                $this->logoPosition,
                $this->logoPosX,
                $this->logoPosY,
                $this->logoOpacity
            );
        }

        return $this;
    }

    public function save(string $path): bool
    {
        $pathParts = explode('.', $path);
        $extension = strtolower(end($pathParts));

        switch ($extension) {
            case 'png':
                $this->image->toPng()->save($path);
                return true;
            case 'jpg':
                $this->image->toJpeg()->save($path);
                return true;
            case 'webp':
                $this->image->toWebp()->save($path);
                return true;
            default:
                return false;
        }
    }
}
