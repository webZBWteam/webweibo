<?php

/**
 * Quform_Validator_LessThan
 *
 * Checks whether or not the given value is less than the given maximum
 *
 * @package Quform
 * @subpackage Validator
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Validator_LessThan extends Quform_Validator_Abstract
{
    /**
     * Maximum value
     * @var int
     */
    protected $_max;

    /**
     * Error message templates. Placeholders:
     *
     * %1$s = the given value
     * %2$d = the set maximum
     *
     * @var array
     */
    protected $_messageTemplates = array(
        'not_less_than' => '\'%1$s\' is not less than \'%2$d\''
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
            if (array_key_exists('max', $options)) {
                $this->setMax($options['max']);
            }
        }
    }

    /**
     * Returns true if and only if $value is less than max option
     *
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if ($this->_max === null) {
            throw new Exception('Max is not set');
        }

        if ($this->_max <= $value) {
            $this->addMessage(sprintf($this->_messageTemplates['not_less_than'], $value, $this->getMax()));
            return false;
        }

        return true;
    }

    /**
     * Sets the maximum value
     *
     * @param mixed $max
     * @return Quform_Validator_LessThan
     */
    public function setMax($max)
    {
        $this->_max = $max;
        return $this;
    }

    /**
     * Get the maximum value
     *
     * @return mixed
     */
    public function getMax()
    {
        return $this->_max;
    }
}