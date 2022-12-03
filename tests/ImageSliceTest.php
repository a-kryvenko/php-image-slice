<?php

namespace Antey\ImageSlice;

use Antey\ImageSlice\Adapter\ImageResizeAdapter;
use Antey\ImageSlice\Exception\ImageSliceException;
use PHPUnit\Framework\TestCase;
use Exception;

/**
 * @covers \Antey\ImageSlice\AbstractImageSlice
 * @covers \Antey\ImageSlice\Exception\ImageSliceException
 * @uses \Antey\ImageSlice\Adapter\ImageResizeAdapter
 * @uses \Antey\ImageSlice\HorizontalImageSlice
 * @uses \Antey\ImageSlice\Storage\PathCalculator
 */
class ImageSliceTest extends TestCase
{
    public function testWrongSliceResolution(): void
    {
        $this->expectException(ImageSliceException::class);
        $imageSlice = new HorizontalImageSlice(1, 0);
    }

    public function testWrongImageResolution(): void
    {
        $this->expectException(ImageSliceException::class);
        $imageSlice = new HorizontalImageSlice(10, 10);
        $imageSlice->slice(__DIR__ . '/resources/horizontal.png');
    }
}
