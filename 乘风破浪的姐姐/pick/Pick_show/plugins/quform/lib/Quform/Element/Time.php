<?php

/**
 * Quform_Element_Time
 *
 * A Quform time element
 *
 * @package Quform
 * @subpackage Element
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Element_Time extends Quform_Element
{
    /**
     * The PHP date() format of the time for display
     * @var string
     */
    protected $_timeFormat = 'H:i';

    /**
     * Class constructor
     *
     * @param string $name
     * @param string $label
     * @param array $options
     */
    public function __construct($name, $label = '', $options = null)
    {
        parent::__construct($name, $label);

        if (is_array($options)) {
            if (array_key_exists('timeFormat', $options)) {
                $this->setTimeFormat($options['timeFormat']);
            }
        }
    }

    /**
     * Sets the PHP date() format of the time for display
     *
     * @param string $format
     * @return Quform_Element_Time
     */
    public function setTimeFormat($format)
    {
        $this->_timeFormat = $format;
        return $this;
    }

    /**
     * Get the PHP date() format of the time for display
     *
     * @return string
     */
    public function getTimeFormat()
    {
        return $this->_timeFormat;
    }

    /**
     * Get the formatted value
     *
     * The value can be formatted into HTML or plain text, arrays
     * are joined by $separator
     *
     * @deprecated 2.0.3 Use getValueHtml or getValuePlain
     * @param string $format plain or html
     * @param string $separator
     * @return string The formatted value
     */
    public function getFormattedValue($format = 'plain', $separator = ', ')
    {
        if ($format === 'html') {
            return $this->getValueHtml($separator);
        } else {
            return $this->getValuePlain($separator);
        }
    }

    /**
     * Get the value of the form element formatted in HTML
     *
     * @param string $separator The separator to join array types
     * @return string
     */
    public function getValueHtml($separator = '<br />')
    {
        // For this element, it's the same value as plain text
        return $this->getValuePlain($separator);
    }

    /**
     * Get the value of the form element formatted in plain text
     *
     * @param string $separator The separator to join array types
     * @return string
     */
    public function getValuePlain($separator = ', ')
    {
        $v = $this->getValue();
        $value = '';

        if (is_array($v) && isset($v['hour'], $v['minute']) && is_numeric($v['hour']) && is_numeric($v['minute'])) {
            $value = date($this->getTimeFormat(), strtotime($v['hour'] . ':' . $v['minute']));
        }

        return $value;
    }

    /**
     * Get the fully qualified name of the element
     *
     * @return string
     */
    public function getFullyQualifiedName()
    {
        // The JS needs to find an element with this name
        return $this->getName() . '[hour]';
    }

    /**
     * Returns true if the value of this element is empty, false otherwise
     *
     * @return boolean
     */
    public function isEmpty()
    {
        $v = $this->getValue();

        if (is_array($v) && isset($v['hour'], $v['minute']) && is_numeric($v['hour']) && is_numeric($v['minute'])) {
            return false;
        }

        return true;
    }
}