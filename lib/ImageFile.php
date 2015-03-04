<?php
/**
 * ImageFile
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
 * ImageFile
 * Image object wrapper.
 *
 * PHP version 5.5+
 *
 * @category Shackles
 * @package  Shackles
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
class ImageFile
    implements Image
{

    /*------------------------------------
     * PRIVATE PROPERTIES
     *------------------------------------*/

    /**
     * The image full path
     *
     * @var string
     */
    private $_fullPath;

    /**
     * Extension of the image
     *
     * @var string
     */
    private $_extension;

    /**
     * Image mime type
     *
     * @var string
     */
    private $_mime;

    /**
     * Name of the image
     *
     * @var string
     */
    private $_file;

    /**
     * Original source name
     *
     * @var string
     */
    private $_sourceName;

    /**
     * Default JPG output quality
     *
     * @var int
     */
    private $_jpgQuality = 60;

    /**
     * Create a new Image
     *
     * @param string $file Name of the image
     */
    function __construct($file)
    {
        $this->_file = $file;
    }

    /**
     * Creates a new image resource base on the extension provided
     * Throws an exception if no mime type match.
     *
     * @throws \Exception
     * @return null|resource
     */
    public function createImageResource()
    {
        $imagePath = $this->getFullPath();
        $imageResource = null;

        switch ($this->getMime()) {
        case Image::JPG :
            $imageResource = imagecreatefromjpeg($imagePath);
            break;
        case Image::GIF :
            $imageResource = imagecreatefromgif($imagePath);
            break;
        case Image::PNG :
            $imageResource = imagecreatefrompng($imagePath);
            break;
        }

        if (!$imageResource) {
            throw new \Exception("Unidentified Image File");
        }

        return $imageResource;
    }

    /**
     * Creates and writes final image
     *
     * @param mixed $imageResource Image Resource
     *
     * @return void
     */
    public function createImage($imageResource)
    {
        $quality = $this->getJpgQuality();
        $imagePath = $this->getFullPath();

        switch ($this->getMime()) {
        case Image::JPG :
            imagejpeg($imageResource, $imagePath, $quality);
            break;
        case Image::GIF :
            imagegif($imageResource, $imagePath);
            break;
        case Image::PNG :
            imagepng($imageResource, $imagePath);
            break;
        }
    }

    /**
     * Sets the image name
     *
     * @param string $file Image File
     *
     * @return void
     */
    public function setImageName($file)
    {
        $this->_file = $file;
    }

    /**
     * Return the physical path of the image
     *
     * @return string
     */
    public function getImageName()
    {
        return $this->_file;
    }

    /**
     * Sets the image source name
     *
     * @param string $sourceName Source name
     *
     * @return void
     */
    public function setSourceName($sourceName)
    {
        $this->_sourceName = $sourceName;
    }

    /**
     * Return the name of the source image
     *
     * @return string
     */
    public function getSourceName()
    {
        return $this->_sourceName;
    }

    /**
     * Return the image file extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->_extension;
    }

    /**
     * Sets the image file extension
     *
     * @param string $extension File extension
     *
     * @return void
     */
    public function setExtension($extension)
    {
        $this->_extension = $extension;
    }

    /**
     * Returns the full image path (image name and path)
     *
     * @return string
     */
    public function getFullPath()
    {
        return $this->_fullPath . $this->_file;
    }

    /**
     * Sets the image full path
     *
     * @param string $fullPath Image full path
     *
     * @return void
     */
    public function setFullPath($fullPath)
    {
        $this->_fullPath = $fullPath;
    }

    /**
     * Return the current JPG output quality
     *
     * @return int
     */
    public function getJpgQuality()
    {
        return $this->_jpgQuality;
    }

    /**
     * Sets the current JPG output quality (values range from 1 - 100)
     *
     * @param int $jpgQuality JPG output quality
     *
     * @return void
     */
    public function setJpgQuality($jpgQuality)
    {
        $this->_jpgQuality = $jpgQuality;
    }

    /**
     * Return the image mime
     *
     * @return string
     */
    public function getMime()
    {
        return $this->_mime;
    }

    /**
     * Sets the image mime
     *
     * @param string $mime Mime value
     *
     * @return void
     */
    public function setMime($mime)
    {
        $this->_mime = $mime;
    }

}