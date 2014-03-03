<?php

namespace AmericanReading\Geometry;

use UnexpectedValueException;

class Size
{
    public $width;
    public $height;

    public function __construct($width = 0, $height = 0)
    {
        if (!is_numeric($width) || !is_numeric($height)) {
            throw new UnexpectedValueException("Numeric values expected for width and height.");
        }
        $this->width = $width;
        $this->height = $height;
    }

    public static function initWithString($size)
    {
        $pattern = "/(?P<width>\d+)\D*(?P<height>\d+)/";
        if (preg_match($pattern, $size, $matches)) {
            return self::initWithArray($matches);
        }
        throw new UnexpectedValueException("Unable to parse string dimensions.");
    }

    public static function initWithArray($arr)
    {
        if (!isset($arr['width'], $arr['height'])) {
            throw new UnexpectedValueException("Array must conatin \"width\" and \"height\" members.");
        }
        return new self($arr['width'], $arr['height']);
    }

    public function __toString()
    {
        return "" . $this->width . 'x' . $this->height;
    }

    /**
     * Return a Size instance with the same proportions that fits to the passed width and height.
     *
     * Pass 0 as the width or heigth to leave that dimension free to expand to any size needed. If both width and
     * height are non-zero, the resulting size's width and height will be less than or equal to the passed.
     *
     * @param int|float $fitWidth
     * @param int|float $fitHeight
     * @return Size
     */
    public function scaleToFit($fitWidth = 0, $fitHeight = 0)
    {
        $originalRatio = $this->width / $this->height;

        if ($fitWidth == 0 && $fitHeight == 0) {

            // No need to resize.
            return new self($this->width, $this->height);

        } elseif ($fitWidth == 0 && $fitHeight != 0) {

            // Resize to a set height.
            $newWidth = $fitHeight * $originalRatio;
            return new self($newWidth, $fitHeight);

        } elseif ($fitWidth != 0 && $fitHeight == 0) {

            // Resize to a set width.
            $newHeight = $fitWidth / $originalRatio;
            return new self($fitWidth, $newHeight);

        } else {

            // Resize, contraining to the fit dimensions.
            $fitRatio = $fitWidth / $fitHeight;

            if ($fitRatio >= $originalRatio) {

                // Scale to fit the height;
                $newWidth = $fitHeight * $originalRatio;
                return new self($newWidth, $fitHeight);

            } else {

                // Scale to fit to the width;
                $newHeight = $fitWidth / $originalRatio;
                return new self($fitWidth, $newHeight);

            }
        }
    }

    /** Round the width and height members. */
    public function round($precision = 0, $mode = PHP_ROUND_HALF_UP)
    {
        $this->width = round($this->width, $precision, $mode);
        $this->height = round($this->height, $precision, $mode);
    }
}
