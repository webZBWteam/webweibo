<?php

/**
 * Quform_Validator_Alpha
 *
 * Checks whether or not the value contains only alphabetical characters
 *
 * @package Quform
 * @subpackage Validator
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Validator_Alpha extends Quform_Validator_Abstract
{
    /**
     * Whether to allow white space characters; off by default
     * @var boolean
     */
    protected $_allowWhiteSpace = false;

    /**
     * Error message templates. Placeholders:
     *
     * %s = the given value
     *
     * @var array
     */
    protected $_messageTemplates = array(
        'invalid' => 'Only alphabet characters are allowed'
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
            if (array_key_exists('allowWhiteSpace', $options)) {
                $this->setAllowWhiteSpace($options['allowWhiteSpace']);
            }
        }
    }

    /**
     * Returns true if the given value contains only alphabet characters.
     * Return false otherwise.
     *
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_scalar($value)) {
            $this->addMessage($this->_messageTemplates['invalid']);
            return false;
        }

        $value = (string) $value;

        $alphaFilter = new Quform_Filter_Alpha(array(
            'allowWhiteSpace' => $this->_allowWhiteSpace
        ));

        if ($value !== $alphaFilter->filter($value)) {
            $this->addMessage(sprintf($this->_messageTemplates['invalid'], $value));
            return false;
        }

        return true;
    }

    /**
     * Whether or not to allow white space
     *
     * @param boolean $flag
     * @return Quform_Validator_Alpha
     */
    public function setAllowWhiteSpace($flag)
    {
        $this->_allowWhiteSpace = (bool) $flag;
        return $this;
    }

    /**
     * Is white space allowed?
     *
     * @return boolean
     */
    public function getAllowWhiteSpace()
    {
        return $this->_allowWhiteSpace;
    }
}