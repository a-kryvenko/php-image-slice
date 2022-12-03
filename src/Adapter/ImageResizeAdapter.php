<?php

namespace Antey\ImageSlice\Adapter;

use Gumlet\ImageResize;
use Gumlet\ImageResizeException;

class ImageResizeAdapter
{
    private ?ImageResize $imageResize = null;

    /**
     * @param string $path
     * @param ImageResize|null $imageResize
     * @throws ImageResizeException
     */
    public function __construct(string $path = '', ImageResize $imageResize = null)
    {
        if (!is_null($imageResize)) {
            $this->imageResize = $imageResize;
        } else if (!empty($path)) {
            $this->imageResize = new ImageResize($path);
        }
    }

    /**
     * @param string $path
     * @param ImageResize|null $imageResize
     * @return ImageResizeAdapter
     * @throws ImageResizeException
     */
    public function load(string $path, ImageResize $imageResize = null): ImageResizeAdapter
    {
        return new self($path, $imageResize);
    }

    /**
     * @param string $path
     * @param $imageType
     * @return void
     * @throws ImageResizeException
     */
    public function save(string $path, $imageType = null): void
    {
        $this->imageResize->save($path, $imageType);
    }

    public function freecrop(int $width, int $height, int $x = 0, int $y = 0): void
    {
        $this->imageResize->freecrop($width, $height, $x, $y);
    }

    public function getWidth(): int
    {
        return $this->imageResize->getDestWidth();
    }

    public function getHeight(): int
    {
        return $this->imageResize->getDestHeight();
    }

    public function resizeToHeight(int $height, bool $allowUpscale = false): void
    {
        $this->imageResize->resizeToHeight($height, $allowUpscale);
    }

    public function resizeToWidth(int $width, bool $allowUpscale = false): void
    {
        $this->imageResize->resizeToWidth($width, $allowUpscale);
    }
}
