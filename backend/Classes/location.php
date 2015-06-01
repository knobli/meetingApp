<?php
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @Entity(repositoryClass="LocationRepository") 
 * @Table(name="tbl_Orte")
 **/
class Location {
	
	/** @Id @Column(name="ID_Ort", type="integer") @GeneratedValue **/
	private $id;
	/** @Column(name="Name", type="string") **/
	private $name;
	
	/**
     * @OneToMany(targetEntity="SigninObject", mappedBy="location", fetch="LAZY") */
	private $signInObjects;

	public function __construct($name) {
		$this->name = $name;
		
		$this->signInObjects = new ArrayCollection();
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
	
	public function getJsonData(){
		$vars["id"]=$this->id;
		$vars["name"]=$this->name;
		return $vars;
     } 	
		
}
?>