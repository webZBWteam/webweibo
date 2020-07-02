<?php

/**
 * Quform_Validator_Captcha
 *
 * Checks whether or not the value matches the value set in $_SESSION['captcha']. A sample
 * class for custom captcha implementations.
 *
 * @package Quform
 * @subpackage Validator
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Validator_Captcha extends Quform_Validator_Abstract
{
    /**
     * Error message templates. Placeholders:
     *
     * %s = the given value
     *
     * @var array
     */
    protected $_messageTemplates = array(
        'not_match' => 'The value does not match'
    );

    /**
     * Compares the given value with the captcha value
     * saved in session. Also sets the error message.
     *
     * @param $value The value to check
     * @return boolean True if valid false otherwise
     */
    public function isValid($value)
    {
        if (isset($_SESSION['captcha']) && $value == $_SESSION['captcha']) {
            return true;
        }

        $this->addMessage(sprintf($this->_messageTemplates['not_match'], $value));
        return false;
    }
}