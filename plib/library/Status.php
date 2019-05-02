<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 10.04.2019
 * Time: 13:05
 */

class Modules_Cagent_Status
{
    /**
     * @var boolean
     */
    protected $status;
    protected $successText = '';
    protected $errorText = '';

    /**
     * Modules_Cagent_Status constructor.
     * @param $status
     * @param $text
     */
    public function __construct($status, $text)
    {
        $this->status = $status;

        if ($status) {
            $this->setSuccessText($text);
        } else {
            $this->setErrorText($text);
        }
    }

    public function success()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getSuccessText()
    {
        return $this->successText;
    }

    /**
     * @param string $successText
     */
    public function setSuccessText($successText)
    {
        $this->successText = trim($successText);
    }

    /**
     * @return string
     */
    public function getErrorText()
    {
        return $this->errorText;
    }

    /**
     * @param string $errorText
     */
    public function setErrorText($errorText)
    {
        $this->errorText = trim($errorText);
    }


}