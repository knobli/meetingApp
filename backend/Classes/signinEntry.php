<?php
/**
 * @Entity(repositoryClass="SigninEntryRepository") 
 * @Table(name="vtbl_Mitglied_Termin")
 **/
class SigninEntry {
	
	const SING_IN = 1;
	const SING_OUT = 0;
	const NO_STATUS = 3;
	
	const ADMIN_DESCRITPION = "__ADMIN__";

	/** @Id @ManyToOne(targetEntity="Member") *
	 * @JoinColumn(name="FK_Mitglied", referencedColumnName="Mitglied_ID") */	
	private $member;
	/** @Id @ManyToOne(targetEntity="SigninObject") *
	 * @JoinColumn(name="FK_Termin", referencedColumnName="ID") */
	private $signinObject;
	/** @Column(name="Bemerkung",type="text") */
	private $description;
	/** @Column(name="Status",type="integer") */
	private $status;

	public function __construct(Member $member, SigninObject $signinObject, $status) {
		$this->member = $member;
		$this->signinObject = $signinObject;
		$this->description = "";
		$this->status = $status;
	}	

	/**
	 * @return Member
	 */
	public function getMember(){
		return $this->member;
	}
	
	/**
	 * @return SigninObject
	 */
	public function getSigninObject(){
		return $this->signinObject;
	}

	public function setDescription($description){
		$this->description = $description;
	}
	
	public function getDescription(){
		return trim($this->description);
	}
	
	public function getStatus(){
		return $this->status;
	}
	
	public function setStatus($status){
		$this->status = $status;
	}

	public function getJsonData(){
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
		return $var;
     } 	
		
}
?>