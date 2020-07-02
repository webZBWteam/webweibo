<?php

/**
 * Quform_Validator_Recaptcha
 *
 * Checks the given reCAPTCHA solution is correct
 *
 * @package Quform
 * @subpackage Validator
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Validator_Recaptcha extends Quform_Validator_Abstract
{
    /**
     * reCAPTCHA secret key
     * @var string
     */
    protected $_secretKey;

    /**
     * Error message templates
     * @var array
     */
    protected $_messageTemplates = array(
        'missing-input-secret' => 'The secret parameter is missing',
        'invalid-input-secret' => 'The secret parameter is invalid or malformed',
        'missing-input-response' => 'The response parameter is missing',
        'invalid-input-response' => 'The response parameter is invalid or malformed',
        'error' => 'An error occurred, please try again'
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
            if (array_key_exists('secretKey', $options)) {
                $this->_secretKey = $options['secretKey'];
            }
        }
    }

    /**
     * Checks the reCAPTCHA answer
     *
     * @param $value The value to check
     * @return boolean True if valid false otherwise
     */
    public function isValid($value)
    {
        if ($this->_secretKey == null) {
            throw new Exception('reCAPTCHA secret key is required');
        }

        $params = array(
            'secret' => $this->_secretKey,
            'response' => isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '',
            'remoteip' => Quform::getIPAddress()
        );

        $qs = http_build_query($params, '', '&');
        $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?' . $qs);
        $response = json_decode($response, true);

        if (!is_array($response) || !isset($response['success'])) {
            $this->addMessage($this->_messageTemplates['error']);
            return false;
        }

        if (!$response['success']) {
            if (isset($response['error-codes']) && is_array($response['error-codes']) && count($response['error-codes'])) {
                foreach ($response['error-codes'] as $error) {
                    if (array_key_exists($error, $this->_messageTemplates)) {
                        $message = $this->_messageTemplates[$error];
                    } else {
                        $message = $this->_messageTemplates['invalid-input-response'];
                    }

                    $this->addMessage($message);
                }
            } else {
                $this->addMessage($this->_messageTemplates['error']);
            }

            return false;
        }

        return true;
    }
}