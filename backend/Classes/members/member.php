<?php
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @Entity(repositoryClass="MemberRepository") 
 * @Table(name="tbl_Mitglieder")
 **/
class Member {
	
	/** @Id @Column(name="Mitglied_ID", type="integer") @GeneratedValue **/
	private $id;
	/** @Column(name="Anrede", type="string") **/
	private $title;	
	/** @Column(name="Vorname", type="string") **/
	private $firstname;
	/** @Column(name="Nachname", type="string") **/
	private $surname;
	/** @Column(name="Strasse", type="string") **/
	private $street;
	/** @Column(name="Postfach", type="string") **/
	private $box;
	/** @Column(name="PLZ", type="string") **/
	private $zip;
	/** @Column(name="Ort", type="string") **/
	private $city;
	/** @Column(name="Land", type="string") **/
	private $country;	
	/** @Column(name="Tel_P", type="string") **/
	private $phone;
	/** @Column(name="Tel_G", type="string") **/
	private $businessPhone;	
	/** @Column(name="Tel_N", type="string") **/
	private $mobile;	
	/** @Column(name="`e-mail1`", type="string") **/
	private $mail1;
	/** @Column(name="`e-mail2`", type="string") **/
	private $mail2;
	/** @Column(name="Geburtsdatum", type="datetime") **/
	private $birthday;		
	/** @Column(name="Beruf", type="string") **/
	private $profession;
	/** @Column(name="Todesdatum", type="datetime", nullable=true) **/
	private $deathDate;		
	/** @Column(name="Besonderes", type="text", nullable=true) **/
	private $remark;
	
	 /**
     * @OneToOne(targetEntity="Account", mappedBy="member")
     */	
	private $account;

    /**
     * @OneToMany(targetEntity="SigninEntry", mappedBy="member", fetch="LAZY") */
    private $signEntries;

	public function __construct() {
		$this->id = null;
		$this->firstname = "";
		$this->surname = "";
		$this->street = "";
		$this->zip = "";
		$this->city = "";
		$this->mail1 = "";
		$this->mail2 = "";
		$this->remark = "";
		$this->birthday = null;
		$this->deathDate = null;

		$this->account = null;
	}
	  
	public function getId(){
		return $this->id;
	}
	
	public function setId($id){
		$this->id = $id;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function setTitle($title){
		$this->title = $title;
	}		
	
	public function getFirstname(){
		return $this->firstname;
	}
	
	public function setFirstname($firstname){
		$this->firstname = $firstname;
	}	
	
	public function getSurname(){
		return $this->surname;
	}
	
	public function setSurname($surname){
		$this->surname = $surname;
	}		
	
	public function getName(){
		return $this->getFirstname() . " " . $this->getSurname();
	}
	
	public function getStreet(){
		return $this->street;
	}
	
	public function setStreet($street){
		$this->street = $street;
	}	
	
	public function getZip(){
		return $this->zip;
	}
	
	public function setZip($zip){
		$this->zip = $zip;
	}	
	
	public function getCity(){
		return $this->city;
	}
	
	public function setCity($city){
		$this->city = $city;
	}
	
	public function getPhoneNumber(){
		return $this->phone;
	}
	
	public function setPhoneNumber($phone){
		$this->phone = $phone;
	}	
	
	public function getBusinessPhoneNumber(){
		return $this->businessPhone;
	}
	
	public function setBusinessPhoneNumber($businessPhone){
		$this->businessPhone = $businessPhone;
	}	
	
	public function getMobileNumber(){
		return $this->mobile;
	}
	
	public function setMobileNumber($mobile){
		$this->mobile = $mobile;
	}					
	
	public function getMail1(){
		return $this->mail1;
	}
	
	public function setMail1($mail1){
		$this->mail1 = $mail1;
	}		
	
	public function getMail2(){
		return $this->mail2;
	}
	
	public function setMail2($mail2){
		$this->mail2 = $mail2;
	}			
	
	public function getYear(){
        if($this->birthday !== null) {
            return $this->birthday->format("Y");
        }
        return null;
	}
	
	public function getBirthday(){
        if($this->birthday !== null) {
		    return $this->birthday->format("d.m.Y");
        }
        return null;
	}

	public function getProfession(){
		return $this->profession;
	}
	
	public function setProfession($profession){
		$this->profession = $profession;
	}	
	
	public function isStudent(){
		if (preg_match("/Schüler/", $this->profession) || preg_match("/Lehrling/", $this->profession)) {
			return true;
		}
		return false;
	}
	
	public function getDeathDate(){
		return $this->deathDate->format("d.m.Y");
	}
	
	public function setDeathDate(DateTime $deathDate){
		$this->deathDate = $deathDate;
	}	
	
	public function getRemark(){
		return $this->remark;
	}
	
	public function setRemark($remark){
		$this->remark = $remark;
	}

    /**
     * @return Account
     */
	public function getAccount(){
		return $this->account;
	}		
	
	public function setAccount(Account $account){
		$this->account = $account;
	}
	
	public function getJsonData(){
		return array("id" => $this->getId(), "firstname" => $this->getFirstname(), "surname" => $this->getSurname(), "city" => $this->getCity());
     } 				
   
}
?>