<?php

declare(strict_types=1);

namespace Fractal512\OpenGraphImage\Tests;

use Fractal512\OpenGraphImage\OpenGraphImage;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class OpenGraphImageTest extends TestCase
{
    private OpenGraphImage $openGraphImage;
    private string $directoryPath;

    protected function setUp(): void
    {
        $this->openGraphImage = new OpenGraphImage();

        $rootDirName = 'virtualDir';
        vfsStream::setup($rootDirName);
        $this->directoryPath = vfsStream::url($rootDirName);
    }

    /**
     * @dataProvider textProvider
     */
    public function testMake(string $text): void
    {
        $result = $this->openGraphImage->make($text);
        $this->assertInstanceOf(OpenGraphImage::class, $result);

        $background = null;
        $result = $this->openGraphImage->make($text, $background);
        $this->assertInstanceOf(OpenGraphImage::class, $result);

        $background = 'path/to/background/image.png';
        $result = $this->openGraphImage->make($text, $background);
        $this->assertInstanceOf(OpenGraphImage::class, $result);
    }

    /**
     * @dataProvider textProvider
     */
    public function testSave(string $text): void
    {
        $path = $this->directoryPath . '/test-image.wrong';

        $openGraphImage = new OpenGraphImage();
        $result = $openGraphImage->make($text)->save($path);
        $this->assertFalse($result);

        $path = $this->directoryPath . '/test-image.png';

        $openGraphImage = new OpenGraphImage();
        $result = $openGraphImage->save($path);
        $this->assertFalse($result);

        $openGraphImage = new OpenGraphImage();
        $result = $openGraphImage->make($text)->save($path);
        $this->assertTrue($result);
        $this->assertEquals('image/png', mime_content_type($path));

        $path = $this->directoryPath . '/test-image.jpg';

        $openGraphImage = new OpenGraphImage();
        $result = $openGraphImage->make($text)->save($path);
        $this->assertTrue($result);
        $this->assertEquals('image/jpeg', mime_content_type($path));

        $path = $this->directoryPath . '/test-image.webp';

        $openGraphImage = new OpenGraphImage();
        $result = $openGraphImage->make($text)->save($path);
        $this->assertFalse($result);

        $openGraphImage = new OpenGraphImage(['driver' => 'imagick']);
        $result = $openGraphImage->make($text)->save($path);
        $this->assertTrue($result);
        $this->assertEquals('image/webp', mime_content_type($path));
    }

    public function textProvider(): array
    {
        return [
            [
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque porta sapien eros,
                 euismod condimentum magna facilisis ac. Praesent sagittis eu urna at dictum.
                 Mauris sollicitudin porttitor augue, vitae iaculis eros laoreet sit amet.",
            ],
            ["Morbi commodo quam sit amet orci rhoncus, nec aliquet arcu tincidunt. Vestibulum semper facilisis"],
            ["Donec ut tincidunt ante, non auctor est"],
        ];
    }
}
