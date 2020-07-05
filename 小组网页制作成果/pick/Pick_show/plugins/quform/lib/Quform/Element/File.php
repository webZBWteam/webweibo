<?php

/**
 * Quform_Element_File
 *
 * A Quform file element
 *
 * @package Quform
 * @subpackage Element
 * @copyright Copyright (c) 2009-2015 ThemeCatcher (http://www.themecatcher.net)
 */
class Quform_Element_File extends Quform_Element
{
    /**
     * File upload validator
     * @var Quform_Validator_FileUpload
     */
    protected $_fileUploadValidator;

    /**
     * Whether or not to attach any uploads to the notification email
     * @var boolean
     */
    protected $_attach = true;

    /**
     * Whether or not to save any uploaded files to the server
     * @var boolean
     */
    protected $_save = true;

    /**
     * The path to save the uploaded files
     * @var string
     */
    protected $_savePath = '';

    /**
     * The URL to the upload save path
     * @var string
     */
    protected $_saveUrl = '';

    /**
     * The value
     * @var array
     */
    protected $_value = array();

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

        $this->_fileUploadValidator = new Quform_Validator_FileUpload(array(
            'name' => $this->getName()
        ));
        $this->addValidator($this->_fileUploadValidator);

        if (is_array($options)) {
            if (array_key_exists('allowedExtensions', $options)) {
                $this->_fileUploadValidator->setAllowedExtensions($options['allowedExtensions']);
            }

            if (array_key_exists('maximumFileSize', $options)) {
                $this->_fileUploadValidator->setMaxFileSize($options['maximumFileSize']);
            }

            if (array_key_exists('required', $options)) {
                $this->_fileUploadValidator->setRequired($options['required']);
            }

            if (array_key_exists('attach', $options)) {
                $this->setAttach($options['attach']);
            }

            if (array_key_exists('save', $options)) {
                $this->setSave($options['save']);
            }
        }
    }

    /**
     * Get this elements file upload validator
     *
     * @return Quform_Validator_FileUpload
     */
    public function getFileUploadValidator()
    {
        return $this->_fileUploadValidator;
    }

    /**
     * Is the uploaded file valid?
     *
     * @param string $value The value to check
     * @param array $context The other submitted form values
     * @return boolean True if valid, false otherwise
     */
    public function isValid($value, $context = null)
    {
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
     * Sets whether or not the uploaded files for this element
     * should be attached to the notification email
     *
     * @param boolean $flag
     * @return Quform_Element_File
     */
    public function setAttach($flag)
    {
        $this->_attach = (bool) $flag;
        return $this;
    }

    /**
     * Should the uploaded files be attached to the notification
     * email?
     *
     * @return boolean
     */
    public function getAttach()
    {
        return $this->_attach;
    }

    /**
     * Sets whether or not the uploaded files for this element should
     * be saved to the server
     *
     * @param boolean $flag
     * @return Quform_Element_File
     */
    public function setSave($flag)
    {
        $this->_save = (bool) $flag;
        return $this;
    }

    /**
     * Should the uploaded files be saved to the server?
     *
     * @return boolean
     */
    public function getSave()
    {
        return $this->_save;
    }

    /**
     * Set the upload save path for uploaded files, this should be
     * the absolute path on disk
     *
     * @param string $savePath
     * @return Quform_Element_File
     */
    public function setSavePath($savePath)
    {
        $this->_savePath = $savePath;
        return $this;
    }

    /**
     * Get the upload save path
     *
     * @return string
     */
    public function getSavePath()
    {
        return $this->_savePath;
    }

    /**
     * Does the element have a save path?
     *
     * @return boolean
     */
    public function hasSavePath()
    {
        return strlen($this->_savePath);
    }

    /**
     * Set the URL to the upload save path
     *
     * @param string $savePath
     * @return Quform_Element_File
     */
    public function setSaveUrl($saveUrl)
    {
        $this->_saveUrl = $saveUrl;
        return $this;
    }

    /**
     * Get the URL to the upload save path
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->_saveUrl;
    }

    /**
     * Does the element have a URL to the upload save path?
     *
     * @return boolean
     */
    public function hasSaveUrl()
    {
        return strlen($this->_saveUrl);
    }

    /**
     * Add an uploaded file to the element's value
     *
     * @param array
     * @return Quform_Element_File
     */
    public function addFile($file)
    {
        $this->_value[] = $file;
        return $this;
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
     *
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

        if (is_array($filteredValue)) {
            foreach ($filteredValue as $file) {
                if (isset($file['url']) && strlen($file['url'])) {
                    $value .= '<a href="' . $file['url'] . '">' . $file['filename'] . ' (' . self::formatFileSize($file['size']) . ')' . '</a>';
                } else {
                    $value .= $file['filename'] . ' (' . self::formatFileSize($file['size']) . ')';
                }
                $value .= $separator;
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

        if (is_array($filteredValue)) {
            foreach ($filteredValue as $file) {
                $value .= $file['filename'] . ' (' . self::formatFileSize($file['size']) . ')';
                if (isset($file['url']) && strlen($file['url'])) {
                    $value .= ' (' . $file['url'] . ')';
                }
                $value .= $separator;
            }
        }

        return $value;
    }

    /**
     * Get the human readable file size from the given bytes
     *
     * @param int $size
     * @return string
     */
    public static function formatFileSize($size)
    {
        if ($size == 0) {
            return 'n/a';
        }

        $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $sizes[$i]);
    }

    /**
     * Save an uploaded file
     *
     * @param string $uploadPath The uploads folder path on disk
     * @param string $uploadUrl The URL to the uploads folder
     * @param array $fileData
     * @param Quform_Element_File $element
     * @return false|array The array of new file data or false on failure
     */
    public static function saveUpload($uploadPath, $uploadUrl, $fileData, $element)
    {
        $savePath = $element->hasSavePath() ? $element->getSavePath() : $uploadPath;
        $savePath = rtrim(realpath($savePath), '/') . '/';
        $saveUrl = $element->hasSaveUrl() ? $element->getSaveUrl() : $uploadUrl;
        $saveUrl = strlen($saveUrl) ? rtrim($saveUrl, '/') . '/' : '';

        if (is_writeable($savePath)) {
            if (file_exists($savePath . $fileData['filename'])) {
                $fileData['filename'] = self::generateFilename($savePath, $fileData['filename']);
            }

            if (move_uploaded_file($fileData['path'], $savePath . $fileData['filename'])) {
                chmod($savePath . $fileData['filename'], 0644);
                $fileData['path'] = $savePath . $fileData['filename'];
                $fileData['url'] = strlen($saveUrl) ? $saveUrl . $fileData['filename'] : '';

                return $fileData;
            }
        }

        return false;
    }

    /**
     * Returns an array with information about the given path
     *
     * @param string $path
     * @return array
     */
    public static function pathinfo($path)
    {
        $pathInfo = array(
            'dirname' => '',
            'basename' => '',
            'filename' => '',
            'extension' => ''
        );

        // PHP <5.2 workaround
        if (!defined(PATHINFO_FILENAME) && strpos($path, '.') !== false) {
            $pathInfo['filename'] = substr($path, 0, strrpos($path, '.'));
        }

        return array_merge($pathInfo, pathinfo($path));
    }

    /**
     * Returns a santised filename from the given path
     *
     * @param string $path
     * @return string
     */
    public static function filterFilename($path)
    {
        $pathInfo = Quform_Element_File::pathinfo($path);
        $extension = $pathInfo['extension'];
        $filename = $pathInfo['filename'];

        $filenameFilter = new Quform_Filter_Filename();
        $filename = $filenameFilter->filter($filename);
        $filename = (strlen($filename)) ? $filename : 'upload';
        $filename = (strlen($extension)) ? "$filename.$extension" : $filename;

        return $filename;
    }

    /**
     * Generate a filename that does not already exist in the given path
     *
     * @param string $path Path to save the file
     * @param string $filename The filename
     * @return string
     */
    public static function generateFilename($path, $filename)
    {
        $count = 1;
        $newFilenamePath = $path . $filename;

        while (file_exists($newFilenamePath)) {
            $newFilename = $count++ . '_' . $filename;
            $newFilenamePath = $path . $newFilename;
        }

        return $newFilename;
    }
}