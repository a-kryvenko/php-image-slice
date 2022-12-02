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
    private array $createdFiles = [];

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
     * this parameter specify the file you wish to slice into a pieces
     * </p>
     * @param string $directory <p>
     * this parameter specify folder, where parts will be stored. If empty - will be
     * stored in original file directory.
     * </p>
     * @param string $name <p>
     * this parameter specify name of result image parts. If empty - will be
     * used original filename.
     * </p>
     * @return string[] paths of sliced peaces
     * @throws Exception
     *
     * Resize original file and save as single or multiple files in specified path.<br/>
     * By default sliced files for /path/to/example.jpeg will be stored as:
     * <ul>
     *  <li>/path/to/example-1.jpeg</li>
     *  <li>/path/to/example-2.jpeg</li>
     *  <li>/path/to/example-3.jpeg</li>
     * </ul>
     */
    public function slice(string $path, string $directory = '', string $name = ''): array
    {
        $this->setImage($path, $name, $directory);

        $resizedImagePath = $path . '.resized.tmp';
        $this->resize();
        $this->imageResizeAdapter->save($resizedImagePath);

        try {
            $parts = $this->split($resizedImagePath);
        } catch (Exception $e) {
            if (file_exists($resizedImagePath)) {
                unlink($resizedImagePath);
            }
            foreach ($this->createdFiles as $createdFile) {
                if (file_exists($createdFile)) {
                    unlink($createdFile);
                }
            }
            throw $e;
        }
        if (file_exists($resizedImagePath)) {
            unlink($resizedImagePath);
        }
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
    }

    /**
     * @return void
     * Resize image to resolution you wish.
     */
    abstract protected function resize(): void;

    /**
     * @param string $resizedImagePath
     * @return array
     * @throws ImageResizeException
     * @throws ImageSliceException
     * Slice resized image into a pieces and return paths.
     */
    private function split(string $resizedImagePath): array
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
                $this->imageResizeAdapter->load($resizedImagePath)
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

        return floor(($this->imageResizeAdapter->getWidth() - $slidesCount * $this->width) / 2);
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
            mkdir($folder);
        }

        return $folder;
    }
}
