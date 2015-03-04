<?php
/**
 * StringEvaluator
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
 * StringEvaluator
 * Is a utility class that performs string evaluation and replacements.
 * String tokens are replaced by values found in the replacement maps.
 *
 * PHP version 5.5+
 *
 * @category Shackles_Util
 * @package  Shackles\Util
 * @author   jpdguzman <jpdguzman@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://www.raincreativelab.com
 */
class StringEvaluator
{

    /**
     * Perform string evaluation and replacements
     *
     * @param string $string         String to evaluate
     * @param array  $replacementMap Replacement variable array
     *
     * @return string
     */
    public static function evaluate($string, $replacementMap)
    {
        $collectedVars = array();
        preg_match_all('/{(.*?)}/i', $string, $collectedVars);

        // Replace all the variables
        // with their matching value
        foreach ($collectedVars[1] as $key) {

            $replacement = (array_key_exists($key, $replacementMap)) ?
                $replacementMap[$key] : "";

            $string = preg_replace('/\{' . $key . '\}/', $replacement, $string);
        }

        return $string;
    }
} 