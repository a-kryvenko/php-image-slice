<?php

namespace Antey\ImageSlice;

/**
 * @covers \Antey\ImageSlice\HorizontalImageSlice
 * @covers \Antey\ImageSlice\AbstractImageSlice
 * @uses \Antey\ImageSlice\Adapter\ImageResizeAdapter
 * @uses \Antey\ImageSlice\Storage\PathCalculator
 */
class HorizontalImageSliceTest extends ImageSliceTestAbstract
{
    private const TEST_IMAGE_WIDTH = 9; //px
    private const TEST_IMAGE_HEIGHT = 3; //px

    protected function getImagePath(): string
    {
        return __DIR__ . '/resources/horizontal.png';
    }

    protected function getDestinationFolder(): string
    {
        return __DIR__ . '/resources/horizontal_parts/';
    }

    public function testSliceWithoutUpscale(): void
    {
        $imageSlice = new HorizontalImageSlice(3, 6);
        $this->testImageSlice($imageSlice, [
            [3, 3],
            [3, 3],
            [3, 3],
        ]);
    }

    public function testSliceWithUpscale(): void
    {
        $imageSlice = new HorizontalImageSlice(3, 6);
        $imageSlice->allowUpscale();

        $this->testImageSlice($imageSlice, [
            [3, 6],
            [3, 6],
            [3, 6],
            [3, 6],
            [3, 6],
            [3, 6],
        ]);
    }

    public function testSliceWithoutLastSlide(): void
    {
        $imageSlice = new HorizontalImageSlice(4, 3);
        $this->testImageSlice($imageSlice, [
            [4, 3],
            [4, 3],
        ]);
    }

    public function testSliceWithLastSlide(): void
    {
        $imageSlice = new HorizontalImageSlice(4, 3);
        $imageSlice->allowLastSlide();
        $this->testImageSlice($imageSlice, [
            [4, 3],
            [4, 3],
            [1, 3]
        ]);
    }
}
