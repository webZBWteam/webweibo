<?php

/**
 * Quform_Filter_Trim
 *
 * Strips white space and other characters from the beginning and end
 *
 * @package Quform
 * @subpackage Filter
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Filter_Trim implements Quform_Filter_Interface
{
    /**
     * Trims whitespace and other characters from the
     * beginning and end of the given value.
     *
     * @param string $value The value to filter
     * @return string The filtered value
     */
    public function filter($value)
    {
        return trim($value);
    }
}