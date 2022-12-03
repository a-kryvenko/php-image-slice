<?php

namespace Antey\ImageSlice;

use PHPUnit\Framework\TestCase;

/**
 * @codeCoverageIgnore
 */
abstract class ImageSliceTestAbstract extends TestCase
{
    abstract protected function getImagePath(): string;
    abstract protected function getDestinationFolder(): string;

    protected function testImageSlice(
        AbstractImageSlice $imageSlice,
        array $expectedSlices
    ): void
    {
        $expectedSlicesCount = count($expectedSlices);

        $slices = $imageSlice->slice($this->getImagePath(), $this->getDestinationFolder());
        $slicesCount = count($slices);

        $this->assertEquals(
            $expectedSlicesCount,
            $slicesCount,
            sprintf('Wrong slices count. Expected %d slices, sliced %d.', $expectedSlicesCount, $slicesCount)
        );
        foreach ($slices as $k => $slice) {
            $sizes = getimagesize($slice);
            $expectedResolution = $this->getResolutionString($expectedSlices[$k][0], $expectedSlices[$k][1]);
            $resolution = $this->getResolutionString($sizes[0], $sizes[1]);
            $this->assertEquals(
                $expectedResolution,
                $resolution,
                sprintf(
                    'Wrong slice resolution. Expected %s, sliced %s.',
                    $expectedResolution,
                    $resolution
                )
            );
        }

        $this->cleanUp($slices);
    }

    private function cleanUp(array $paths): void
    {
        foreach ($paths as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }

        if (file_exists($this->getDestinationFolder())) {
            rmdir($this->getDestinationFolder());
        }
    }

    private function getResolutionString(int $width, int $height): string
    {
        return $width . 'x' . $height;
    }
}
