<?php
/**
 * ChainRunner
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

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use shackles\util\ImageFileFormatter;


/**
 * ChainRunner
 * Runs all the rules defined in a chain.
 * The runner is responsible in providing the neccessary initialization
 * so that the rules can process the image correctly.
 *
 * PHP version 5.5+
 *
 * @category Shackles
 * @package  Shackles
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
class ChainRunner
{

    /**
     * Filesystem object
     *
     * @var Filesystem
     */
    private $_fileSystem;

    /**
     * Directory where images are stored
     *
     * @var string
     */
    private $_baseDirectory;

    /**
     * Image being processed
     *
     * @var Image
     */
    private $_image;

    /**
     * File naming format strategy
     *
     * @var string
     */
    private $_format = "{NAME}";

    /**
     * JPG output quality override (value 1 - 100)
     *
     * @var int
     */
    private $_jpgOutputQuality;

    /**
     * Initialize the chain runner.
     * It requires the image base directory.
     *
     * @param string $imageBaseDirectory Directory of where the image are stored
     *
     * @throws \Exception
     * @return self
     */
    public function init($imageBaseDirectory)
    {
        $this->_baseDirectory = $imageBaseDirectory;

        $this->_throwExceptionIfBaseDirectoryIsNotSet();
        $this->_throwExceptionIfBaseDirectoryDoesNotExists();

        $this->_fileSystem = new Filesystem(
            new Local($this->_baseDirectory)
        );

        return $this;
    }

    /**
     * Runs the chain and its all rules.
     * You can assign multiple chains to process one image
     *
     * run(chain1, chain2, ...)
     *
     * @param Chain $chain Chain object
     *
     * @return void
     */
    public function run(Chain $chain)
    {
        $this->_throwExceptionIfBaseDirectoryIsNotSet();
        $this->_throwExceptionIfNoImageIsFound();

        $chains = func_get_args();

        /** @var Chain $chain */
        foreach ($chains as $chain) {

            // Inject the file system to the rules
            $rules = $chain->getRules();

            /** @var Rule $rule */
            foreach ($rules as $rule) {
                $rule->setFileSystem($this->_fileSystem);
            }

            $chain->process($this->_image);
            $this->_formatFile($this->_image);
        }
    }

    /**
     * Sets the image to process
     * NOTE: Put the name of the image relative to the source.
     *
     * @param string $fileName   Image file name (without any path)
     * @param int    $jpgQuality JPG output quality
     *
     * @return self
     */
    public function process($fileName, $jpgQuality = 0)
    {
        $image = new ImageFile($fileName);

        // If jpg quality is provided
        if ($jpgQuality) {
            $this->_throwExceptionIfJPGQualityIsInvalid($jpgQuality);
            $this->_jpgOutputQuality = $jpgQuality;
        }

        $this->_image = $this->_extractImageProperties($image);

        return $this;
    }

    /**
     * Extracts the properties of the image
     *
     * @param Image $image Image object
     *
     * @return Image
     */
    private function _extractImageProperties(Image $image)
    {
        /** @var Local $localAdapter */
        $localAdapter = $this->getFileSystem()->getAdapter();
        $path = $localAdapter->getPathPrefix();
        $imageName = $image->getImageName();

        // This is the original file name
        $imageFile = new \SplFileObject("{$path}{$imageName}");

        $image->setSourceName($imageName);
        $image->setMime($this->getFileSystem()->getMimetype($imageName));
        $image->setExtension($imageFile->getExtension());
        $image->setFullPath($path);

        // override the default quality if the jpgOutputQuality is set
        if ($this->_jpgOutputQuality) {
            $image->setJpgQuality($this->_jpgOutputQuality);
        }

        return $image;
    }

    /**
     * Returns the Image object
     *
     * @return Image
     */
    public function getImage()
    {
        return $this->_image;
    }

    /**
     * Provide the file naming strategy of the output image
     *
     * @param string $format file name strategy format
     *
     * @return $this
     */
    public function writeAs($format)
    {
        $this->_format = $format;

        return $this;
    }

    /**
     * Formats the file by applying the file name strategy defined
     *
     * @param Image $image Image object being processed
     *
     * @return void
     */
    private function _formatFile(Image $image)
    {
        $imageFileFormatter = new ImageFileFormatter($image);
        $newFileName = $imageFileFormatter->generate($this->_format, []);

        // Rename the final file to its designated format
        $imageName = $image->getImageName();
        if ($imageName !== $newFileName) {
            $this->getFileSystem()->rename($imageName, $newFileName);
        }

        $image->setImageName($newFileName);
    }

    /*------------------------------------
     * INTERNALS
     *------------------------------------*/

    /**
     * Returns the file system object
     *
     * @return Filesystem
     */
    protected function getFileSystem()
    {
        return $this->_fileSystem;
    }

    /**
     * Throws an exception if no image object is found or set
     *
     * @throws \Exception
     * @return void
     */
    private function _throwExceptionIfNoImageIsFound()
    {
        if (!$this->_image) {
            throw new \Exception("Unable to run rules on chain. No image found");
        }
    }

    /**
     * Throws an exception if no base directory is set
     *
     * @throws \Exception
     * @return void
     */
    private function _throwExceptionIfBaseDirectoryIsNotSet()
    {
        if (!$this->_baseDirectory) {
            throw new \Exception(
                "Base directory is required for the runner to work."
            );
        }
    }

    /**
     * Throws an exception if base directory being set does not exists
     *
     * @throws \Exception
     * @return void
     */
    private function _throwExceptionIfBaseDirectoryDoesNotExists()
    {
        if (!file_exists($this->_baseDirectory)) {
            throw new \Exception("Base directory does not exists");
        }
    }

    /**
     * Throws an exception if the value of the jpg output quality
     * is not within range.
     *
     * @param int $jpgQuality JPG output quality
     *
     * @throws \Exception
     * @return void
     */
    private function _throwExceptionIfJPGQualityIsInvalid($jpgQuality)
    {
        if ($jpgQuality < 0 || $jpgQuality > 100) {
            throw new \Exception(
                "Invalid JPG output quality set. Values should be from 1 - 100"
            );
        }
    }

} 