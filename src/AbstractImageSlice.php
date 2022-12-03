<?php

namespace Antey\ImageSlice;

use Antey\ImageSlice\Adapter\ImageResizeAdapter;
use Antey\ImageSlice\Exception\ImageSliceException;
use Antey\ImageSlice\Storage\PathCalculator;
use Exception;
use Gumlet\ImageResizeException;

abstract class AbstractImageSlice
{
    protected int $width;
    protected int $height = 0;
    protected ImageResizeAdapter $imageResizeAdapter;
    private string $name = '';
    private string $folder = '';

    protected bool $upscale = false;
    private bool $lastSlide = false;

    private string $tmpFile = '';
    private array $createdFiles = [];
    private bool $isOwnFolder = false;

    /**
     * @param int $width
     * @param int $height
     * @throws ImageSliceException
     */
    public function __construct(int $width, int $height)
    {
        if ($width < 1 || $height < 1) {
            throw new ImageSliceException(
                'Image resolution must be at least than 1x1, '
                . $width . 'x' . $height
                . ' given.'
            );
        }

        $this->width = $width;
        $this->height = $height;

        $this->imageResizeAdapter = new ImageResizeAdapter();
    }

    /**
     * @param bool $allow
     * @return void
     *
     * Allow image up-scaling if main dimension is
     * lower than you wish.
     */
    public function allowUpscale(bool $allow = true): void
    {
        $this->upscale = $allow;
    }

    /**
     * @param bool $allow
     * @return void
     *
     * Allow creating last slide, which can have smaller dimensions.
     * By default, disabled.
     */
    public function allowLastSlide(bool $allow = true): void
    {
        $this->lastSlide = $allow;
    }

    /**
     * @param string $path <p>
     * this parameter specify absolute path to file you wish to slice into a pieces.
     * </p>
     * @param string $directory <p>
     * this parameter specify folder, where parts will be stored. If empty - will be
     * stored in directory of original file.
     * </p>
     * @param string $name <p>
     * this parameter specify name of result image parts. If empty - will be
     * used name of source file.
     * </p>
     * @return string[] paths of sliced pieces.
     * @throws Exception
     * <br>
     * Will resize original file and save as single or multiple files in specified path.<br/>
     * By default, sliced files for /path/to/example.jpeg will be stored as:
     * <ul>
     *  <li>/path/to/example-1.jpeg</li>
     *  <li>/path/to/example-2.jpeg</li>
     *  <li>/path/to/example-3.jpeg</li>
     * </ul>
     */
    public function slice(string $path, string $directory = '', string $name = ''): array
    {
        $this->setImage($path, $name, $directory);
        $this->resize();

        try {
            $parts = $this->split();
        } catch (Exception $e) {
            $this->cleanTmpFile();
            $this->cleanPartFiles();
            $this->removeDirectory();
            throw $e;
        }
        $this->cleanTmpFile();
        $this->createdFiles = [];

        return $parts;
    }

    /**
     * @param string $path
     * @param string $name
     * @param string $folder
     * @return void
     * @throws ImageResizeException
     *
     * Prepare current image
     */
    private function setImage(string $path, string $name, string $folder): void
    {
        $this->imageResizeAdapter = $this->imageResizeAdapter->load($path);
        $this->name = PathCalculator::getFileName($path, $name);
        $this->folder = $this->getFolder($path, $folder);
        $this->tmpFile = $path . '.resized.tmp';
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function resize(): void
    {
        $this->setImageSize();
        $this->imageResizeAdapter->save($this->tmpFile);
    }

    /**
     * @return void
     * @throws Exception
     */
    protected abstract function setImageSize(): void;

    /**
     * @return array
     * @throws ImageResizeException
     * @throws ImageSliceException
     * Slice resized image into a pieces and return paths.
     */
    private function split(): array
    {
        $pieces = [];

        $countSlides = $this->getSlidesCount();
        if ($countSlides == 0) {
            throw new ImageSliceException('Cannot slice given image to pieces. Resolution to low.');
        }

        $margin = $this->getStartMargin($countSlides);

        for ($i = 0; $i < $countSlides; $i ++) {
            $pieces[] = $this->createSlice(
                $margin + $i * $this->getSliceSize(),
                $this->folder . '/' . PathCalculator::getSliceName($this->name, $i),
                $this->imageResizeAdapter->load($this->tmpFile)
            );
        }

        return $pieces;
    }

    /**
     * @return int width or height of resized image
     */
    abstract protected function getImageSize(): int;

    /**
     * @return int width or height of one slice
     */
    abstract protected function getSliceSize(): int;

    /**
     * @return int number of pieces, what can be extracted from image.
     */
    private function getSlidesCount(): int {
        if ($this->lastSlide) {
            return ceil($this->getImageSize() / $this->getSliceSize());
        } else {
            return floor($this->getImageSize() / $this->getSliceSize());
        }
    }

    /**
     * @param int $slidesCount
     * @return int margin from left or top border of image
     * for first image slice.
     */
    private function getStartMargin(int $slidesCount): int
    {
        if ($this->lastSlide) {
            return 0;
        }

        return floor(($this->getImageSize() - $slidesCount * $this->getSliceSize()) / 2);
    }

    /**
     * @param int $margin
     * @param string $destinationPath
     * @param ImageResizeAdapter $imageResizeAdapter
     * @return string
     * @throws ImageResizeException
     * Create one image part and return path.
     */
    private function createSlice(
        int $margin,
        string $destinationPath,
        ImageResizeAdapter $imageResizeAdapter
    ): string
    {
        $imageResizeAdapter->freecrop(
            min($this->width, $this->imageResizeAdapter->getWidth() - $this->getSliceLeftMargin($margin)),
            min($this->height, $this->imageResizeAdapter->getHeight() - $this->getSliceTopMargin($margin)),
            $this->getSliceLeftMargin($margin),
            $this->getSliceTopMargin($margin)
        );
        $imageResizeAdapter->save($destinationPath);

        return $destinationPath;
    }

    abstract protected function getSliceLeftMargin(int $startShift): int;

    abstract protected function getSliceTopMargin(int $startShift): int;

    /**
     * @param string $path
     * @param string $folder
     * @return string
     *
     * Return folder for image parts.
     */
    private function getFolder(string $path, string $folder): string
    {
        $folder = PathCalculator::getDestinationFolder($path, $folder);

        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
            $this->isOwnFolder = true;
        } else {
            $this->isOwnFolder = false;
        }

        return $folder;
    }

    private function cleanPartFiles(): void
    {
        foreach ($this->createdFiles as $createdFile) {
            if (file_exists($createdFile)) {
                unlink($createdFile);
            }
        }
    }

    private function cleanTmpFile(): void
    {
        if (file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
    }

    private function removeDirectory(): void
    {
        if (
            $this->isOwnFolder
            && is_dir($this->folder)
        ) {
            rmdir($this->folder);
        }
    }
}
