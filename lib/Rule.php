<?php
/**
 * Rule
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

use League\Flysystem\Filesystem;


/**
 * Rule
 * Is the basic unit of work on how an image will be processed.
 * Rules can be grouped and run in series by the ChainRunner.
 *
 * This is a template class specific rules will extends to.
 *
 * PHP version 5.5+
 *
 * @category Shackles
 * @package  Shackles
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
abstract class Rule
{

    /*------------------------------------
     * PRIVATE VARIABLES
     *------------------------------------*/

    /**
     * Rule base settings
     *
     * @var array
     */
    private $_settings = [];

    /**
     * @var Filesystem
     */
    private $_fileSystem;


    /**
     * This is a custom method that will be used
     * to process the image.
     *
     * @param Image $image Image to process
     *
     * @return void
     */
    abstract protected function processImage(Image $image);

    /**
     * Rule
     * A rule can have default or baseline settings that can
     * be customized or used during processing.
     *
     * @param array $settings Settings array
     */
    function __construct($settings = [])
    {
        $this->_settings = $settings;
    }

    /**
     * Run the rule to process the image.
     *
     * @param Image $image Image to process
     */
    public function process(Image $image)
    {
        $this->processImage($image);
    }

    /*------------------------------------
     * ACCESSORS
     *------------------------------------*/

    /**
     * Returns the settings array
     *
     * @return array
     */
    public function getSettings()
    {
        return $this->_settings;
    }

    /**
     * Sets the file system object
     *
     * @param Filesystem $fileSystem File system object
     *
     * @return void
     */
    public function setFileSystem($fileSystem)
    {
        $this->_fileSystem = $fileSystem;
    }

    /*------------------------------------
     * INTERNAL METHODS
     *------------------------------------*/

    /**
     * Return the file system object
     *
     * @return Filesystem
     */
    protected function getFileSystem()
    {
        return $this->_fileSystem;
    }

    /**
     * Checks if the rule has a particular setting
     * Return true if the settings key exists
     *
     * @param string $key Settings key
     *
     * @return bool
     */
    protected function has($key)
    {
        return array_key_exists(
            $key, $this->getSettings()
        );
    }

    /**
     * Return the value of the setting
     *
     * @param string $key Settings key
     *
     * @return null | mixed
     */
    protected function get($key)
    {
        return ($this->has($key)) ? $this->getSettings()[$key] : null;
    }


}