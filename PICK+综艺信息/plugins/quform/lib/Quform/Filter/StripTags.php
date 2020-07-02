<?php

/**
 * Quform_Filter_StripTags
 *
 * Strips HTML tags
 *
 * @package Quform
 * @subpackage Filter
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Filter_StripTags implements Quform_Filter_Interface
{
    /**
     * HTML tags that will not be stripped
     * @var array
     */
    protected $_allowableTags = array();

    /**
     * Class constructor
     *
     * @param array $options
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (array_key_exists('allowableTags', $options)) {
                $this->setAllowableTags($options['allowableTags']);
            }
        }
    }

    /**
     * Strips all HTML tags from the given value.
     *
     * @param string $value The value to filter
     * @return string The filtered value
     */
    public function filter($value)
    {
        return strip_tags($value, join('', $this->_allowableTags));
    }

    /**
     * Set the allowable tags
     *
     * @param array $allowableTags
     * @return Quform_Filter_StripTags
     */
    public function setAllowableTags($allowableTags)
    {
        $this->_allowableTags = $allowableTags;
        return $this;
    }

    /**
     * Get the allowable tags
     *
     * @return array
     */
    public function getAllowableTags()
    {
        return $this->_allowableTags;
    }
}