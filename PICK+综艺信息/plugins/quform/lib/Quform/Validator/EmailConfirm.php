<?php

/**
 * Quform_Validator_EmailConfirm
 *
 * Checks whether the given value matches the other email address
 *
 * @package Quform
 * @subpackage Validator
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Validator_EmailConfirm extends Quform_Validator_Abstract
{
	/**
     * The unique name of the other email address field
     * @var boolean
     */
    protected $_key = 'email';

	/**
     * Error message templates. Placeholders:
     *
     * %s = the given email address
     *
     * @var array
     */
    protected $_messageTemplates = array(
        'not_match' => 'The email addresses do not match'
    );

	/**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        if (is_array($options)) {
            if (array_key_exists('key', $options)) {
                $this->setKey($options['key']);
            }
        }
    }

    public function isValid($value)
    {
    	if (isset($_POST[$this->_key]) && $_POST[$this->_key] == $value) {
    		return true;
    	}

    	$this->addMessage(sprintf($this->_messageTemplates['not_match'], $value));
        return false;
    }

	/**
     * Set the unique name of the other email address field
     *
     * @param string $key
     * @return Quform_Validator_EmailConfirm
     */
    public function setKey($key)
    {
        $this->_key = $key;
        return $this;
    }

    /**
     * Get the unique name of the other email address field
     *
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }
}