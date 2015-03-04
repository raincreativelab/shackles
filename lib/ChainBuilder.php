<?php
/**
 * ChainBuilder
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
 * ChainBuilder
 * Is a ChainRunner factory that builds an instance of
 * ChainRunner base on the settings provided.
 *
 * It provides a way to serialize chains and rules
 * and parse serialized chains and rules that runner can run.
 *
 * PHP version 5.5+
 *
 * @category Shackles
 * @package  Shackles
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
class ChainBuilder
{

    /**
     * Creates a new instance of the runner base
     * on the settings provided.
     *
     * Here are the setting details:
     *
     * source: Directory where you images are stored
     * format: Files are renamed when processed.
     *         This is the file name format of the file
     *
     * @param array $settings Chain Runner settings.
     *
     * @return ChainRunner
     */
    public static function newRunner($settings)
    {
        // Throw exception if there is no source set
        self::_throwExceptionIfSourceIsNotDefined($settings);

        // Set default format
        $source = $settings["source"];
        $format = (isset($settings["format"])) ?
            $settings["format"] : "{NAME}-{####}-{W}x{H}";

        $chainRunner = new ChainRunner();
        $chainRunner
            ->init($source)
            ->writeAs($format);

        return $chainRunner;
    }

    /**
     * Convert the chain into its JSON counter part
     *
     * @param Chain $chain Chain object to stringify
     *
     * @return string
     */
    public function stringify($chain)
    {
        $ruleSet = [];
        $rules = $chain->getRules();

        /** @var Rule $rule */
        foreach ($rules as $rule) {

            $ruleSet[] = [
                "rule"     => get_class($rule),
                "settings" => $rule->getSettings()
            ];

        }

        return json_encode($ruleSet);
    }

    /**
     * Convert a rule string into a chain
     *
     * @param string $ruleString Chain Rules string format
     *
     * @return Chain
     */
    public function convert($ruleString)
    {
        $ruleSet = json_decode($ruleString);
        $this->_throwExceptionOnInvalidRuleSet($ruleSet);

        $rules = [];
        foreach ($ruleSet as $rule) {
            $ruleClass = $rule->rule;
            $settings = (array)$rule->settings;

            $this->_throwExceptionOnUnknownRuleClass($ruleClass);

            $rules[] = new $ruleClass($settings);
        }

        $chain = new Chain();
        $chain->setRules($rules);

        return $chain;
    }

    /**
     * Throws an exception if the result set returns a false.
     * This is a case of invalid json_decode
     *
     * @param string $ruleSet Rule set string
     *
     * @throws \Exception
     * @return void
     */
    private function _throwExceptionOnInvalidRuleSet($ruleSet)
    {
        if ($ruleSet === null || $ruleSet === false) {
            throw new \Exception("Invalid rule string provided");
        }
    }

    /**
     * Throws an exception if the Rule class doesn't exists
     *
     * @param string $ruleClass Rule class
     *
     * @throws \Exception
     * @return void
     */
    private function _throwExceptionOnUnknownRuleClass($ruleClass)
    {
        if (!class_exists($ruleClass)) {
            throw new \Exception("Unable to find {$ruleClass}");
        }
    }

    /**
     * Throws if the source in the settings is not defined
     *
     * @param array $settings Settings array
     *
     * @throws \Exception
     * @return void
     */
    private static function _throwExceptionIfSourceIsNotDefined($settings)
    {
        if (!isset($settings["source"])) {
            throw new \Exception("Unable to find image source");
        }
    }
} 