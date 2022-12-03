<?php

namespace Antey\ImageSlice;

use Antey\ImageSlice\Exception\ImageSliceException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Antey\ImageSlice\AbstractImageSlice
 * @covers \Antey\ImageSlice\Exception\ImageSliceException
 */
class ImageSliceTest extends TestCase
{
    public function testWrongResolution(): void
    {
        $this->expectException(ImageSliceException::class);
        $imageSlice = new HorizontalImageSlice(1, 0);
    }
}
