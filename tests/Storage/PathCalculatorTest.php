<?php

namespace Antey\ImageSlice\Storage;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Antey\ImageSlice\Storage\PathCalculator
 */
class PathCalculatorTest extends TestCase
{
    /**
     * @param string $filePath
     * @param string $directory
     * @param string $expectedFolder
     * @return void
     * @dataProvider destinationFolderProvider
     */
    public function testGetDestinationFolder(string $filePath, string $directory, string $expectedFolder): void
    {
        $this->assertEquals(
            $expectedFolder,
            PathCalculator::getDestinationFolder($filePath, $directory)
        );
    }

    /**
     * @param string $filePath
     * @param string $name
     * @param string $expectedName
     * @return void
     * @dataProvider fileNameProvider
     */
    public function testGetFileName(string $filePath, string $name, string $expectedName): void
    {
        $this->assertEquals(
            $expectedName,
            PathCalculator::getFileName($filePath, $name)
        );
    }

    /**
     * @param string $name
     * @param int $number
     * @param string $expectedName
     * @return void
     * @dataProvider sliceNameProvider
     */
    public function testGetSliceName(string $name, int $number, string $expectedName): void
    {
        $this->assertEquals(
            $expectedName,
            PathCalculator::getSliceName($name, $number)
        );
    }

    public function destinationFolderProvider(): array
    {
        return [
            ['/path/to/file.jpeg', '/path/to/components', '/path/to/components'],
            ['/path/to/file.jpeg', '/path/to/components/', '/path/to/components'],
            ['/path/to/file.jpeg', '', '/path/to'],
            ['file.jpeg', '', ''],
        ];
    }

    public function fileNameProvider(): array
    {
        return [
            ['/path/to/file.jpeg', 'part.jpeg', 'part.jpeg'],
            ['/path/to/file.jpeg', 'parts/part.jpeg', 'parts/part.jpeg'],
            ['/path/to/file.jpeg', '/part.jpeg', 'part.jpeg'],
            ['/path/to/file.jpeg', '', 'file.jpeg'],
            ['file.jpeg', '', 'file.jpeg'],
        ];
    }

    public function sliceNameProvider(): array
    {
        return [
            ['part', 0, 'part-1'],
            ['part.jpeg', 0, 'part-1.jpeg'],
            ['parts/part.jpeg', 0, 'parts/part-1.jpeg'],
        ];
    }
}
