<?php
/**
 * @Entity @Table(name="tbl_Rechte")
 **/
class UserRight {
	
	/** @Id @Column(name="ID_Recht", type="integer") @GeneratedValue **/
	private $id;
	/** @Column(name="Recht", type="string") **/
	private $name;

	public function __construct($name) {
		$this->name = $name;
	}	
	
	public function getId(){
		return $this->id;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($name){
		$this->name = $name;
	}
		
}
?>