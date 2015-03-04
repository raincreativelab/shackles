<?php
/**
 * FileFormatter
 *
 * PHP version 5.5+
 *
 * @category Shackles_Util
 * @package  Shackles\Util
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
namespace shackles\util;


/**
 * FileFormatter
 * Is an extension of the string evaluator that adds
 * random string wildcards to the formatting.
 *
 * PHP version 5.5+
 *
 * @category Shackles_Util
 * @package  Shackles\Util
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
class FileFormatter
{

    /**
     * Checks if there are wild-card string to be evaluated.
     * Wildcards are randomly generated string. Represented as
     * {#} in the string. The length of the string depends on the
     * number of # found.
     *
     * @param string $format File format name strategy
     * @param array  $vars   Replacement variables
     *
     * @return string
     */
    public function generate($format, $vars = [])
    {
        $charSpace = "QWERTYUIOPASDFGHJKLZXCVBNM0123456789";

        $output = preg_replace_callback(
            '/{([#]+)}/',
            function ($subject) use (&$vars, $charSpace) {

                $size = strlen($subject[1]);
                $chars = [];

                $str = "";
                for ($i = 0; $i < $size; $i++) {
                    $max = strlen($charSpace) - 1;
                    $min = 0;

                    $chars[] = $charSpace[mt_rand($min, $max)];
                }

                return implode("", $chars);
            },
            $format
        );

        return StringEvaluator::evaluate($output, $vars);
    }
} 