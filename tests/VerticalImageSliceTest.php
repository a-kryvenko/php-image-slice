<?php

namespace Antey\ImageSlice;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Antey\ImageSlice\VerticalImageSlice
 */
class VerticalImageSliceTest extends TestCase
{
    private string $imagePath = __DIR__ . '/resources/vertical.png';
    private string $destinationFolder = __DIR__ . '/resources/vertical_parts/';
    private int $imageWidth = 3; //px
    private int $imageHeight = 9; //px

    public function testSliceWithoutUpscale(): void
    {
        $expectedSlices = 3;

        $slicer = new VerticalImageSlice(6, 3);
        $parts = $slicer->slice($this->imagePath, $this->destinationFolder);

        $this->assertCount($expectedSlices, $parts);

        $this->cleanUp($parts);
    }

    public function testSliceWithUpscale(): void
    {
        $expectedSlices = 6;

        $slicer = new VerticalImageSlice(6, 3);
        $slicer->allowUpscale();
        $parts = $slicer->slice($this->imagePath, $this->destinationFolder);

        $this->assertCount($expectedSlices, $parts);

        $this->cleanUp($parts);
    }

    public function testSliceWithoutLastSlide(): void
    {
        $expectedSlices = 2;

        $slicer = new VerticalImageSlice(3, 4);
        $parts = $slicer->slice($this->imagePath, $this->destinationFolder);

        $this->assertCount($expectedSlices, $parts);

        $this->cleanUp($parts);
    }

    public function testSliceWithLastSlide(): void
    {
        $expectedSlices = 3;

        $slicer = new VerticalImageSlice(3, 4);
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
