<?php
use shackles\Chain;
use shackles\ChainBuilder;
use shackles\rules\Duplicate;
use shackles\rules\Grayscale;

/**
 * GrayscaleTest
 *
 * PHP version 5.5+
 *
 * @category $
 * @package  $
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
class GrayscaleTest
    extends PHPUnit_Framework_TestCase
{

    public function testGrayscaleRule()
    {
        $chain = new Chain();
        $chain->setRules([
            new Duplicate(),
            new Grayscale()
        ]);

        $basePath = __DIR__ . "/image";
        $runner = ChainBuilder::newRunner([
            "source" => $basePath,
            "format" => "gs-{NAME}"
        ]);

        $runner
            ->process("5639.jpg", 100)
            ->run($chain);

        $file = glob($basePath . "/gs-5639*");
        $this->assertTrue(file_exists($file[0]));
    }
}
 