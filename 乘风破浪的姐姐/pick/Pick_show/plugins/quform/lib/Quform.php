<?php

/**
 * Quform
 *
 * A Quform form
 *
 * @package Quform
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform
{
    /**
     * The elements of the form
     * @var array
     */
    protected $_elements = array();

    /**
     * Form error message
     * @var string
     */
    protected $_error = '';

    /**
     * Add a single element to the form
     *
     * @param $element Quform_Element The element to add
     * @return Quform
     */
    public function addElement(Quform_Element $element)
    {
        $element->setForm($this);
        $this->_elements[$element->getName()] = $element;
        return $this;
    }

    /**
     * Add multiple elements to the form
     *
     * @param $elements array The array of elements
     * @return Quform
     */
    public function addElements(array $elements)
    {
        foreach ($elements as $element) {
            $this->addElement($element);
        }
        return $this;
    }

    /**
     * Is the form valid?
     *
     * @param array $values The values to check against
     * @return boolean True if valid, false otherwise
     */
    public function isValid(array $values = array())
    {
        $valid = true;

        foreach ($this->getElements() as $element) {
            if ($element->isArray()) {
                $value = $this->_dissolveArrayValue($values, $element->getName());
            } else {
                $value = isset($values[$element->getName()]) ? $values[$element->getName()] : null;
            }

            if (!$element->isValid($value, $values)) {
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * Get all of the form elements
     *
     * @return array The form elements
     */
    public function getElements()
    {
        return $this->_elements;
    }

    /**
     * Get a form element by name
     *
     * @param string $name
     * @return Quform_Element|null $name
     */
    public function getElement($name)
    {
        if (isset($this->_elements[$name])) {
            return $this->_elements[$name];
        }

        return null;
    }

    /**
     * Remove the form element with the given name
     *
     * @param string $name
     * @return Quform
     */
    public function removeElement($name)
    {
        if (isset($this->_elements[$name])) {
            unset($this->_elements[$name]);
        }

        return $this;
    }

    /**
     * Get the elements and any errors they have
     *
     * @return array
     */
    public function getElementErrors()
    {
        $errors = array();

        foreach ($this->getElements() as $element) {
            $errors[$element->getFullyQualifiedName()] = array('label' => $element->getLabel(), 'errors' => $element->getErrors());
        }

        return $errors;
    }

    /**
     * Get the values of all fields
     *
     * @return array The values of all fields
     */
    public function getValues()
    {
        $values = array();

        foreach ($this->getElements() as $element) {
            $values[$element->getName()] = $element->getValue();
        }

        return $values;
    }

    /**
     * Get the value of a single field
     *
     * @param string $name The name of the field
     * @return mixed The value of the given field or null
     */
    public function getValue($name)
    {
        $value = null;
        $element = $this->getElement($name);

        if ($element instanceof Quform_Element) {
            $value = $element->getValue();
        }

        return $value;
    }

    /**
     * Get the value of a form element formatted in HTML
     *
     * @param string $name The unique element name
     * @param string $separator The separator to join array types
     * @return string
     */
    public function getValueHtml($name, $separator = '<br />')
    {
        $value = '';
        $element = $this->getElement($name);

        if ($element instanceof Quform_Element) {
            $value = $element->getValueHtml($separator);
        }

        return $value;
    }

    /**
     * Get the value of a form element formatted as plain text
     *
     * @param string $name The unique element name
     * @param string $separator The separator to join array types
     * @return string
     */
    public function getValuePlain($name, $separator = ', ')
    {
        $value = '';
        $element = $this->getElement($name);

        if ($element instanceof Quform_Element) {
            $value = $element->getValuePlain($separator);
        }

        return $value;
    }

    /**
     * Escape HTML special characters
     *
     * @param string $string
     * @param int $quoteStyle
     * @param boolean $doubleEncode
     */
    public static function escape($string, $quoteStyle = ENT_NOQUOTES, $doubleEncode = true)
    {
        if (QUFORM_HTMLSPECIALCHARS_DOUBLE_ENCODE) {
            return htmlspecialchars($string, $quoteStyle, QUFORM_CHARSET, $doubleEncode);
        } else {
            return htmlspecialchars($string, $quoteStyle, QUFORM_CHARSET);
        }
    }

    /**
     * Internal autoloader for spl_autoload_register().
     *
     * @param string $class
     */
    public static function autoload($class)
    {
        // Don't interfere with other autoloaders
        if (strpos($class, 'Quform') !== 0) {
            return false;
        }

        $path = dirname(__FILE__). '/' . str_replace('_', '/', $class).'.php';

        if (!file_exists($path)) {
            return false;
        }

        require_once $path;
    }

    /**
     * Log arguments to the PHP error log
     */
    public static function log()
    {
        foreach (func_get_args() as $arg) {
            ob_start();
            var_dump($arg);
            error_log(ob_get_clean());
        }
    }

    /**
     * Get the form submitter's IP address
     *
     * @return string
     */
    public static function getIPAddress()
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $ip;
    }

    /**
     * Get the URL of the referring page (the HTTP referer)
     *
     * If $link is true the value will be wrapped in an HTML link to the address
     * and the URL will be escaped.
     *
     * @param boolean $link
     * @return string
     */
    public static function getReferer()
    {
        if (isset($_SERVER['HTTP_REFERER']) && strlen($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['HTTP_REFERER'];
        }
    }

    /**
     * Configure autoloading of Quform classes
     */
    public static function registerAutoload()
    {
        spl_autoload_register(array('Quform', 'autoload'));
    }

    /**
     * Replace all placeholder values in a string with their
     * form value equivalents
     *
     * @param string $string
     * @return string
     */
    public function replacePlaceholderValues($string)
    {
        return preg_replace_callback('/%.+%/U', array($this, '_getPlaceholderValue'), $string);
    }

    /**
     * Extract the name and email address from the given parameter
     *
     * $spec can be:
     *
     * 1. An email address string
     * 2. A string with an element name placeholder such as %email%
     * 3. An array with the key being the name and value being the email address, both
     *    of these can also be element name placeholders
     *
     * @param mixed $spec
     * @return array
     */
    public function parseEmailRecipient($spec)
    {
        $emailValidator = new Quform_Validator_Email();
        $info = array(
            'name' => '',
            'email' => ''
        );

        if (is_string($spec)) {
            $info['email'] = $this->_getEmailValidAddress($spec);
        } else if (is_array($spec)) {
            $info['email'] = $this->_getEmailValidAddress(key($spec));

            $name = array_pop($spec);
            if (preg_match('/%.+%/', $name)) {
                $info['name'] = $this->replacePlaceholderValues($name);
            } else {
                $info['name'] = $name;
            }
        }

        return $info;
    }

    /**
     * Gets a valid email from the given string, it could be an email
     * address or a element placeholder
     *
     * @param $email
     * @return string
     */
    protected function _getEmailValidAddress($email)
    {
        $emailValidator = new Quform_Validator_Email();
        if ($emailValidator->isValid($email)) {
            return $email;
        } else if (preg_match('/^%.+%$/', $email)) {
            $email = $this->replacePlaceholderValues($email);
            if ($emailValidator->isValid($email)) {
                return $email;
            }
        }

        return '';
    }

    /**
     * Get the form value of a single placeholder
     *
     * @param string $matches
     * @return string The the form value
     */
    protected function _getPlaceholderValue($matches)
    {
        $match = $matches[0];

        $match = preg_replace('/(^%|%$)/', '', $match);

        $value = $this->getValue($match);

        if (is_array($value)) {
            $value = join(', ', $value);
        }

        return $value;
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
     * Extract the value by walking the array using given array path.
     *
     * Given an array path such as foo[bar][baz], returns the value of the last
     * element (in this case, 'baz').
     *
     * @param  array $value Array to walk
     * @param  string $arrayPath Array notation path of the part to extract
     * @return string
     */
    protected function _dissolveArrayValue($value, $arrayPath)
    {
        // As long as we have more levels
        while ($arrayPos = strpos($arrayPath, '[')) {
            // Get the next key in the path
            $arrayKey = trim(substr($arrayPath, 0, $arrayPos), ']');

            // Set the potentially final value or the next search point in the array
            if (isset($value[$arrayKey])) {
                $value = $value[$arrayKey];
            }

            // Set the next search point in the path
            $arrayPath = trim(substr($arrayPath, $arrayPos + 1), ']');
        }

        if (isset($value[$arrayPath])) {
            $value = $value[$arrayPath];
        }

        return $value;
    }

    /**
     * Set the form error message
     *
     * @param string $message
     * @return Quform
     */
    public function setError($message)
    {
        $this->_error = $message;
        return $this;
    }

    /**
     * Does the form have an error message?
     *
     * @return boolean
     */
    public function hasError()
    {
        return $this->_error != '';
    }

    /**
     * Clear the form error message
     * @return Quform
     */
    public function clearError()
    {
        $this->_error = '';
        return $this;
    }

    /**
     * Get the form error message
     *
     * @return string
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Strip slashes from the given value (recursive)
     *
     * @param mixed $value
     * @return mixed
     */
    public static function stripslashes($value)
    {
        if (is_array($value)) {
            $value = array_map(array('Quform', 'stripslashes'), $value);
        } else {
            $value = stripslashes($value);
        }
        return $value;
    }

    /**
     * Encode PHP data in JSON
     *
     * @param mixed $data The data to encode
     * @return string The JSON encoded response
     */
    public static function jsonEncode($data)
    {
        if (function_exists('json_encode')) {
            return json_encode($data);
        } else {
            if (!class_exists('Services_JSON')) {
                require_once QUFORM_ROOT . '/lib/JSON.php';
            }
            $json = new Services_JSON();
            return $json->encode($data);
        }
    }

    /**
     * Returns a new PHPMailer instance from the given settings
     *
     * @param array $smtp The SMTP settings
     * @return PHPMailer
     */
    public static function newPHPMailer($smtp = array())
    {
        $mailer = new PHPMailer(QUFORM_DEBUG);
        $mailer->CharSet = QUFORM_CHARSET;

        if (isset($smtp['host']) && strlen($smtp['host'])) {
            $mailer->isSMTP();
            $mailer->Host = $smtp['host'];

            if (isset($smtp['port']) && strlen($smtp['port'])) {
                $mailer->Port = abs((int) $smtp['port']);
            }

            if (isset($smtp['username']) && strlen($smtp['username'])) {
                $mailer->SMTPAuth = true;
                $mailer->Username = $smtp['username'];
            }

            if (isset($smtp['password']) && strlen($smtp['password'])) {
                $mailer->Password = $smtp['password'];
            }

            if (isset($smtp['encryption']) && in_array($smtp['encryption'], array('', 'tls', 'ssl'))) {
                $mailer->SMTPSecure = $smtp['encryption'];
            }
        }

        return $mailer;
    }
}