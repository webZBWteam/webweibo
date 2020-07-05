<?php

/**
 * Quform_Element
 *
 * A Quform form element
 *
 * @package Quform
 * @subpackage Element
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Element
{
    /**
     * Element name
     * @var string
     */
    protected $_name;

    /**
     * Element label
     * @var string
     */
    protected $_label;

    /**
     * Element value
     * @var mixed
     */
    protected $_value;

    /**
     * Element filters
     * @var array
     */
    protected $_filters = array();

    /**
     * Element validators
     * @var array
     */
    protected $_validators = array();

    /**
     * Error messages
     * @var array
     */
    protected $_errors = array();

    /**
     * Is the element multiple input e.g. multiple select
     * @var boolean
     */
    protected $_isMultiple = false;

    /**
     * Is the element in an array?
     * @var boolean
     */
    protected $_isArray = false;

    /**
     * Whether or not the element should be hidden from the notification email
     * @var boolean
     */
    protected $_isHidden = false;

    /**
     * The form this element belongs to
     * @var Quform
     */
    protected $_form;

    public function __construct($name, $label = '')
    {
        if (is_string($name)) {
            if (substr($name, -2) == '[]') {
                $this->setIsMultiple(true);
                $name = substr($name, 0, -2);
            }

            if ($name !== '') {
                $this->setName($name);

                if (!is_string($label) || $label == '') {
                    $this->setLabel($this->_prettyName($this->getName()));
                } else {
                    $this->setLabel($label);
                }
            } else {
                throw new Exception('Every form element must have a name');
            }
        } else {
            throw new Exception('Form element name must be a string');
        }
    }

    /**
     * Set the name of the element
     *
     * @param string $name The name to set
     * @return Quform_Element
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Get the name of the element
     *
     * @return string The name of the element
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set the label of the element
     *
     * @param string $label The label to set
     * @return Quform_Element
     */
    public function setLabel($label)
    {
        $this->_label = $label;
        return $this;
    }

    /**
     * Get the label of the element
     *
     * @return string The label of the element
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * Get the fully qualified name of the element
     *
     * @return string
     */
    public function getFullyQualifiedName()
    {
        $name = $this->getName();

        if ($this->isMultiple()) {
            $name .= '[]';
        }

        return $name;
    }

    /**
     * Set the flag that the element can have multiple values.
     *
     * @param boolean $flag
     * @return Quform_Element
     */
    public function setIsMultiple($flag = true)
    {
        $this->_isMultiple = (bool) $flag;
        return $this;
    }

    /**
     * Does this element have multiple values?
     *
     * @return boolean
     */
    public function isMultiple()
    {
        return $this->_isMultiple;
    }

    /**
     * Set the flag to indicate that the element is
     * an array.
     *
     * @param boolean $flag
     * @return Quform_Element
     */
    public function setIsArray($flag = true)
    {
        $this->_isArray = (bool) $flag;
        return $this;
    }

    /**
     * Is the element an array?
     *
     * @param boolean $flag
     */
    public function isArray()
    {
        return $this->_isArray;
    }

    /**
     * Add a filter
     *
     * @param string|Quform_Filter_Interface $filter The name or instance of the filter
     * @return Quform_Element
     */
    public function addFilter($filter, $options = array())
    {
        if (is_string($filter)) {
            $filter = $this->_loadFilter($filter, $options);
        }

        if ($filter instanceof Quform_Filter_Interface) {
            $name = get_class($filter);
            $this->_filters[$name] = $filter;
        } else {
            throw new Exception('Filter passed to addFilter must be a string or instance of Quform_Filter_Interface');
        }

        return $this;
    }

    /**
     * Add multiple filters
     *
     * @param array $filters The array of filter names or instances
     * @return Quform_Element
     */
    public function addFilters(array $filters)
    {
        foreach ($filters as $filter) {
            if (is_string($filter)) {
                $this->addFilter($filter);
            } else if ($filter instanceof Quform_Filter_Interface) {
                $this->addFilter($filter);
            } else if (is_array($filter)) {
                if (isset($filter[0])) {
                    $options = array();
                    if (isset($filter[1]) && is_array($filter[1])) {
                        $options = $filter[1];
                    }

                    $this->addFilter($filter[0], $options);
                }
            }
        }

        return $this;
    }

    /**
     * Set the filters, overrides previously added filters
     *
     * @param array $filters The array of filter names or instances
     * @return Quform_Element
     */
    public function setFilters(array $filters)
    {
        $this->clearFilters();
        $this->addFilters($filters);
        return $this;
    }

    /**
     * Remove all filters
     *
     * @return Quform_Element
     */
    public function clearFilters()
    {
        $this->_filters = array();
        return $this;
    }

    /**
     * Does this element have filters?
     *
     * @return boolean
     */
    public function hasFilters()
    {
        return count($this->getFilters()) > 0;
    }

    /**
     * Get the filters
     *
     * @return array The array of filters
     */
    public function getFilters()
    {
        return $this->_filters;
    }

    /**
     * Does the element have the given filter?
     *
     * @param string $name The name of the filter
     * @return boolean
     */
    public function hasFilter($filter)
    {
        $result = false;

        if ($filter instanceof Quform_Filter_Interface) {
            $filter = get_class($filter);
        }

        if (is_string($filter)) {
            if (strpos($filter, 'Quform_Filter_') === false) {
                $filter = 'Quform_Filter_' . ucfirst($filter);
            }

            $result = array_key_exists($filter, $this->getFilters());
        }

        return $result;
    }

    /**
     * Get the filter with the given name
     *
     * @param string $filter The name of the filter
     * @return Quform_Filter_Interface|null The filter or null if the element does not have the filter
     */
    public function getFilter($filter)
    {
        $instance = null;

        if (strpos($filter, 'Quform_Filter_') === false) {
            $filter = 'Quform_Filter_' . ucfirst($filter);
        }

        $filters = $this->getFilters();
        if (array_key_exists($filter, $filters)) {
            $instance = $filters[$filter];
        }

        return $instance;
    }

    /**
     * Remove a filter with the given name
     *
     * @param string $filter The name of the filter
     * @return Quform_Element
     */
    public function removeFilter($filter)
    {
        if (strpos($filter, 'Quform_Filter_') === false) {
            $filter = 'Quform_Filter_' . ucfirst($filter);
        }

        $filters = $this->getFilters();
        if (array_key_exists($filter, $filters)) {
            unset($this->_filters[$filter]);
        }

        return $this;
    }

    /**
     * Add a validator
     *
     * @param string|Quform_Validator_Interface $validator The validator to add
     * @return Quform_Element
     */
    public function addValidator($validator, $options = array())
    {
        if (is_string($validator)) {
            $validator = $this->_loadValidator($validator, $options);
        }

        if ($validator instanceof Quform_Validator_Interface) {
            $name = get_class($validator);
            $this->_validators[$name] = $validator;
        } else {
            throw new Exception('Validator passed to addValidator must be a string or instance of Quform_Validator_Interface');
        }

        return $this;
    }

    /**
     * Add mutliple validators
     *
     * @param array $validators The validators to add
     * @return Quform_Element
     */
    public function addValidators(array $validators)
    {
        foreach ($validators as $validator) {
            if (is_string($validator)) {
                $this->addValidator($validator);
            } else if ($validator instanceof Quform_Validator_Interface) {
                $this->addValidator($validator);
            } else if (is_array($validator)) {
                if (isset($validator[0])) {
                    $options = array();
                    if (isset($validator[1]) && is_array($validator[1])) {
                        $options = $validator[1];
                    }

                    $this->addValidator($validator[0], $options);
                }
            }
        }

        return $this;
    }

    /**
     * Set the validators, overrides previously added validators
     *
     * @param array $validators The validators to add
     * @return Quform_Element
     */
    public function setValidators(array $validators)
    {
        $this->clearValidators();
        $this->addValidators($validators);
        return $this;
    }

    /**
     * Remove all validators
     *
     * @return Quform_Element
     */
    public function clearValidators()
    {
        $this->_validators = array();
        return $this;
    }

    /**
     * Does the element have any validators?
     *
     * @return boolean
     */
    public function hasValidators()
    {
        return count($this->getValidators()) > 0;
    }

    /**
     * Get the validators
     *
     * @return array The validators
     */
    public function getValidators()
    {
        return $this->_validators;
    }

    /**
     * Does the element have the given validator?
     *
     * @param string|Quform_Validator_Interface $name The name or instance of the validator
     * @return boolean
     */
    public function hasValidator($validator)
    {
        $result = false;

        if ($validator instanceof Quform_Validator_Interface) {
            $validator = get_class($validator);
        }

        if (is_string($validator)) {
            if (strpos($validator, 'Quform_Validator_') === false) {
                $validator = 'Quform_Validator_' . ucfirst($validator);
            }

            $result = array_key_exists($validator, $this->getValidators());
        }

        return $result;
    }

    /**
     * Get the validator with the given name
     *
     * @param string $validator The name of the validator
     * @return Quform_Validator_Interface|null The validator or null if the element does not have the validator
     */
    public function getValidator($validator)
    {
        $instance = null;

        if (strpos($validator, 'Quform_Validator_') === false) {
            $validator = 'Quform_Validator_' . ucfirst($validator);
        }

        $validators = $this->getValidators();
        if (array_key_exists($validator, $validators)) {
            $instance = $validators[$validator];
        }

        return $instance;
    }

    /**
     * Remove a validator with the given name
     *
     * @param string $validator The name of the validator
     * @return Quform_Element
     */
    public function removeValidator($validator)
    {
        if (strpos($validator, 'Quform_Validator_') === false) {
            $validator = 'Quform_Validator_' . ucfirst($validator);
        }

        $validators = $this->getValidators();
        if (array_key_exists($validator, $validators)) {
            unset($this->_validators[$validator]);
        }

        return $this;
    }

    /**
     * Sets whether the element is required or not
     *
     * @param boolean $flag
     * @return Quform_Element
     */
    public function setRequired($flag = true)
    {
        if ($flag === true) {
            $this->addValidator('required');
        } else {
            $this->removeValidator('required');
        }

        return $this;
    }

    /**
     * Get the unfiltered (raw) value
     *
     * @return string The raw value
     */
    public function getValueUnfiltered()
    {
        return $this->_value;
    }

    /**
     * Set the value
     *
     * @param string $value The value to set
     * @return Quform_Element
     */
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * Get the filtered value
     *
     * @return string The filtered value
     */
    public function getValue()
    {
        $valueFiltered = $this->_value;

        if (is_array($valueFiltered)) {
            $this->_filterValueRecursive($valueFiltered);
        } else {
            $this->_filterValue($valueFiltered);
        }

        return $valueFiltered;
    }

    /**
     * Is the given value valid?
     *
     * @param string $value The value to check
     * @param array $context The other submitted form values
     * @return boolean True if valid, false otherwise
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);
        $value = $this->getValue();

        if (!$this->hasValidator('required')) {
            $requiredValidator = new Quform_Validator_Required();

            // We can skip validating if the value is empty and the element is not required
            if (!$requiredValidator->isValid($value)) {
                return true;
            }
        }

        $this->_errors = array();
        $valid = true;

        foreach ($this->getValidators() as $validator) {
            if ($validator->isValid($value, $context)) {
                continue;
            } else {
                $errors = $validator->getMessages();
                $valid = false;
            }

            $this->_errors = array_merge($this->_errors, $errors);
        }

        return $valid;
    }

    /**
     * Get the error messages
     *
     * @return array The error messages
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Does the element have errors?
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return count($this->getErrors()) > 0;
    }

    /**
     * Set whether or not the value should be shown in places
     * like the notification email
     *
     * @param boolean $flag
     * @return Quform_Element
     */
    public function setIsHidden($flag = true)
    {
        $this->_isHidden = (bool) $flag;
        return $this;
    }

    /**
     * Should the value should be shown in places
     * like the notification email
     *
     * @return boolean
     */
    public function isHidden()
    {
        return $this->_isHidden;
    }

    /**
     * Set the form the element belongs to
     *
     * @param Quform $form
     */
    public function setForm(Quform $form)
    {
        $this->_form = $form;
        return $this;
    }

    /**
     * Get the form the element belongs to
     *
     * @return Quform
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Filter the given value by reference
     *
     * @param string $value
     */
    protected function _filterValue(&$value)
    {
        foreach ($this->getFilters() as $filter) {
            $value = $filter->filter($value);
        }
    }

    /**
     * Recursively filter the given value by reference
     *
     * @param array $value
     */
    protected function _filterValueRecursive(&$value)
    {
        if (is_array($value)) {
            array_walk($value, array($this, '_filterValueRecursive'));
        } else {
            $this->_filterValue($value);
        }
    }

    /**
     * Load the filter instance from the given name
     *
     * @param string $filter
     * @param array $options Options to pass to the filter
     * @return null|Quform_Filter_Interface The filter
     */
    protected function _loadFilter($filter, $options = array())
    {
        $instance = null;

        if (!empty($filter)) {
            $className = 'Quform_Filter_' . ucfirst($filter);
            if (class_exists($className)) {
                $instance = new $className($options);
            }
        }

        if ($instance == null) {
            throw new Exception("Filter '$filter' does not exist");
        }

        return $instance;
    }

    /**
     * Load the validator instance from the given name
     *
     * @param string $validator
     * @param array $options Options to pass to the validator
     * @return null|Quform_Validator_Interface The validator
     */
    protected function _loadValidator($validator, $options = array())
    {
        $instance = null;

        if (!empty($validator)) {
            $className = 'Quform_Validator_' . ucfirst($validator);
            if (class_exists($className)) {
                $instance = new $className($options);
            }
        }

        if ($instance == null) {
            throw new Exception("Validator '$validator' does not exist");
        }

        return $instance;
    }

    /**
     * Get the pretty version of the form element name. Translates
     * the machine name to a more human readable format.  E.g.
     * "email_address" becomes "Email address".
     *
     * @param string $name The form element name
     * @return string The pretty version of the name
     */
    protected function _prettyName($name)
    {
        $prettyName = str_replace(array('-', '_'), ' ', $name);
        $prettyName = ucfirst($prettyName);
        return $prettyName;
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
        $filteredValue = $this->getValue();
        $value = '';

        if (is_scalar($filteredValue)) {
            $value = nl2br(Quform::escape($filteredValue));
        } else if (is_array($filteredValue)) {
            foreach ($filteredValue as $val) {
                if (is_scalar($val)) {
                    $value .= nl2br(Quform::escape($val)) . $separator;
                }
            }
        }

        return $value;
    }

    /**
     * Get the value of the form element formatted in plain text
     *
     * @param string $separator The separator to join array types
     * @return string
     */
    public function getValuePlain($separator = ', ')
    {
        $filteredValue = $this->getValue();
        $value = '';

        if (is_scalar($filteredValue)) {
            $value = (string) $filteredValue;
        } else if (is_array($filteredValue)) {
            foreach ($filteredValue as $val) {
                if (is_scalar($val)) {
                    $value .= (string) $val . $separator;
                }
            }
        }

        return $value;
    }

    /**
     * Returns true if the value of this element is empty, false otherwise
     *
     * @return boolean
     */
    public function isEmpty()
    {
        $v = $this->getValue();

        return $v === '' || $v === null || $v === array();
    }
}