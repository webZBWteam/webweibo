<?php

/**
 * Quform_Validator_Date
 *
 * Checks that the given value is a valid date
 *
 * @package Quform
 * @subpackage Validator
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Validator_Date extends Quform_Validator_Abstract
{
    /**
     * Error message templates
     * @var array
     */
    protected $_messageTemplates = array(
        'invalid' => 'This is not a valid date'
    );

    /**
     * Checks whether the given value is a valid date. Also sets
     * the error message if not.
     *
     * @param $value The value to check
     * @return boolean True if valid false otherwise
     */
    public function isValid($value)
    {
        if (is_array($value) && isset($value['day'], $value['month'], $value['year'])
            && is_numeric($value['day']) && is_numeric($value['month']) && is_numeric($value['year'])
            && checkdate((int) $value['month'], (int) $value['day'], (int) $value['year'])
        ) {
            return true;
        }

        $this->addMessage($this->_messageTemplates['invalid']);
        return false;
    }
}