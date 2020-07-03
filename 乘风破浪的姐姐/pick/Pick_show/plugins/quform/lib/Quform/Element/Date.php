<?php

/**
 * Quform_Element_Date
 *
 * A Quform date element
 *
 * @package Quform
 * @subpackage Element
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Element_Date extends Quform_Element
{
    /**
     * The PHP date() format of the date for display
     * @var string
     */
    protected $_dateFormat = 'F j, Y';

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
            if (array_key_exists('dateFormat', $options)) {
                $this->setDateFormat($options['dateFormat']);
            }
        }
    }

    /**
     * Sets the PHP date() format of the date for display
     *
     * @param string $format
     * @return Quform_Element_Date
     */
    public function setDateFormat($format)
    {
        $this->_dateFormat = $format;
        return $this;
    }

    /**
     * Get the PHP date() format of the date for display
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->_dateFormat;
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

        if (is_array($v) && isset($v['day'], $v['month'], $v['year'])
            && is_numeric($v['day']) && is_numeric($v['month']) && is_numeric($v['year'])
        ) {
            $day = (int) $v['day'];
            $month = (int) $v['month'];
            $year = (int) $v['year'];

            if (checkdate($month, $day, $year)) {
                $value = date($this->getDateFormat(), mktime(12, 0, 0, $month, $day, $year));
            }
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
        return $this->getName() . '[month]';
    }

    /**
     * Returns true if the value of this element is empty, false otherwise
     *
     * @return boolean
     */
    public function isEmpty()
    {
        $v = $this->getValue();

        if (is_array($v) && isset($v['day'], $v['month'], $v['year'])
            && is_numeric($v['day']) && is_numeric($v['month']) && is_numeric($v['year'])
        ) {
            return false;
        }

        return true;
    }
}