<?php

/**
 * Quform_Validator_Abstract
 *
 * Abstract class for validators. All validators should extend this class.
 *
 * @package Quform
 * @subpackage Validator
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
abstract class Quform_Validator_Abstract implements Quform_Validator_Interface
{
    /**
     * The error messages
     * @var array
     */
    protected $_messages = array();

    /**
     * Error message templates
     * @var array
     */
    protected $_messageTemplates = array();

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (array_key_exists('messages', $options)) {
                $this->setMessageTemplates($options['messages']);
            }
        }
    }

    /**
     * Get the error messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Get the first error message
     *
     * @return string
     */
    public function getFirstMessage()
    {
        return isset($this->_messages[0]) ? $this->_messages[0] : '';
    }

    /**
     * Add an error message
     *
     * @param string $message
     * @return Quform_Validator_Abstract
     */
    public function addMessage($message)
    {
        $this->_messages[] = $message;
        return $this;
    }

    /**
     * Clears the error messages
     *
     * @return Quform_Validator_Abstract
     */
    public function clearMessages()
    {
        $this->_messages = array();
        return $this;
    }

    /**
     * Override a message template
     *
     * @param $key The key of the message to override
     * @param $message The message
     */
    public function setMessageTemplate($key, $message)
    {
        if (array_key_exists($key, $this->_messageTemplates) && strlen($message)) {
            $this->_messageTemplates[$key] = $message;
        }
    }

    /**
     * Override multiple message templates
     *
     * @param array $messages
     */
    public function setMessageTemplates(array $messages)
    {
        foreach ($messages as $key => $message) {
            $this->setMessageTemplate($key, $message);
        }
    }

    /**
     * Get the message template with the given key
     *
     * @param string $key
     */
    public function getMessageTemplate($key)
    {
        if (array_key_exists($key, $this->_messageTemplates)) {
            return $this->_messageTemplates[$key];
        }
    }
}