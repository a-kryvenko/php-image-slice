<?php

namespace Antey\ImageSlice;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Antey\ImageSlice\HorizontalImageSlice
 */
class HorizontalImageSliceTest extends TestCase
{
    private string $imagePath = __DIR__ . '/resources/horizontal.png';
    private string $destinationFolder = __DIR__ . '/resources/horizontal_parts/';
    private int $imageWidth = 9; //px
    private int $imageHeight = 3; //px

    public function testSliceWithoutUpscale(): void
    {
        $expectedSlices = 3;

        $slicer = new HorizontalImageSlice(3, 6);
        $parts = $slicer->slice($this->imagePath, $this->destinationFolder);

        $this->assertCount($expectedSlices, $parts);

        $this->cleanUp($parts);
    }

    public function testSliceWithUpscale(): void
    {
        $expectedSlices = 6;

        $slicer = new HorizontalImageSlice(3, 6);
        $slicer->allowUpscale();
        $parts = $slicer->slice($this->imagePath, $this->destinationFolder);

        $this->assertCount($expectedSlices, $parts);

        $this->cleanUp($parts);
    }

    public function testSliceWithoutLastSlide(): void
    {
        $expectedSlices = 2;

        $slicer = new HorizontalImageSlice(4, 3);
        $parts = $slicer->slice($this->imagePath, $this->destinationFolder);

        $this->assertCount($expectedSlices, $parts);

        $this->cleanUp($parts);
    }

    public function testSliceWithLastSlide(): void
    {
        $expectedSlices = 3;

        $slicer = new HorizontalImageSlice(4, 3);
        $slicer->allowLastSlide();
        $parts = $slicer->slice($this->imagePath, $this->destinationFolder);

        $this->assertCount($expectedSlices, $parts);

        $this->cleanUp($parts);
    }

    private function cleanUp(array $paths): void
    {
        foreach ($paths as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }

        if (file_exists($this->destinationFolder)) {
            rmdir($this->destinationFolder);
        }
    }
}
