<?php

/**
 * Quform_Validator_Time
 *
 * Checks that the given value is a valid time
 *
 * @package Quform
 * @subpackage Validator
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Validator_Time extends Quform_Validator_Abstract
{
    /**
     * Error message templates
     * @var array
     */
    protected $_messageTemplates = array(
        'invalid' => 'This is not a valid time'
    );

    /**
     * Checks whether the given value is a valid time. Also sets
     * the error message if not.
     *
     * @param $value The value to check
     * @return boolean True if valid false otherwise
     */
    public function isValid($value)
    {
        if (is_array($value) && isset($value['hour'], $value['minute'])
            && preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/', $value['hour'] . ':' . $value['minute'])
        ) {
            return true;
        }

        $this->addMessage($this->_messageTemplates['invalid']);
        return false;
    }
}