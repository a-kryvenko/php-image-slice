<?php

use Antey\ImageSlice\Adapter\ImageResizeAdapter;
use Gumlet\ImageResize;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Antey\ImageSlice\Adapter\ImageResizeAdapter
 * @uses \Gumlet\ImageResize
 */
class ImageResizeAdapterTest extends TestCase
{
    public function testConstructorPath(): void
    {
        $imageResize = $this->createMock(ImageResize::class);
        $imageResize->method('getDestWidth')->willReturn(100);

        $adapter = new ImageResizeAdapter(__DIR__ . '/../resources/horizontal.png');
        $this->assertEquals(9, $adapter->getWidth());
    }

    public function testConstructorObject(): void
    {
        $imageResize = $this->createMock(ImageResize::class);
        $imageResize->method('getDestWidth')->willReturn(100);

        $adapter = new ImageResizeAdapter(__DIR__ . '/../resources/horizontal.png', $imageResize);

        $this->assertEquals(100, $adapter->getWidth());
    }

    public function testLoad(): void
    {
        $imageResize = $this->createMock(ImageResize::class);
        $imageResize->method('getDestWidth')->willReturn(100);
        $imageResize2 = $this->createMock(ImageResize::class);
        $imageResize2->method('getDestWidth')->willReturn(200);

        $adapter = new ImageResizeAdapter('', $imageResize);
        $this->assertEquals(100, $adapter->getWidth());

        $adapter = $adapter->load('', $imageResize2);
        $this->assertEquals(200, $adapter->getWidth());
    }

    public function testSave(): void
    {
        $imageResize = $this->createMock(ImageResize::class);
        $imageResize->expects($this->once())
            ->method('save')
            ->with('filepath');

        $adapter = new ImageResizeAdapter('', $imageResize);
        $adapter->save('filepath');
    }

    public function testFreecrop(): void
    {
        $imageResize = $this->createMock(ImageResize::class);
        $imageResize->expects($this->once())
            ->method('freecrop')
            ->with(10, 10, 1, 0);

        $adapter = new ImageResizeAdapter('', $imageResize);
        $adapter->freecrop(10, 10, 1);
    }

    public function testGetWidth(): void
    {
        $imageResize = $this->createMock(ImageResize::class);
        $imageResize->expects($this->once())
            ->method('getDestWidth')
            ->willReturn(100);

        $adapter = new ImageResizeAdapter('', $imageResize);

        $this->assertEquals(100, $adapter->getWidth());
    }

    public function testGetHeight(): void
    {
        $imageResize = $this->createMock(ImageResize::class);
        $imageResize->expects($this->once())
            ->method('getDestHeight')
            ->willReturn(100);

        $adapter = new ImageResizeAdapter('', $imageResize);

        $this->assertEquals(100, $adapter->getHeight());
    }

    public function testResizeToWidth(): void
    {
        $imageResize = $this->createMock(ImageResize::class);
        $imageResize->expects($this->once())
            ->method('resizeToWidth')
            ->with(100);

        $adapter = new ImageResizeAdapter('', $imageResize);
        $adapter->resizeToWidth(100);
    }

    public function testResizeToHeight(): void
    {
        $imageResize = $this->createMock(ImageResize::class);
        $imageResize->expects($this->once())
            ->method('resizeToHeight')
            ->with(100);

        $adapter = new ImageResizeAdapter('', $imageResize);
        $adapter->resizeToHeight(100);
    }
}
