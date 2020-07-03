<?php

/**
 * Quform_Validator_WordCount
 *
 * Checks whether or not the number of words in the given value are between
 * the set minimum and maximum
 *
 * @package Quform
 * @subpackage Validator
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Validator_WordCount extends Quform_Validator_Abstract
{
    /**
     * Minimum number of words
     * @var int
     */
    protected $_min;

    /**
     * Maximum number of words.  If null, there is no maximum length
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
        'too_short' => 'Value must contain at least %1$d words',
        'too_long' => 'Value must contain no more than %1$d words'
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
     * Returns true if and only if the word count of $value is at least the min option and
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

        $count = str_word_count($value);

        if ($count < $this->getMin()) {
            $this->addMessage(sprintf($this->_messageTemplates['too_short'], $this->getMin(), $value, $count));
            return false;
        }

        if ($this->getMax() !== null && $this->getMax() < $count) {
            $this->addMessage(sprintf($this->_messageTemplates['too_long'], $this->getMax(), $value, $count));
            return false;
        }

        return true;
    }

    /**
     * Set the minimum word count
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
     * Get the minimum word count
     *
     * @return int
     */
    public function getMin()
    {
        return $this->_min;
    }

    /**
     * Set the maximum word count
     *
     * @param int $max
     * @return Quform_Validator_WordCount
     */
    public function setMax($max)
    {
        $this->_max = (int) $max;
        return $this;
    }

    /**
     * Get the maximum word count
     *
     * @return int|null
     */
    public function getMax()
    {
        return $this->_max;
    }
}