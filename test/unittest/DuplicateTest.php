<?php
use shackles\Chain;
use shackles\ChainBuilder;
use shackles\rules\Duplicate;

/**
 * DuplicateTest
 *
 * PHP version 5.5+
 *
 * @category $
 * @package  $
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
class DuplicateTest
    extends PHPUnit_Framework_TestCase
{

    public function testDuplicateRule()
    {
        $chain = new Chain();
        $chain->setRules([
            new Duplicate()
        ]);

        $basePath = __DIR__ . "/image";
        $runner = ChainBuilder::newRunner([
            "source" => $basePath
        ]);

        $runner
            ->process("5639.jpg")
            ->run($chain);

        $file = $runner->getImage()->getFullPath();
        $this->assertTrue(file_exists($file));
        unlink($file);
    }
}
 