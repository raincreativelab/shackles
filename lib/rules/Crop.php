<?php
/**
 * Crop
 *
 * PHP version 5.5+
 *
 * @category Shackles_Util
 * @package  Shackles\Util
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
namespace shackles\rules;

use shackles\Image;
use shackles\Rule;


/**
 * Crop
 * Crops the image base on the crop box settings
 *
 * Settings:
 *
 * width:           Cropped width
 * height:          Cropped height
 * crop-box-width:  Width of the specific area of an image.
 *                  Height is automatically computed, aspect ratio is maintained.
 *                  (optional)
 * x:               X coordinates of the crop box (optional)
 * y:               Y coordinates of the crop box (optional)
 *
 * If x and y are not provided, it will automatically center the crop box
 *
 * PHP version 5.5+
 *
 * @category Shackles_Util
 * @package  Shackles\Util
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
class Crop
    extends Rule
{
    /**
     * For auto cropping purposes,
     * it provides the image coverage
     */
    const IMAGE_COVERAGE = .95;

    /**
     * Image object
     *
     * @var Image
     */
    private $_image;

    /**
     * Ratio multiplier.
     *
     * @var int
     */
    private $_ratioMultiplier;

    /**
     * Image orientation (landscape or portrait)
     *
     * @var string
     */
    private $_orientation;

    /**
     * Width of the source image
     *
     * @var int
     */
    private $_sourceImageWidth;

    /**
     * Height of the source image
     *
     * @var int
     */
    private $_sourceImageHeight;


    /**
     * Crop the image
     *
     * @param Image $image Image to process
     *
     * @return void
     */
    protected function processImage(Image $image)
    {
        $this->_image = $image;

        $finalCropWidth = $this->get("width");
        $finalCropHeight = $this->get("height");

        $this->_ratioMultiplier = $this->_getRatioMultiplier(
            $finalCropWidth,
            $finalCropHeight
        );

        // Get orientation using the ratio multiplier
        // compute a dummy width and height
        $this->_orientation = $this->_getOrientation();

        list($imageWidth, $imageHeight) = $this->_getImageDimension();

        $this->_sourceImageWidth = $imageWidth;
        $this->_sourceImageHeight = $imageHeight;

        ////////////////////////////////////////////////////////
        // Auto Cropping
        // If there are no width and height details provided
        // Do auto cropping using the dimensions of the image
        list($width, $height, $x, $y) = $this->_autoCropSourceImage();

        ////////////////////////////////////////////////////////
        // If cropped dimensions are provided
        if ($this->has("crop-box-width")) {
            list($width, $height, $x, $y) = $this->_cropUsingBox();
        }
        //////////////////////////////////////////////////////////////////

        $sourceImage = $this->_image->createImageResource();
        $croppedImage = imagecreatetruecolor($finalCropWidth, $finalCropHeight);

        imagecopyresampled(
            $croppedImage, $sourceImage,
            0, 0, $x, $y,
            $finalCropWidth, $finalCropHeight,
            $width, $height
        );

        $this->_image->createImage($croppedImage);
    }

    /**
     * Get the physical dimension of the image
     *
     * @return array
     */
    private function _getImageDimension()
    {
        $imagePath = $this->_image->getFullPath();

        // Use the original width and height of the image
        return getimagesize($imagePath);
    }

    /**
     * Return the orientation of the image being cropped
     * using the ratio multiplier value
     *
     * Returns either landscape or portrait
     *
     * @return string
     */
    private function _getOrientation()
    {
        // Test the ratio multiplier to get whether
        // the crop box is a portrait or landscape
        $width = 100;
        $height = $width * $this->_ratioMultiplier;

        // Check if its landscape or portrait
        // If its both equal and height is greater that width
        $orientation = "portrait";

        // If width is greater than height
        if ($width > $height) {
            $orientation = "landscape";
        }

        return $orientation;
    }

    /**
     * Get the ratio multiplier value
     *
     * @param int $width  Final crop width
     * @param int $height Final crop height
     *
     * @return float
     */
    private function _getRatioMultiplier($width, $height)
    {
        $this->_throwExceptionForInvalidDimension($width, $height);

        return $height / $width;
    }

    /**
     * Returns the cropped dimension of the source image
     * base on its current image dimension and orientation
     *
     * @return array
     */
    private function _getCroppedDimensionOfSourceImage()
    {
        $imageWidth = $this->_sourceImageWidth;
        $imageHeight = $this->_sourceImageHeight;
        $orientation = $this->_orientation;
        $ratioMultiplier = $this->_ratioMultiplier;

        // Default to portrait orientation
        // Make sure the height doesn't go beyond the image height
        list($height, $width) = $this->_adjustHeight(
            ceil($imageWidth * self::IMAGE_COVERAGE),       // Width
            ceil($imageWidth * $ratioMultiplier),           // Height
            $imageHeight
        );

        // Always get the 80 percent area of the image
        if ($orientation === "landscape") {

            // Make sure that the width doesn't go beyond the image width
            list($width, $height) = $this->_adjustWidth(
                ceil($imageHeight / $ratioMultiplier),      // Width
                ceil($imageHeight * self::IMAGE_COVERAGE),  // Height
                $imageWidth
            );
        }

        return array($width, $height);
    }

    /**
     * Returns the coordinates of a centered crop box
     *
     * @param int $width  Image width
     * @param int $height Image height
     *
     * @return array
     */
    private function _getCropBoxCoordinates($width, $height)
    {
        // Identify the X Y coordinates base on the derived width
        // and the current image width and height
        // Set the crop position at the center of the image
        return [
            ($this->_sourceImageWidth / 2) - ($width / 2),
            ($this->_sourceImageHeight / 2) - ($height / 2)
        ];
    }

    /**
     * Readjusts the height if the cropped value exceeds the source height
     *
     * @param int $width        Image width
     * @param int $height       Image height
     * @param int $sourceHeight Source image height
     *
     * @return array
     */
    private function _adjustHeight(
        $width,
        $height,
        $sourceHeight
    ) {
        // If the derived height is greater than the image height
        // readjust to the current maximum height of the image
        if ($height > $sourceHeight) {
            $height = ceil($sourceHeight * self::IMAGE_COVERAGE);
            $width = ceil($height / $this->_ratioMultiplier);
        }

        return array($height, $width);
    }

    /**
     * Readjusts the width if the cropped value exceeds the
     * source width.
     *
     * @param int $width       Image width
     * @param int $height      Image height
     * @param int $sourceWidth Image source width
     *
     * @return array
     */
    private function _adjustWidth($width, $height, $sourceWidth)
    {
        // If the derived width is greater than the
        // current image width, readjust base on the current
        // maximum width of the image
        if ($width > $sourceWidth) {
            $width = ceil($sourceWidth * self::IMAGE_COVERAGE);
            $height = ceil($width * $this->_ratioMultiplier);

            return array($width, $height);
        }

        return array($width, $height);
    }

    /**
     * Auto crop the source image
     *
     * @return array
     */
    private function _autoCropSourceImage()
    {
        list($width, $height) = $this->_getCroppedDimensionOfSourceImage();
        list($x, $y) = $this->_getCropBoxCoordinates($width, $height);

        return array($width, $height, $x, $y);
    }

    /**
     * Crop using the cropping box
     * It will check if crop-box-width settings is set
     *
     * If x and y corrdinates are provided, it will use that details,
     * else, it will center the box
     *
     * @return array
     */
    private function _cropUsingBox()
    {
        $width = $this->get("crop-box-width");
        $height = $width * $this->_ratioMultiplier;

        if ($this->has("x") === false || $this->has("y") === false) {
            list($x, $y) = $this->_getCropBoxCoordinates($width, $height);
        } else {
            $x = $this->get("x");
            $y = $this->get("y");
        }

        return array($width, $height, $x, $y);
    }

    /**
     * Throw exception for invalid dimensions
     *
     * @param int $width  Final crop width
     * @param int $height Final crop height
     *
     * @throws \Exception
     * @return void
     */
    private function _throwExceptionForInvalidDimension($width, $height)
    {
        if ($height <= 0 || $width <= 0) {
            throw new \Exception("Invalid crop dimensions");
        }
    }

}