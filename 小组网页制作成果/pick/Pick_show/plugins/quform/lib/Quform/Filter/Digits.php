<?php

/**
 * Quform_Filter_Digits
 *
 * Strips all non-digit characters
 *
 * @package Quform
 * @subpackage Filter
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Filter_Digits implements Quform_Filter_Interface
{
    /**
     * Whether to allow white space characters; off by default
     * @var boolean
     */
    protected $_allowWhiteSpace = false;

    /**
     * Class constructor
     *
     * @param array $options
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (array_key_exists('allowWhiteSpace', $options)) {
                $this->setAllowWhiteSpace($options['allowWhiteSpace']);
            }
        }
    }

    /**
     * Filter everything from the given value except digits
     *
     * @param string $value The value to filter
     * @return string The filtered value
     */
    public function filter($value)
    {
        $whiteSpace = $this->_allowWhiteSpace ? '\s' : '';

        $pattern = '/[^0-9' . $whiteSpace .']/';

        return preg_replace($pattern, '', (string) $value);
    }

    /**
     * Whether or not to allow white space
     *
     * @param boolean $flag
     * @return Quform_Filter_Digits
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