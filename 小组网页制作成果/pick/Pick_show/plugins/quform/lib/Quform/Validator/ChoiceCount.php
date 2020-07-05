<?php

/**
 * Quform_Validator_ChoiceCount
 *
 * Checks that the number of choices is between the set minimum and maximum
 *
 * @package Quform
 * @subpackage Validator
 * @copyright Copyright (c) 2009-2017 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Validator_ChoiceCount extends Quform_Validator_Abstract
{
    /**
     * Minimum number of choices
     * @var int
     */
    protected $_min;

    /**
     * Maximum number of choices
     * @var int|null
     */
    protected $_max;

    /**
     * Error message templates. Placeholders:
     *
     * %1$d = the set minimum or maximum
     * %2$s = the number of choices given
     *
     * @var array
     */
    protected $_messageTemplates = array(
        'invalid' => 'Value is an invalid type, it must be an array',
        'too_few' => 'Please select at least %1$s value(s)',
        'too_many' => 'Please select no more than %1$s value(s)'
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
     * Returns true if and only if the number of choices in $value is at least the min option and
     * no greater than the max option (when the max option is not null).
     *
     * @param array $value
     * @return boolean
     */
    public function isValid($value)
    {
        if ($this->getMax() !== null && $this->getMin() > $this->getMax()) {
            throw new Exception('The minimum must be less than or equal to the maximum');
        }

        if (!is_array($value)) {
            $this->addMessage($this->_messageTemplates['invalid']);
            return false;
        }

        $count = count($value);

        if ($count < $this->getMin()) {
            $this->addMessage(sprintf($this->_messageTemplates['too_few'], $this->getMin(), $count));
            return false;
        }

        if ($this->getMax() !== null && $this->getMax() < $count) {
            $this->addMessage(sprintf($this->_messageTemplates['too_many'], $this->getMax(), $count));
            return false;
        }

        return true;
    }

    /**
     * Set the minimum number of choices
     *
     * @param int $min
     * @return Quform_Validator_ChoiceCount
     */
    public function setMin($min)
    {
        $this->_min = max(0, (int) $min);
        return $this;
    }

    /**
     * Get the minimum number of choices
     *
     * @return int
     */
    public function getMin()
    {
        return $this->_min;
    }

    /**
     * Set the maximum number of choices
     *
     * @param int $max
     * @return Quform_Validator_ChoiceCount
     */
    public function setMax($max)
    {
        $this->_max = (int) $max;
        return $this;
    }

    /**
     * Get the maximum number of choices
     *
     * @return int|null
     */
    public function getMax()
    {
        return $this->_max;
    }
}