<?php

/**
 * Quform_Validator_FileUpload
 *
 * Validates a file upload against the given settings
 *
 * @package Quform
 * @subpackage Validator
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Validator_FileUpload extends Quform_Validator_Abstract
{
    /**
     * The name of the element to check in the $_FILES array
     * @var string
     */
    protected $_name;

    /**
     * Allowed file extensions
     * @var array
     */
    protected $_allowedExtensions = array();

    /**
     * Maximum file size in bytes
     *
     * Default is 10MB (10485760 bytes)
     * @var int|float
     */
    protected $_maximumFileSize = 10485760;

    /**
     * Required flag
     * @var boolean
     */
    protected $_required = false;

    /**
     * How many files are required for a multiple upload element, 0 = all
     * @var int
     */
    protected $_requiredCount = 0;

    /**
     * Error message templates. Placeholders (varies):
     *
     * %s = the filename
     * %d = the number of required files
     *
     * @var array
     */
    protected $_messageTemplates = array(
        'x_required' => 'At least %d files are required',
        'one_required' => 'At least 1 file is required',
        'required' => 'This field is required',
        'x_too_big' => "'%s' exceeds the maximum allowed file size",
        'too_big' => 'File exceeds the maximum allowed file size',
        'x_only_partial' => "'%s' was only partially uploaded",
        'only_partial' => 'File was only partially uploaded',
        'no_file' => 'No file was uploaded',
        'missing_temp_folder' => 'Missing a temporary folder',
        'failed_to_write' => 'Failed to write file to disk',
        'stopped_by_extension' => 'File upload stopped by extension',
        'x_bad_type' => "File type of '%s' is not allowed",
        'bad_type' => 'File type is not allowed',
        'x_not_uploaded' => "File '%s' is not an uploaded file",
        'not_uploaded' => 'File is not an uploaded file',
        'unknown' => 'Unknown upload error'
    );

    /**
     * Constructor
     *
     * A string with the name of the element or an array of
     * options with a name key is required
     *
     * @param string|array $options
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        if (is_string($options)) {
            $this->setName($options);
        } elseif (is_array($options)) {
            if (array_key_exists('name', $options)) {
                $this->setName($options['name']);
            }

            if (array_key_exists('allowedExtensions', $options)) {
                $this->setAllowedExtensions($options['allowedExtensions']);
            }

            if (array_key_exists('maximumFileSize', $options)) {
                $this->setMaximumFileSize($options['maximumFileSize']);
            }

            if (array_key_exists('required', $options)) {
                $this->setRequired($options['required']);
            }
        }
    }

    /**
     * Returns true if and only if the uploaded file is free of errors
     *
     * @param   string   $value  Ignored in this validator
     * @return  boolean
     */
    public function isValid($value)
    {
        if (!(is_string($this->_name) && strlen($this->_name) > 0)) {
            throw new Exception('Name is required');
        }

        if (!array_key_exists($this->_name, $_FILES) || !is_array($_FILES[$this->_name])) {
            if ($this->getRequired()) {
                $this->addMessage($this->_messageTemplates['required']);
                return false;
            } else {
                return true;
            }
        }

        $file = $_FILES[$this->_name];
        if (is_array($file['error'])) {
            // Process multiple upload field
            $validFileCount = 0;

            foreach ($file['error'] as $key => $error) {
                if ($error === UPLOAD_ERR_OK) {
                    // The file uploaded OK

                    if (!$this->_isUploadedFile($file['tmp_name'][$key])) {
                        // The file is not an uploaded file - possibly an attack
                        $this->addMessage($this->_getFileUploadError(QUFORM_UPLOAD_ERR_NOT_UPLOADED, $file['name'][$key]));
                        return false;
                    }

                    if ($this->_maximumFileSize > 0 && $file['size'][$key] > $this->_maximumFileSize) {
                        // The file is larger than the size allowed by the settings
                        $this->addMessage($this->_getFileUploadError(QUFORM_UPLOAD_ERR_FILE_SIZE, $file['name'][$key]));
                        return false;
                    }

                    $pathInfo = pathinfo($file['name'][$key]);
                    $extension = strtolower($pathInfo['extension']);

                    if (count($this->_allowedExtensions) > 0 && !in_array($extension, $this->_allowedExtensions)) {
                        // The file extension is not allowed
                        $this->addMessage($this->_getFileUploadError(QUFORM_UPLOAD_ERR_TYPE, $file['name'][$key]));
                        return false;
                    }

                    $validFileCount++;
                } elseif ($error === UPLOAD_ERR_NO_FILE) {
                    continue;
                } else {
                    $this->addMessage($this->_getFileUploadError($error, $file['name'][$key]));
                    return false;
                }
            }

            if ($this->getRequired()) {
                $requiredCount = $this->getRequiredCount();

                if ($requiredCount > 0) {
                    if ($requiredCount > $validFileCount) {
                        if ($requiredCount > 1) {
                            $this->addMessage(sprintf($this->_messageTemplates['x_required'], $requiredCount));
                        } else {
                            $this->addMessage($this->_messageTemplates['one_required']);
                        }
                        return false;
                    }
                } else {
                    if ($validFileCount == 0) {
                        $this->addMessage($this->_messageTemplates['required']);
                        return false;
                    }
                }
            }
        } else {
            // Process single upload field
            if ($file['error'] === UPLOAD_ERR_OK) {
                // The file uploaded OK

                if (!$this->_isUploadedFile($file['tmp_name'])) {
                    // The file is not an uploaded file - possibly an attack
                    $this->addMessage($this->_getFileUploadError(QUFORM_UPLOAD_ERR_NOT_UPLOADED));
                    return false;
                }

                if ($this->_maximumFileSize > 0 && $file['size'] > $this->_maximumFileSize) {
                    // The file is larger than the size allowed by the element settings
                    $this->addMessage($this->_getFileUploadError(QUFORM_UPLOAD_ERR_FILE_SIZE));
                    return false;
                }

                $pathInfo = pathinfo($file['name']);
                $extension = strtolower($pathInfo['extension']);

                if (count($this->_allowedExtensions) > 0 && !in_array($extension, $this->_allowedExtensions)) {
                    // The file extension is not allowed
                    $this->addMessage($this->_getFileUploadError(QUFORM_UPLOAD_ERR_TYPE));
                    return false;
                }
            } elseif ($file['error'] === UPLOAD_ERR_NO_FILE) {
                if ($this->getRequired()) {
                    $this->addMessage($this->_messageTemplates['required']);
                    return false;
                }
            } else {
                $this->addMessage($this->_getFileUploadError($file['error'], $file['name']));
                return false;
            }
        }

        return true;
    }

    /**
     * Set the name of the element to validate in $_FILES
     *
     * @param string $name
     * @return Quform_Validator_FileUpload
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Get the name of the element
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set the allowed extensions, an array of lowercase
     * extensions e.g. array('jpg', 'jpeg', 'gif'). An empty
     * array means all file extensions are allowed
     *
     * @param array $allowedExtensions
     * @return Quform_Validator_FileUpload
     */
    public function setAllowedExtensions(array $allowedExtensions)
    {
        foreach ($allowedExtensions as &$allowedExtension) {
            $allowedExtension = strtolower($allowedExtension);
        }

        $this->_allowedExtensions = $allowedExtensions;
        return $this;
    }

    /**
     * Get the allowed extensions
     *
     * @return array
     */
    public function getAllowedExtensions()
    {
        return $this->_allowedExtensions;
    }

    /**
     * Set the maximum file size in bytes
     *
     * @param int|float $maximumFileSize
     * @return Quform_Validator_FileUpload
     */
    public function setMaximumFileSize($maximumFileSize)
    {
        $this->_maximumFileSize = $maximumFileSize;
        return $this;
    }

    /**
     * Get the maximum file size in bytes
     *
     * @return int|float
     */
    public function getMaximumFileSize()
    {
        return $this->_maximumFileSize;
    }

    /**
     * Set whether the file is required or not
     *
     * @param boolean $flag
     * @return Quform_Validator_FileUpload
     */
    public function setRequired($flag = true)
    {
        $this->_required = (bool) $flag;
        return $this;
    }

    /**
     * Get whether the file is required or not
     *
     * @return boolean
     */
    public function getRequired()
    {
        return $this->_required;
    }

    /**
     * Set the count for required files for a multiple upload field
     *
     * @param int $count
     * @return Quform_Validator_FileUpload
     */
    public function setRequiredCount($count)
    {
        $this->_requiredCount = $count;
        return $this;
    }

    /**
     * Get the count for required files for a multiple upload field
     *
     * @return int
     */
    public function getRequiredCount()
    {
        return $this->_requiredCount;
    }

    /**
     * Get the error message corresponding to the error code generated by
     * PHP file uploads and this validator
     *
     * @param int $errorCode The error code
     * @param string $filename (optional) The filename to add to the message
     * @return string The error message
     */
    protected function _getFileUploadError($errorCode, $filename = '')
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
            case QUFORM_UPLOAD_ERR_FILE_SIZE:
                if (strlen($filename) > 0) {
                    $message = sprintf($this->_messageTemplates['x_too_big'], $filename);
                } else {
                    $message = $this->_messageTemplates['too_big'];
                }
                return $message;
            case UPLOAD_ERR_PARTIAL:
                if (strlen($filename) > 0) {
                    $message = sprintf($this->_messageTemplates['x_only_partial'], $filename);
                } else {
                    $message = $this->_messageTemplates['only_partial'];
                }
                return $message;
            case UPLOAD_ERR_NO_FILE:
                return $this->_messageTemplates['no_file'];
            case UPLOAD_ERR_NO_TMP_DIR:
                return $this->_messageTemplates['missing_temp_folder'];
            case UPLOAD_ERR_CANT_WRITE:
                return $this->_messageTemplates['failed_to_write'];
            case UPLOAD_ERR_EXTENSION:
                return $this->_messageTemplates['stopped_by_extension'];
            case QUFORM_UPLOAD_ERR_TYPE:
                if (strlen($filename) > 0) {
                    $message = sprintf($this->_messageTemplates['x_bad_type'], $filename);
                } else {
                    $message = $this->_messageTemplates['bad_type'];
                }
                return $message;
            case QUFORM_UPLOAD_ERR_NOT_UPLOADED:
                if (strlen($filename) > 0) {
                    $message = sprintf($this->_messageTemplates['x_not_uploaded'], $filename);
                } else {
                    $message = $this->_messageTemplates['not_uploaded'];
                }
                return $message;
            default:
                return $this->_messageTemplates['unknown'];
        }
    }

    /**
     * Wrapper around is_uploaded_file for overriding in unit tests
     *
     * @param   string   $filename  The path to the file
     * @return  boolean             Returns true if the given filename is an uploaded file
     */
    protected function _isUploadedFile($filename)
    {
        return is_uploaded_file($filename);
    }
}