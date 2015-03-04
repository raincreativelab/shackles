<?php
/**
 * Chain
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
 * Chain
 * is a collection of Rules run in series base on
 * how the rules are ordered.
 *
 * PHP version 5.5+
 *
 * @category Shackles
 * @package  Shackles
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
class Chain
{

    /**
     * Set of rules use to process the image.
     *
     * @var array
     */
    private $_rules = [];

    /**
     * Runs each of the rules to process the image.
     *
     * @param Image &$image Image to process
     *
     * @return Image
     */
    public function process(Image &$image)
    {
        $rules = $this->getRules();

        /** @var Rule $rule */
        foreach ($rules as $rule) {
            $rule->process($image);
        }

        return $image;
    }


    /*------------------------------------
     * ACCESSORS
     *------------------------------------*/

    /**
     * Returns all the rules defined
     *
     * @return array
     */
    public function getRules()
    {
        return $this->_rules;
    }

    /**
     * Sets the rules
     *
     * @param array $rules Rules
     *
     * @return void
     */
    public function setRules($rules)
    {
        $this->_rules = $rules;
    }

} 