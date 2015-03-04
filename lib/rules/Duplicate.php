<?php
/**
 * Duplicate
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
use shackles\util\ImageFileFormatter;


/**
 * Duplicate
 * Duplicates the image.
 * When a duplicate rule is applied it uses the new image,
 * preserving the original file
 *
 * PHP version 5.5+
 *
 * @category Shackles_Rules
 * @package  Shackles\Rules
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
class Duplicate
    extends Rule
{

    /**
     * Duplicate the original image
     *
     * @param Image $image Image to process
     *
     * @return Image
     */
    protected function processImage(Image $image)
    {
        $ff = new ImageFileFormatter($image);
        $duplicatedImage = $ff->generate("{NAME}-{####}.dup");

        $this->getFileSystem()->copy(
            $image->getImageName(),
            $duplicatedImage
        );

        // It will return a duplicated image instead
        // of the original file
        $image->setImageName($duplicatedImage);

        return $image;

    }

} 