<?php
/**
 * GrayScale
 *
 * PHP version 5.5+
 *
 * @category Shackles_Rules
 * @package  Shackles\Rules
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
namespace shackles\rules;

use shackles\Image;
use shackles\Rule;


/**
 * GrayScale
 * Converts any colored image to its grayscale value
 *
 * PHP version 5.5+
 *
 * @category Shackles_Rules
 * @package  Shackles\Rules
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
class Grayscale
    extends Rule
{

    /**
     * Converts image to grayscale
     *
     * @param Image $image Image to process
     *
     * @return void
     */
    protected function processImage(Image $image)
    {
        $imageResource = $image->createImageResource();
        list($width, $height) = getimagesize($image->getFullPath());

        for ($i = 0; $i < $width; $i++) {
            for ($j = 0; $j < $height; $j++) {
                // get the rgb value for current pixel

                $rgb = imagecolorat($imageResource, $i, $j);

                // extract each value for r, g, b

                $rr = ($rgb >> 16) & 0xFF;
                $gg = ($rgb >> 8) & 0xFF;
                $bb = $rgb & 0xFF;

                // get the Value from the RGB value

                $g = round(($rr + $gg + $bb) / 3);

                // grayscale values have r=g=b=g

                $val = imagecolorallocate($imageResource, $g, $g, $g);

                // set the gray value

                imagesetpixel($imageResource, $i, $j, $val);
            }
        }

        $image->createImage($imageResource);
    }


} 