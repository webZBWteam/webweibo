<?php

/**
 * Quform_Validator_GreaterThan
 *
 * Checks whether or not the given value is greater than the given minimum
 *
 * @package Quform
 * @subpackage Validator
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Validator_GreaterThan extends Quform_Validator_Abstract
{
    /**
     * Minimum value
     * @var int
     */
    protected $_min;

    /**
     * Error message templates. Placeholders:
     *
     * %1$s = the given value
     * %2$d = the set minimum
     *
     * @var array
     */
    protected $_messageTemplates = array(
        'not_numeric' => 'Value is not numeric',
        'not_greater_than' => '\'%1$s\' is not greater than \'%2$d\''
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
            if (array_key_exists('min', $options)) {
                $this->setMin($options['min']);
            }
        }
    }

    /**
     * Returns true if and only if $value is greater than min option
     *
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if ($this->getMin() === null) {
            throw new Exception("Missing option 'min'");
        }

        if (!is_numeric($value)) {
            $this->addMessage(sprintf($this->_messageTemplates['not_numeric'], $value, $this->getMin()));
            return false;
        }

        if ($this->getMin() >= $value) {
            $this->addMessage(sprintf($this->_messageTemplates['not_greater_than'], $value, $this->getMin()));
            return false;
        }

        return true;
    }

    /**
     * Sets the minimum value
     *
     * @param mixed $min
     * @return Quform_Validator_GreaterThan
     */
    public function setMin($min)
    {
        $this->_min = $min;
        return $this;
    }

    /**
     * Get the minimum value
     *
     * @return mixed
     */
    public function getMin()
    {
        return $this->_min;
    }
}