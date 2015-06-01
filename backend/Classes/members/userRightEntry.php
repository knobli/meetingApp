<?php

/**
 * @Entity
 * @Table(name="vtbl_Account_Recht")
 **/
class UserRightEntry {

    /**
     * @Id @Column(name="ID", type="integer") @GeneratedValue */
    private $id;
	/** @ManyToOne(targetEntity="UserRight")
	 * 	@JoinColumn(name="FK_Recht", referencedColumnName="ID_Recht") */
	private $userRight;
	/** @ManyToOne(targetEntity="Account", inversedBy="rightEntries")
	 * @JoinColumn(name="FK_Account", referencedColumnName="Account_ID") */
	private $account;

	public function __construct() {
		$this->userRight = null;
		$this->account = null;
	}

    /**
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @return UserRight
     */
	public function getUserRight(){
		return $this->userRight;
	}
	
	public function setUserRight(UserRight $userRight){
		$this->userRight = $userRight;
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

}
?>