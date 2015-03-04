<?php
use shackles\Chain;
use shackles\ChainRunner;

/**
 * ChainRunnerTest
 *
 * PHP version 5.5+
 *
 * @category $
 * @package  $
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
class ChainRunnerTest
    extends PHPUnit_Framework_TestCase
{

    private $_basePath;

    public function setup()
    {
        $this->_basePath = __DIR__ . "/image";
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Base directory is required for the runner to work.
     */
    public function testRunnerWithBaseDirectory()
    {
        $runner = new ChainRunner();
        $runner->init("");
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Base directory is required for the runner to work.
     */
    public function testRunnerWithNoBaseDirectorySet()
    {
        $runner = new ChainRunner();
        $runner->run(new Chain());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Base directory does not exists
     */
    public function testRunnerWithValidBaseDirectory()
    {
        $runner = new ChainRunner();
        $runner->init("lol");
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unable to run rules on chain. No image found
     */
    public function testRunnerWithNoImageSet()
    {
        $runner = new ChainRunner();
        $runner->init(__DIR__ . "/image");
        $runner->run(new Chain());
    }

    public function testRunnerOnFileFormattingStrategy()
    {
        $runner = new ChainRunner();

        $chain = Mockery::mock(Chain::class);
        $rule = Mockery::mock(Rule::class);
        $rule->shouldReceive("setFileSystem");

        $chain->shouldReceive("getRules")->andReturn([$rule]);
        $chain->shouldReceive("process");

        $basePath = $this->_basePath;
        $runner->init($basePath)
            ->writeAs("test-{NAME}")
            ->process("5639.jpg")
            ->run($chain);

        $this->assertTrue(file_exists($basePath . "/test-5639.jpg"));
        rename($basePath . "/test-5639.jpg", $basePath . "/5639.jpg");
    }

    public function testRunnerOnDefaultFileFormattingStrategy()
    {
        $runner = new ChainRunner();

        $chain = Mockery::mock(Chain::class);
        $chain->shouldReceive("getRules")->andReturn([]);
        $chain->shouldReceive("process");

        $basePath = $this->_basePath;
        $runner->init($basePath)
            ->process("5639.jpg")
            ->run($chain);

        $this->assertTrue(file_exists($basePath . "/5639.jpg"));
    }

    public function testRunnerOnJPGQualityOverride()
    {
        $runner = new ChainRunner();
        $runner->init($this->_basePath)
            ->process("5639.jpg", 100);

        $this->assertEquals(100, $runner->getImage()->getJpgQuality());
    }


    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid JPG output quality set. Values should be from 1 - 100
     */
    public function testRunnerOnInvalidJPGQuality()
    {
        $runner = new ChainRunner();
        $runner->init($this->_basePath)
            ->process("5639.jpg", -1);
    }


}
 