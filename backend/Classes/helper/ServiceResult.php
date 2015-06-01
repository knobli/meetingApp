<?php
/**
 * Created by PhpStorm.
 * User: knobli
 * Date: 16.11.2014
 * Time: 17:17
 */

namespace helper;

class ServiceResult
{

    /**
     * @var ResultEnum
     */
    private $result;

    /**
     * @var array
     */
    private $messages;

    /**
     * @var mixed
     */
    private $payload;

    function __construct($result = null)
    {
        $this->result = $result;
        $this->messages = array();
    }

    /**
     * @param $message
     */
    public function addSuccessMessage($message)
    {
        $this->setSuccessResult();
        $this->addMessage(new ResultMessage($message, ResultEnum::SUCCESS));
    }

    /**
     * @param $message
     */
    public function addInfoMessage($message)
    {
        $this->addMessage(new ResultMessage($message, ResultEnum::INFO));
    }

    /**
     * @param $message
     */
    public function addWarningMessage($message)
    {
        $this->setWarningResult();
        $this->addMessage(new ResultMessage($message, ResultEnum::WARNING));
    }

    /**
     * @param $message
     */
    public function addErrorMessage($message)
    {
        $this->setErrorResult();
        $this->addMessage(new ResultMessage($message, ResultEnum::ERROR));
    }

    private function addMessage(ResultMessage $message)
    {
        $this->messages[] = $message;
    }

    public function merge(ServiceResult $serviceResult)
    {
        if ($serviceResult->isError()) {
            $this->setErrorResult();
        } elseif ($serviceResult->isWarning() && $this->isSuccess()) {
            $this->setWarningResult();
        } elseif ($this->result == null) {
            $this->result = $serviceResult->getResult();
        }
        foreach ($serviceResult->getMessages() as $message) {
            $this->addMessage($message);
        }
    }

    private function setSuccessResult()
    {
        if ($this->result === null) {
            $this->result = ResultEnum::SUCCESS;
        }
    }

    private function setWarningResult()
    {
        if ($this->result != ResultEnum::ERROR) {
            $this->result = ResultEnum::WARNING;
        }
    }

    private function setErrorResult()
    {
        $this->result = ResultEnum::ERROR;
    }


    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return ($this->result === ResultEnum::SUCCESS) ? true : false;
    }

    /**
     * @return bool
     */
    public function isWarning()
    {
        return ($this->result === ResultEnum::WARNING) ? true : false;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return ($this->result === ResultEnum::ERROR) ? true : false;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param mixed $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    public function getJsonData()
    {
        $messagesJson = array();
        foreach ($this->getMessages() as $message) {
            $messagesJson[] = $message->getJsonData();
        }
        return array("success" => $this->getResult(), "error_message" => $messagesJson);
    }

    public function getMessagesAsHtml()
    {
        $html = "";
        foreach ($this->messages as $message) {
            $html .= $this->createMessage($message->getResult(), $message->getMessage());
        }
        return $html;
    }

    private function createMessage($type, $message)
    {
        $alertClass = "";
        if ($type === ResultEnum::SUCCESS) {
            $alertClass = "alert-success";
        } else if ($type === ResultEnum::WARNING) {
            $alertClass = "";
        } else if ($type === ResultEnum::ERROR) {
            $alertClass = "alert-error";
        } else if ($type === ResultEnum::INFO) {
            $alertClass = "alert-info";
        }
        return "<div class='alert " . $alertClass . "'>" . $message . "</div>";
    }

}