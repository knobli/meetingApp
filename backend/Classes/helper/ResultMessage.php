<?php
/**
 * Created by PhpStorm.
 * User: knobli
 * Date: 16.11.2014
 * Time: 17:33
 */

namespace helper;


class ResultMessage {

    /**
     * @var ResultEnum
     */
    private $result;

    /**
     * @var string
     */
    private $message;

    function __construct($message, $result)
    {
        $this->message = $message;
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function getResult(){
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
     * @return bool
     */
    public function isInfo()
    {
        return ($this->result === ResultEnum::INFO) ? true : false;
    }

    public function getJsonData()
    {
        return array("result" => $this->result, "message" => $this->getMessage());
    }

} 