<?php
use shackles\Chain;
use shackles\ChainBuilder;
use shackles\Rule;

/**
 * ChainBuilderTest
 *
 * PHP version 5.5+
 *
 * @category $
 * @package  $
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
class ChainBuilderTest
    extends PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unable to find image source
     */
    public function testIfExceptionIfSourceIsNotSet()
    {
        $chainRunner = ChainBuilder::newRunner([]);
    }

    public function testCreatingNewRunnerInstance()
    {
        $basePath = __DIR__ . "/image";

        $chainRunner = ChainBuilder::newRunner([
            "source" => $basePath
        ]);

        $chainRunner
            ->process("5639.jpg")
            ->run(new Chain());

        $file = glob($basePath . "/5639-*.jpg");

        $this->assertTrue(count($file) === 1);
        $this->assertTrue(file_exists($file[0]));

        rename($file[0], $basePath . "/5639.jpg");
    }

    public function testConvertingChainToString()
    {
        $chain = new Chain();
        $chainBuilder = new ChainBuilder();
        $str = $chainBuilder->stringify($chain);

        $this->assertEquals("[]", $str);

        $rule = Mockery::mock(Rule::class);
        $rule->shouldReceive("getSettings")
            ->andReturn(["width" => 20]);

        $chain->setRules([$rule]);

        $this->assertEquals(
            '[{"rule":"Mockery_0_shackles_Rule","settings":{"width":20}}]',
            $chainBuilder->stringify($chain)
        );
    }

    public function testConvertingStringToChain()
    {
        $str = '[]';

        $chainBuilder = new ChainBuilder();
        $chain = $chainBuilder->convert($str);
        $this->assertEquals(0, count($chain->getRules()));
    }

    public function testConvertingStringToChainWithValidRules()
    {
        $str = '[{"rule":"shackles\\\\rules\\\\Duplicate","settings":[]}]';

        $chainBuilder = new ChainBuilder();
        $chain = $chainBuilder->convert($str);

        $this->assertEquals(1, count($chain->getRules()));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid rule string provided
     */
    public function testConvertingInvalidStringChain()
    {
        $chainBuilder = new ChainBuilder();
        $chain = $chainBuilder->convert("xxx");
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unable to find Rule
     */
    public function testExceptionOnUndefinedRuleClass()
    {
        $chainBuilder = new ChainBuilder();
        $chain = $chainBuilder->convert('[{"rule":"Rule","settings":{"width":20}}]');
    }

}
 