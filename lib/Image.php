<?php
/**
 * Image
 *
 * PHP version 5.5+
 *
 * @category Shackles
 * @package  Shackles
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
namespace shackles;


/**
 * Image
 * Method signature for the image data model
 *
 * PHP version 5.5+
 *
 * @category Shackles
 * @package  Shackles
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
interface Image
{

    /*------------------------------------
     * IMAGE MIME TYPES
     *------------------------------------*/

    const JPG = "image/jpeg";

    const GIF = "image/gif";

    const PNG = "image/png";


    /**
     * Creates a new image resource base on the extension provided
     * Throws an exception if no mime type match.
     *
     * @throws \Exception
     * @return null|resource
     */
    public function createImageResource();

    /**
     * Creates and writes final image
     *
     * @param mixed $imageResource Image Resource
     *
     * @return void
     */
    public function createImage($imageResource);

    /**
     * Sets the image name
     *
     * @param string $file Image File
     *
     * @return void
     */
    public function setImageName($file);

    /**
     * Return the physical path of the image
     *
     * @return string
     */
    public function getImageName();


    /**
     * Sets the image source name
     *
     * @param string $sourceName Source name
     *
     * @return void
     */
    public function setSourceName($sourceName);

    /**
     * Return the name of the source image
     *
     * @return string
     */
    public function getSourceName();

    /**
     * Return the image file extension
     *
     * @return string
     */
    public function getExtension();

    /**
     * Sets the image file extension
     *
     * @param string $extension File extension
     *
     * @return void
     */
    public function setExtension($extension);

    /**
     * Returns the full image path (image name and path)
     *
     * @return string
     */
    public function getFullPath();

    /**
     * Sets the image full path
     *
     * @param string $fullPath Image full path
     *
     * @return void
     */
    public function setFullPath($fullPath);

    /**
     * Return the current JPG output quality
     *
     * @return int
     */
    public function getJpgQuality();

    /**
     * Sets the current JPG output quality (values range from 1 - 100)
     *
     * @param int $jpgQuality JPG output quality
     *
     * @return void
     */
    public function setJpgQuality($jpgQuality);

    /**
     * Return the image mime
     *
     * @return string
     */
    public function getMime();

    /**
     * Sets the image mime
     *
     * @param string $mime Mime value
     *
     * @return void
     */
    public function setMime($mime);
}