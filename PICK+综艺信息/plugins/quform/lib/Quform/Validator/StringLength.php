<?php

/**
 * Quform_Validator_StringLength
 *
 * Checks whether or not the number of characters in the given value are between
 * the set minimum and maximum
 *
 * @package Quform
 * @subpackage Validator
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Validator_StringLength extends Quform_Validator_Abstract
{
    /**
     * Minimum length
     * @var int
     */
    protected $_min;

    /**
     * Maximum length.  If null, there is no maximum length
     * @var int|null
     */
    protected $_max;

    /**
     * Error message templates. Placeholders:
     *
     * %1$d = the set minimum or maximum
     * %2$s = the given value
     * %3$d = the length of the value
     *
     * @var array
     */
    protected $_messageTemplates = array(
        'invalid' => 'Value is an invalid type, it must be a string',
        'too_short' => 'Value must be at least %1$d characters long',
        'too_long' => 'Value must be no longer than %1$d characters'
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
            if (!array_key_exists('min', $options)) {
                $options['min'] = 0;
            }

            $this->setMin($options['min']);

            if (array_key_exists('max', $options)) {
                $this->setMax($options['max']);
            }
        }
    }

    /**
     * Returns true if and only if the string length of $value is at least the min option and
     * no greater than the max option (when the max option is not null).
     *
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if ($this->getMax() !== null && $this->getMin() > $this->getMax()) {
            throw new Exception('The minimum must be less than or equal to the maximum length');
        }

        if (!is_string($value)) {
            $this->addMessage($this->_messageTemplates['invalid']);
            return false;
        }

        $length = strlen(utf8_decode($value));

        if ($length < $this->getMin()) {
            $this->addMessage(sprintf($this->_messageTemplates['too_short'], $this->getMin(), $value, $length));
            return false;
        }

        if ($this->getMax() !== null && $this->getMax() < $length) {
            $this->addMessage(sprintf($this->_messageTemplates['too_long'], $this->getMax(), $value, $length));
            return false;
        }

        return true;
    }

    /**
     * Set the minimum length
     *
     * @param int $min
     * @return Quform_Validator_StringLength
     */
    public function setMin($min)
    {
        $this->_min = max(0, (int) $min);
        return $this;
    }

    /**
     * Get the minimum length
     *
     * @return int
     */
    public function getMin()
    {
        return $this->_min;
    }

    /**
     * Set the maximum length
     *
     * @param int $max
     * @return Quform_Validator_StringLength
     */
    public function setMax($max)
    {
        $this->_max = (int) $max;
        return $this;
    }

    /**
     * Get the maximum length
     *
     * @return int|null
     */
    public function getMax()
    {
        return $this->_max;
    }
}