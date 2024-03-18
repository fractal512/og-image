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

    protected int $imgWidth;
    protected int $imgHeight;
    protected string $backgroundColor;
    protected ?string $backgroundPath;

    protected string $overlayColor;
    protected ?float $overlayAlpha;

    protected ?string $logoPath;
    protected string $logoPosition;
    protected int $logoPosX;
    protected int $logoPosY;
    protected int $logoOpacity;

    protected int $blockPosX;
    protected int $blockPosY;

    protected string $textHorizontalAlign;
    protected string $textVerticalAlign;
    protected int $textWrapWidth;

    protected int $lineMaxChars;
    protected float $lineHeight;

    protected int $fontSize;
    protected string $fontColor;
    protected ?string $fontPath;

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
        'img_width' => 1200,

        /*
        |--------------------------------------------------------------------------
        | Image Height
        |--------------------------------------------------------------------------
        |
        */
        'img_height' => 630,

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
        | Path to the image used for logo.
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
        | Text Block X Position
        |--------------------------------------------------------------------------
        |
        | X position of the text block from the top left corner. Depends on text
        | alignment in the block.
        |
        */
        'block_pos_x' => 100,

        /*
        |--------------------------------------------------------------------------
        | Text Block Y Position
        |--------------------------------------------------------------------------
        |
        | Y position of the text block from the top left corner. Depends on text
        | alignment in the block.
        |
        */
        'block_pos_y' => 100,

        /*
        |--------------------------------------------------------------------------
        | Text Horizontal Alignment
        |--------------------------------------------------------------------------
        |
        | Supported: "left", "center", "right"
        |
        */
        'text_horizontal_align' => 'left',

        /*
        |--------------------------------------------------------------------------
        | Text Vertical Alignment
        |--------------------------------------------------------------------------
        |
        | Supported: "top", "middle", "bottom"
        |
        */
        'text_vertical_align' => 'top',

        /*
        |--------------------------------------------------------------------------
        | Text Wrap Width
        |--------------------------------------------------------------------------
        |
        */
        'text_wrap_width' => 800,

        /*
        |--------------------------------------------------------------------------
        | Maximum Number Of Characters Per Line
        |--------------------------------------------------------------------------
        |
        | Maximum number of characters to control the width of text block.
        | The final width will depend on the font and text size.
        | Please feel free to adjust.
        |
        */
        'line_max_chars' => 40,

        /*
        |--------------------------------------------------------------------------
        | Line Height
        |--------------------------------------------------------------------------
        |
        | Supported: float
        |
        */
        'line_height' => 1.5,

        /*
        |--------------------------------------------------------------------------
        | Font Size
        |--------------------------------------------------------------------------
        |
        */
        'font_size' => 48,

        /*
        |--------------------------------------------------------------------------
        | Font Color
        |--------------------------------------------------------------------------
        |
        */
        'font_color' => '#ffffff',

        /*
        |--------------------------------------------------------------------------
        | Font Path
        |--------------------------------------------------------------------------
        |
        | If set null, will be used Preset Font (Roboto Regular)
        |
        | Supported: "absolute/path/to/your/font.ttf", null
        |
        */
        'font_path' => null,
    ];

    public function __construct(array $config = [])
    {
        $config = array_merge($this->config, $config);

        $this->driver = $config['driver'];
        $this->imgWidth = $config['img_width'];
        $this->imgHeight = $config['img_height'];
        $this->backgroundColor = $config['background_color'];
        $this->backgroundPath = $config['background_path'];
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
        $this->blockPosX = $config['block_pos_x'];
        $this->blockPosY = $config['block_pos_y'];
        $this->lineMaxChars = $config['line_max_chars'];
        $this->lineHeight = $config['line_height'];
        $this->fontSize = $config['font_size'];
        $this->fontColor = $config['font_color'];
        $this->fontPath = $config['font_path'];

        if (!is_null($this->fontPath) && file_exists($this->fontPath)) {
            $this->font = $this->fontPath;
        }
    }

    public function make(string $text, ?int $width = null, ?int $height = null, ?string $driver = null): self
    {
        if (!is_null($width)) {
            $this->imgWidth = $width;
        }

        if (!is_null($width)) {
            $this->imgHeight = $height;
        }

        if (!is_null($driver)) {
            $this->driver = $driver;
        }

        $this->createImage();

        if (!is_null($this->backgroundPath)) {
            $this->drawBackground();
        }

        if (!is_null($this->overlayAlpha)) {
            $this->drawOverlay();
        }

        $this->drawText($text);

        $this->drawLogo();

        return $this;
    }

    public function makeCustom(?int $width = null, ?int $height = null, ?string $driver = null): self
    {
        if (!is_null($width)) {
            $this->imgWidth = $width;
        }

        if (!is_null($width)) {
            $this->imgHeight = $height;
        }

        if (!is_null($driver)) {
            $this->driver = $driver;
        }

        $this->createImage();

        return $this;
    }

    protected function createImage(): void
    {
        $manager = $this->getImageManager();

        $this->image = $manager->create($this->imgWidth, $this->imgHeight)->fill($this->backgroundColor);
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

    public function drawBackground(?string $backgroundPath = null, array $effects = [], bool $fill = true): self
    {
        $path = $backgroundPath ?? $this->backgroundPath;

        if (!is_null($path) && file_exists($path)) {
            $manager = $this->getImageManager();
            $image = $manager->read($path);

            if ($fill) {
                $backgroundWidth = $image->width();
                $backgroundHeight = $image->height();

                $backgroundRatio = $backgroundWidth / $backgroundHeight;
                $imgRatio = $this->imgWidth / $this->imgHeight;

                if ($this->imgWidth != $backgroundWidth && $this->imgHeight != $backgroundHeight) {
                    if ($backgroundRatio <= $imgRatio) {
                        $image->scale($this->imgWidth);
                    } else {
                        $image->scale(height: $this->imgHeight);
                    }
                }
            }

            if (!empty($effects)) {
                $image = $this->applyEffects($image, $effects);
            }

            $this->image->place($image, 'center');
        }

        return $this;
    }

    private function applyEffects(ImageInterface $image, array $effects): ImageInterface
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
            $effects,
            fn(string $effect) => in_array($effect, $availableEffects),
            ARRAY_FILTER_USE_KEY
        );

        foreach ($effects as $effectMethod => $argument) {
            if (is_null($argument)) {
                $image->{$effectMethod}();
            } else {
                $image->{$effectMethod}($argument);
            }

        }

        return $image;
    }

    public function drawOverlay(?string $color = null, ?float $alpha = null): self
    {
        $overlayColor = $color ?? $this->overlayColor;
        $overlayAlpha = $alpha ?? $this->overlayAlpha;

        if (strlen($overlayColor) === 4) {
            $overlayColor = '#' . implode('', array_map('str_repeat', str_split(str_replace('#', '', $overlayColor)), [2, 2, 2]));
        }

        list($r, $g, $b) = sscanf($overlayColor, "#%2x%2x%2x");

        $this->image->drawRectangle(0, 0, function (RectangleFactory $rectangle) use ($r, $g, $b, $overlayAlpha) {
            $rectangle->size($this->imgWidth, $this->imgHeight);
            $rectangle->background("rgba($r, $g, $b, $overlayAlpha)");
        });

        return $this;
    }

    public function drawText(string $text): self
    {
        $this->image->text($text, $this->blockPosX, $this->blockPosY, function (FontFactory $font) {
            $font->filename($this->font);
            $font->color($this->fontColor);
            $font->size($this->fontSize);
            $font->align($this->textHorizontalAlign);
            $font->valign($this->textVerticalAlign);
            $font->lineHeight($this->lineHeight);
            $font->wrap($this->textWrapWidth);
        });

        return $this;
    }

    public function drawLogo(
        ?string $path = null,
        ?string $position = null,
        ?int $posX = null,
        ?int $posY = null,
        ?int $opacity = null
    ): self {
        $logoPath = $path ?? $this->logoPath;

        if (!is_null($logoPath) && file_exists($logoPath)) {
            $this->image->place(
                $logoPath,
                $position ?? $this->logoPosition,
                $posX ?? $this->logoPosX,
                $posY ?? $this->logoPosY,
                $opacity ?? $this->logoOpacity
            );
        }

        return $this;
    }

    public function save(string $path): bool
    {
        $pathParts = explode('.', $path);
        $extension = end($pathParts);

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
