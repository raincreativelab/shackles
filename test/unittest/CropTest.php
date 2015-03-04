<?php
use shackles\Chain;
use shackles\ChainBuilder;
use shackles\ChainRunner;
use shackles\rules\Crop;
use shackles\rules\Duplicate;
use shackles\rules\Rotate;

/**
 * CropTest
 *
 * PHP version 5.5+
 *
 * @category $
 * @package  $
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
class CropTest
    extends PHPUnit_Framework_TestCase
{
    private $_basePath;

    /**
     * @var ChainRunner
     */
    private $_runner;

    public function setup()
    {
        $this->_basePath = __DIR__ . "/image";
        $this->_runner = ChainBuilder::newRunner([
            "source" => $this->_basePath,
            "format" => "thumb-{NAME}"
        ]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid crop dimensions
     */
    public function testIfExceptionIsThrownIfNoCropSettingsSet()
    {
        $chain = new Chain();
        $chain->setRules([
            new Crop()
        ]);

        $this->_runner
            ->process("5639.jpg")
            ->run($chain);
    }

    public function testAutoCropping()
    {
        $chain = new Chain();
        $chain->setRules([
            new Duplicate(),
            new Crop([
                "width"  => 200,
                "height" => 100
            ])
        ]);

        $this->_runner
            ->process("5639.jpg", 100)
            ->run($chain);

        $this->assertTrue(file_exists($this->_runner->getImage()->getFullPath()));
        list($width, $height) = getimagesize(
            $this->_runner->getImage()->getFullPath()
        );

        $this->assertEquals(200, $width);
        $this->assertEquals(100, $height);
    }

    public function testCroppingWithCropWidthProvided()
    {
        $chain = new Chain();
        $chain->setRules([
            new Duplicate(),
            new Crop([
                "width"          => 500,
                "height"         => 250,
                "crop-box-width" => 640,
            ])
        ]);

        $this->_runner
            ->writeAs("bigger-{NAME}-{W}")
            ->process("5639.jpg", 100)
            ->run($chain);

        $this->assertTrue(file_exists($this->_runner->getImage()->getFullPath()));
        list($width, $height) = getimagesize(
            $this->_runner->getImage()->getFullPath()
        );

        $this->assertEquals(500, $width);
        $this->assertEquals(250, $height);
    }

    public function testCroppingWithFullBoxDetails()
    {
        $chain = new Chain();
        $chain->setRules([
            new Duplicate(),
            new Crop([
                "width"          => 500,
                "height"         => 250,
                "crop-box-width" => 640,
                "x"              => 0,
                "y"              => 0
            ])
        ]);

        $this->_runner
            ->writeAs("top-crop-{NAME}-{W}")
            ->process("5639.jpg", 100)
            ->run($chain);

        $this->assertTrue(file_exists($this->_runner->getImage()->getFullPath()));
        list($width, $height) = getimagesize(
            $this->_runner->getImage()->getFullPath()
        );

        $this->assertEquals(500, $width);
        $this->assertEquals(250, $height);
    }

}
 