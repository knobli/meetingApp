<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="tbl_Accounts")
 **/
class Account {
	
	/** @Id @Column(name="Account_ID", type="integer") @GeneratedValue **/
	private $id;
	/** @Column(name="Username", type="string") **/
	private $username;
    /** @Column(name="SaltedPassword", type="string", nullable=true) **/
    private $saltedPassword;
	/** @OneToOne(targetEntity="Member")
	 * 	@JoinColumn(name="FK_Mitglied", referencedColumnName="Mitglied_ID") */	
	private $member;
	/** @Column(name="Erfasst", type="datetime") **/
	private $created;
	/** @Column(name="Last_Login", type="datetime", nullable=true) **/
	private $lastLogin;
	
	/**
     * @OneToMany(targetEntity="UserRightEntry", mappedBy="account", fetch="LAZY") */
	private $rightEntries;

	public function __construct() {
		$this->id = null;
		$this->username = "";
        $this->saltedPassword = "";
		$this->member = null;
		$this->created = Helper::getActualDate();
		$this->lastLogin = null;
		
		$this->rights = new ArrayCollection();
	}		

	public function getId(){
		return $this->id;
	}
	
	public function getUsername(){
		return $this->username;
	}
	
	public function setUsername($username){
		$this->username = $username;
	}	
	
	public function getPassword(){
        return $this->saltedPassword;
	}
	
	public function setPassword($password){
		$this->saltedPassword = $password;
	}

	public function getMember(){
		return $this->member;
	}

    public function setMember(Member $member){
        $this->member = $member;
    }
	
	public function getCreateDate(){
		return $this->created->format("d.m.Y");
	}
	
	public function getCreateDateTime(){
		return $this->created->format("d.m.Y H:i");
	}
	
	public function getLastLoginDate(){
		return $this->lastLogin->format("d.m.Y");
	}
	
	public function getLastLoginDateTime(){
		return $this->lastLogin->format("d.m.Y H:i");
	}	
	
	public function setLastLogin(DateTime $lastLogin){
		$this->lastLogin = $lastLogin;
	}

    /**
     * @return mixed
     */
    public function getRightEntries(){
        return $this->rightEntries;
    }
}
?>