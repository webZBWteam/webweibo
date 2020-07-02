<?php

/**
 * Quform_Validator_Regex
 *
 * Validates the value against a regular expression
 *
 * @package Quform
 * @subpackage Validator
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Validator_Regex extends Quform_Validator_Abstract
{
    /**
     * The regular expression pattern
     * @var string
     */
    protected $_pattern = '';

    /**
     * Error message templates. Placeholders:
     *
     * %s = the given value
     *
     * @var array
     */
    protected $_messageTemplates = array(
        'invalid' => 'Invalid type given. String, integer or float expected',
        'not_match' => 'The given value is not valid'
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
            if (array_key_exists('pattern', $options) && is_string($options['pattern'])) {
                $this->setPattern($options['pattern']);
            }
        }
    }

    /**
     * Returns true if the given value matches the regular expression pattern
     * Return false otherwise.
     *
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            $this->addMessage($this->_messageTemplates['invalid']);
            return false;
        }

        $value = (string) $value;

        if (!preg_match($this->_pattern, $value)) {
            $this->addMessage(sprintf($this->_messageTemplates['not_match'], $value));
            return false;
        }

        return true;
    }

    /**
     * Set the regular expression pattern
     *
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->_pattern = $pattern;
    }

    /**
     * Get the regular expression pattern
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->_pattern;
    }
}