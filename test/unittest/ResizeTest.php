<?php
use shackles\Chain;
use shackles\ChainBuilder;
use shackles\rules\Duplicate;
use shackles\rules\Resize;

/**
 * ResizeTest
 *
 * PHP version 5.5+
 *
 * @category $
 * @package  $
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
class ResizeTest
    extends PHPUnit_Framework_TestCase
{

    public function testResizeRule()
    {
        $chain = new Chain();
        $chain->setRules([
            new Duplicate(),
            new Resize(["width" => 300])
        ]);

        $basePath = __DIR__ . "/image";
        $runner = ChainBuilder::newRunner([
            "source" => $basePath,
            "format" => "small-{NAME}"
        ]);

        $runner
            ->process("5639.jpg")
            ->run($chain);

        $file = glob($basePath . '/small-5639.jpg');
        $resizedImage = $file[0];

        $this->assertTrue(file_exists($resizedImage));
        list($width, $height) = getimagesize($resizedImage);

        // The width of the image should be 300
        $this->assertEquals(300, $width);
        unlink($resizedImage);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Resize rule needs a width to work
     */
    public function testIfNoWidthIsSet()
    {
        $chain = new Chain();
        $chain->setRules([
            new Resize()
        ]);

        $basePath = __DIR__ . "/image";
        $runner = ChainBuilder::newRunner([
            "source" => $basePath,
            "format" => "small-{NAME}"
        ]);

        $runner
            ->process("5639.jpg")
            ->run($chain);
    }
}
 