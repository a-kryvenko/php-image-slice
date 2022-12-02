<?php

namespace Antey\ImageSlice;

class HorizontalImageSlice extends AbstractImageSlice
{
    /**
     * @inheritDoc
     */
    protected function resize(): void
    {
        $this->imageResizeAdapter->resizeToHeight($this->height, $this->upscale);
    }

    /**
     * @return int width of resized image
     */
    protected function getImageSize(): int
    {
        return $this->imageResizeAdapter->getWidth();
    }

    /**
     * @return int width of one slice
     */
    protected function getSliceSize(): int
    {
        return $this->width;
    }

    /**
     * @param int $startShift
     * @return int margin from left border of image
     * for second and next slides
     */
    protected function getSliceLeftMargin(int $startShift): int
    {
        return $startShift;
    }

    /**
     * @param int $startShift
     * @return int margin from top border of image.
     * Always equal "0".
     */
    protected function getSliceTopMargin(int $startShift): int
    {
        return 0;
    }
}
