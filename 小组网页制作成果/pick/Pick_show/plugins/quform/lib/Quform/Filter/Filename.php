<?php

/**
 * Quform_Filter_Filename
 *
 * Sanitises a filename
 *
 * @package Quform
 * @subpackage Filter
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Filter_Filename implements Quform_Filter_Interface
{
    /**
     * Sanitise a filename
     *
     * @param string $value The value to filter
     * @return string The filtered value
     */
    public function filter($value)
    {
        // Make each letter lowercase
        $value = strtolower($value);

        // Remove quote marks
        $value = preg_replace('/[\'"]/', '', $value);

        // Replace any non letter or digit or dot with dash
        $value = preg_replace("/[^0-9a-zA-Z_\.]/", '-', $value);

        // Replace more than one dash with single dash
        $value = preg_replace('/-{2,}/', '-', $value);

        // Remove dashes from the start and end
        $value = preg_replace(array('/-$/', '/^-/'), '', $value);

        return $value;
    }
}