<?php

namespace Antey\ImageSlice;

class VerticalImageSlice extends AbstractImageSlice
{
    /**
     * @inheritDoc
     */
    protected function setImageSize(): void
    {
        $this->imageResizeAdapter->resizeToWidth($this->width, $this->upscale);
    }

    /**
     * @return int height of resized image
     */
    protected function getImageSize(): int
    {
        return $this->imageResizeAdapter->getHeight();
    }

    /**
     * @return int height of one slice
     */
    protected function getSliceSize(): int
    {
        return $this->height;
    }

    /**
     * @param int $startShift
     * @return int margin from left border of image
     * for second and next slides.
     * Always equal "0".
     */
    protected function getSliceLeftMargin(int $startShift): int
    {
        return 0;
    }

    /**
     * @param int $startShift
     * @return int margin from top border of image.
     */
    protected function getSliceTopMargin(int $startShift): int
    {
        return $startShift;
    }
}
