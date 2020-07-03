<?php

/**
 * Quform_Validator_InArray
 *
 * Checks whether or not the given value is in the set array
 *
 * @package Quform
 * @subpackage Validator
 * @copyright Copyright (c) 2009-2018 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Validator_InArray extends Quform_Validator_Abstract
{
    /**
     * The haystack array of possible values
     * @var string
     */
    protected $_haystack = array();

    /**
     * Whether it should check that the data types are also the same
     * @var boolean
     */
    protected $_strict = true;

    /**
     * Whether the check should be inverted
     * @var boolean
     */
    protected $_invert = false;

    /**
     * Error message templates. Placeholders:
     *
     * %1$s = the given value
     *
     * @var array
     */
    protected $_messageTemplates = array(
        'not_in_array' => 'The given value is not valid'
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
            if (array_key_exists('haystack', $options) && is_array($options['haystack'])) {
                $this->setHaystack($options['haystack']);
            }

            if (array_key_exists('strict', $options)) {
                $this->setStrict($options['strict']);
            }

            if (array_key_exists('invert', $options)) {
                $this->setInvert($options['invert']);
            }
        }
    }

    /**
     * Returns true if the given value is in the haystack array
     * Return false otherwise.
     *
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $inArray = in_array($value, $this->_haystack, $this->_strict);

        if ((!$inArray && !$this->_invert) || ($inArray && $this->_invert)) {
            $this->addMessage(sprintf($this->_messageTemplates['not_in_array'], $value));
            return false;
        }

        return true;
    }

    /**
     * Set the haystack array
     *
     * @param array $haystack
     * @return Quform_Validator_InArray
     */
    public function setHaystack($haystack)
    {
        $this->_haystack = $haystack;
        return $this;
    }

    /**
     * Get the haystack array
     *
     * @return array
     */
    public function getHaystack()
    {
        return $this->_haystack;
    }

    /**
     * Set whether it should check that the data types are also the same
     *
     * @param boolean $flag
     * @return Quform_Validator_InArray
     */
    public function setStrict($flag)
    {
        $this->_strict = (bool) $flag;
        return $this;
    }

    /**
     * Get whether it should check that the data types are also the same
     *
     * @return boolean
     */
    public function getStrict()
    {
        return $this->_strict;
    }

    /**
     * Set whether the check should be inverted
     *
     * @param boolean $flag
     * @return Quform_Validator_InArray
     */
    public function setInvert($flag)
    {
        $this->_invert = (bool) $flag;
        return $this;
    }

    /**
     * Get whether the check should be inverted
     *
     * @return boolean
     */
    public function getInvert()
    {
        return $this->_invert;
    }
}