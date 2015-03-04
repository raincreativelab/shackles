<?php
/**
 * Resize
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
 * Resize
 * Resizes the image base on a particular size
 *
 * Settings:
 *
 * width:      Maximum width of the image being resized
 * max-height: Set the maximum height of the image being resized. (optional)
 *
 * PHP version 5.5+
 *
 * @category Shackles_Util
 * @package  Shackles\Util
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
class Resize
    extends Rule
{

    /**
     * Resizes the image base on the width provided.
     * This resize maintain's the image aspect ratio.
     *
     * @param Image $image Image to process
     *
     * @return Image
     */
    protected function processImage(Image $image)
    {
        $this->_throwExceptionIfNoWidthIsSet();

        $width = $this->get("width");
        $maxHeight = ($this->has("max-height")) ? $this->get("max-height") : 0;

        $imageResource = $image->createImageResource();
        list($oldWidth, $oldHeight) = getimagesize($image->getFullPath());

        //////////////////////////////////////////////////////////////////////
        // For Aspect ratio

        $newHeight = 0;

        if ($maxHeight == 0) {
            // if the height is not specified calculate the relative height
            $newHeight = $width * $oldHeight / $oldWidth;
        }

        //////////////////////////////////////////////////////////////////////

        if ($maxHeight > 0) {

            $photoIsPortrait = ($oldHeight > $oldWidth);

            if ($photoIsPortrait) {

                $width = $maxHeight * $oldWidth / $oldHeight;
                $newHeight = $maxHeight;

            } else {

                // This is a landscape photo
                $newHeight = $width * ($oldHeight / $oldWidth);

            }
        }

        $newImageResource = imagecreatetruecolor($width, $newHeight);

        imagecopyresampled(
            $newImageResource, $imageResource,
            0, 0, 0, 0,
            $width, $newHeight,
            $oldWidth, $oldHeight
        );

        $image->createImage($newImageResource);
    }

    /**
     * Throws an exception if width is not set
     *
     * @throws \Exception
     * @return void
     */
    private function _throwExceptionIfNoWidthIsSet()
    {
        if (!$this->has("width")) {
            throw new \Exception("Resize rule needs a width to work");
        }
    }


} 