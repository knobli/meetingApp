<?php
/**
 * @Entity(repositoryClass="SigninObjectRepository") 
 * @Table(name="tbl_Sitzungen")
 **/
class Meeting extends SigninObject {

	const CONST_TYPE = "Meeting";
	const CONST_TYPE_NR=8;
	const ATTACHMENT_PATH = "meeting/";
	private $type = "Sitzung";

	/** @Column(name="Anhang", type="string") **/
	private $attachment;
	
	public function __construct() {
		parent::__construct();
		$this->attachment = "";
	}
	
	public function getAttachment(){
		return $this->attachment;
	}

	public function getAttachmentPath(){
		return self::ATTACHMENT_PATH . $this->attachment;
	}				

	public function setAttachment($attachment){
		$this->attachment = $attachment;
	}
	
	/* abstract implementation */
	public function getTitle(){		
		return $this->getName();
	}

    public function getJsonData(){
        $parentVar = parent::getJsonData();
        $var = get_object_vars($this);
        foreach($var as &$value){
            if(is_array($value)){
                foreach($value as &$valueValue){
                    if(is_object($valueValue) && method_exists($valueValue,'getJsonData')){
                        $valueValue = $valueValue->getJsonData();
                    } else {
                        $valueValue = $valueValue;
                    }
                }
            } else if(is_object($value) && method_exists($value,'getJsonData')){
                $value = $value->getJsonData();
            } else {
                $value = $value;
            }
        }
        return array_merge ($parentVar, $var);
    }

}
?>
