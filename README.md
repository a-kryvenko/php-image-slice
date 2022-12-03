# php-image-slice

PHP library to slice image into a pieces.

------

## Setup

Download this repository and use in you own project.

## Requirements

- PHP version: >= 7.4;
- PHP extensions: gd.

## Dependencies

This package using [php-image-resize](https://github.com/gumlet/php-image-resize)

## Usage

There is two available slice types - slice horizontal and slice vertical.
As constructor parameters for `HorizontalImageSlice` and `VerticalImageSlice`
take width and height (in pixels) pieces, for what you
wish to slice image.

### Horizontal slicing

```php
use Antey\ImageSlice\HorizontalImageSlice;

$imageSlice = new HorizontalImageSlice(100, 100);
$slices = $imageSlice->slice(__DIR__ . '/example.jpeg');
```

As result of this code, our source image
will be sliced into several horizontal pieces
and paths of this pieces will be returned.

### Vertical slicing

```php
use Antey\ImageSlice\VerticalImageSlice;

$imageSlice = new VerticalImageSlice(100, 100);
$slices = $imageSlice->slice(__DIR__ . '/example.jpeg');
```
As result of this code, our source image
will be sliced into several vertical pieces
and paths of this pieces will be returned.

### Upscale

By default, in case when source image has lower resolution,
than expected (for example, original image is 2000x500, and
we want to get 1000x1000 pieces), result pieces will have
height (or width, for `VerticalImageSlice`) same as source image,
so we will get two 1000x500 slices.

If we wish to have strict size of pieces, we can allow upscale

```php
$imageSlice->allowUpscale();
```

After that, in case of previous image, source image will be
up-scaled to 4000x1000, and only after that sliced. As result,
we will get four 1000x1000 slices.

### Last slice

In most scenarios, original image cannot be sliced
into pieces exactly. When we try to slice 2300x1000 image
into 1000x1000 pieces, last part (300px) will be ignored.

But if we want to save this part also - then we need allow
saving last piece:

```php
$imageSlice->allowLastSlide();
```

As result, we will get three slides - two slides 1000x1000,
and one slide 300x1000.
