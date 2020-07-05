<?php

/**
 * Quform_Validator_Honeypot
 *
 * Checks that the given value is empty
 *
 * @package Quform
 * @subpackage Validator
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Validator_Honeypot extends Quform_Validator_Abstract
{
    /**
     * Error message templates
     * @var array
     */
    protected $_messageTemplates = array(
        'empty' => 'This field should be empty'
    );

    /**
     * Checks whether the given value is empty.
     *
     * @param $value The value to check
     * @return boolean True if valid false otherwise
     */
    public function isValid($value)
    {
        $valid = true;

        if ($value !== '') {
            $this->addMessage($this->_messageTemplates['empty']);
            $valid = false;
        }

        return $valid;
    }
}