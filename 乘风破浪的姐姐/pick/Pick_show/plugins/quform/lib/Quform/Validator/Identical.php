<?php

/**
 * Quform_Validator_Identical
 *
 * Checks whether or not the given value is identical to the set token
 *
 * @package Quform
 * @subpackage Validator
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Validator_Identical extends Quform_Validator_Abstract
{
    /**
     * The token to check
     * @var string
     */
    protected $_token;

    /**
     * Error message templates. Placeholders:
     *
     * %1$s = the given value
     * %2$s = the set token
     *
     * @var array
     */
    protected $_messageTemplates = array(
        'not_match' => 'Value does not match'
    );

    /**
     * Constructor
     *
     * @param string|array $options Token or options
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        if (is_array($options)) {
            if (array_key_exists('token', $options)) {
                $this->setToken($options['token']);
            }
        } else if ($options !== null) {
            $this->setToken($options);
        }
    }

    /**
     * Returns true if the given value is equal to the set value
     * Return false otherwise.
     *
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $token = $this->getToken();

        if ($token === null) {
            throw new Exception('Token required');
        }

        if ($value != $token) {
            $this->addMessage(sprintf($this->_messageTemplates['not_match'], $value, $token));
            return false;
        }

        return true;
    }

    /**
     * Set the token
     *
     * @param mixed $token
     * @return Quform_Validator_Identical
     */
    public function setToken($token)
    {
        $this->_token = $token;
        return $this;
    }

    /**
     * Get the token
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->_token;
    }
}