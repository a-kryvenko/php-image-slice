<?php

namespace Antey\ImageSlice;

/**
 * @covers \Antey\ImageSlice\VerticalImageSlice
 */
class VerticalImageSliceTest extends ImageSliceTestAbstract
{
    private const TEST_IMAGE_WIDTH = 3; //px
    private const TEST_IMAGE_HEIGHT = 9; //px

    protected function getImagePath(): string
    {
        return __DIR__ . '/resources/vertical.png';
    }

    protected function getDestinationFolder(): string
    {
        return __DIR__ . '/resources/vertical_parts/';
    }

    public function testSliceWithoutUpscale(): void
    {
        $imageSlice = new VerticalImageSlice(6, 3);
        $this->testImageSlice($imageSlice, [
            [3, 3],
            [3, 3],
            [3, 3],
        ]);
    }

    public function testSliceWithUpscale(): void
    {
        $imageSlice = new VerticalImageSlice(6, 3);
        $imageSlice->allowUpscale();
        $this->testImageSlice($imageSlice, [
            [6, 3],
            [6, 3],
            [6, 3],
            [6, 3],
            [6, 3],
            [6, 3],
        ]);
    }

    public function testSliceWithoutLastSlide(): void
    {
        $imageSlice = new VerticalImageSlice(3, 4);
        $this->testImageSlice($imageSlice, [
            [3, 4],
            [3, 4]
        ]);
    }

    public function testSliceWithLastSlide(): void
    {
        $imageSlice = new VerticalImageSlice(3, 4);
        $imageSlice->allowLastSlide();
        $this->testImageSlice($imageSlice, [
            [3, 4],
            [3, 4],
            [3, 1]
        ]);
    }
}
