<?php
use Doctrine\Common\Collections\ArrayCollection;
use helper\DataFormatter;

/**
 * @Entity(repositoryClass="SigninObjectRepository") 
 * @Table(name="tbl_TerminObjekte")
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="Typ", type="string")
 * @DiscriminatorMap({"Meeting" = "Meeting"})
 **/
abstract class SigninObject {
	
	/** @Id @Column(name="ID",type="integer") @GeneratedValue */
	private $id;
	/** @Column(name="Name",type="string") */
	private $name;
	/** @Column(name="Beschreibung",type="text") */
	private $description;
	/** @Column(name="Start",type="datetime") */
	private $startDate;
	/** @Column(name="Ende",type="datetime") */
	private $endDate;
	/** @ManyToOne(targetEntity="Member") *
	 * @JoinColumn(name="FK_Verantwortlicher", referencedColumnName="Mitglied_ID") */
	private $responsible;
	/** @ManyToOne(targetEntity="Location")
	 * @JoinColumn(name="FK_Ort", referencedColumnName="ID_Ort") */	
	private $location;
	
	public function __construct() {
		$this->name = "";
		$this->description = "";
		$this->responsible = null;
		$this->location = null;
	}	
	
	public function __clone() {
		$this->id = null;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getStartTime(){
		return $this->startDate->format("H:i");
	}	
	
	public function getStartTimeICSFormat(){
		return $this->startDate->format("His");
	}	
	
	public function getStartDateICSFormat(){
		return $this->startDate->format("Ymd");
	}	
	
	public function getStartDate(){
		return $this->startDate->format("d.m.Y");
	}
	
	public function getStart(){
		if($this->startDate == null)
			return "";
		return $this->startDate->format("d.m.Y H:i");
	}
	
	/**
	 * Used to compare date
	 */
	public function getStartSQL(){
		return $this->startDate->format("Y-m-d H:i:s");
	}	
	
	public function getYear(){
		return $this->startDate->format(DataFormatter::YEAR);
	}
	
	public function getMonth(){
		return $this->startDate->format(DataFormatter::MONTH);
	}

    public function getDay(){
        return $this->startDate->format(DataFormatter::DAY);
    }

    public function getHour(){
        return $this->startDate->format(DataFormatter::HOUR);
    }

    public function getMinute(){
        return $this->startDate->format(DataFormatter::MINUTE);
    }
	
	public function getWeekDay(){
		return $this->startDate->format("D");
	}	
	
	/**
	 * Used to compare date
	 */	
	public function getEndSQL(){
		if(!empty($this->endDate))
			return $this->endDate->format("Y-m-d H:i:s");
		return $this->startDate->format("Y-m-d") . "235900";
	}	
	
	public function getEndTime(){
		return $this->endDate->format("H:i");
	}	
	
	public function getEndTimeICSFormat(){
		if(empty($this->endDate) || $this->endDate == "0000-00-00 00:00:00")
			return "235900";
		$time = $this->endDate->format("His");
		return $time;
	}	
	
	public function getEndDateICSFormat(){
		if(empty($this->endDate) || $this->endDate == "0000-00-00 00:00:00")
			return $this->getStartDateICSFormat();
		$date = $this->endDate->format("Ymd");
		return $date;		
	}	
	
	public function getEndDate(){
		return $this->endDate->format("d.m.Y");
	}
	
	public function getEnd(){
		if($this->startDate == null)
			return "";		
		return $this->endDate->format("d.m.Y H:i");
	}	
	
	public function getStartEnd(){
		if(!empty($this->endDate) && $this->startDate != $this->endDate){
			if($this->getStartDate() == $this->getEndDate()){
				return $this->getStartDate() . " " . $this->getStartTime() . " - " . $this->getEndTime();
			}
			return $this->getStartDate() . " " . $this->getStartTime() . " - " . $this->getEndDate() . " " . $this->getEndTime();
		}
		return $this->getStart();
	}
	
	public function setStart(DateTime $startDate){
		$this->startDate = $startDate;
	}	
	
	public function setEnd(DateTime $endDate){		
		$this->endDate = $endDate;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($name){
		$this->name = $name;
	}
	
	public function getDescription(){
		return $this->description;
	}
	
	public function setDescription($description){
		$this->description = $description;
	}
	
	public function inFutur(){
		$actualDate = Helper::getActualDate();
		if($actualDate->format('Y-m-d H:i:s') <= $this->getStartSQL()){
			return true;
		}
		return false;
	}
	
	/**
	 * @return Location
	 */
	public function getLocation(){
		return $this->location;
	}
	
	public function setLocation(Location $location){
		$this->location = $location;
	}
	
	/**
	 * @return Member
	 */
	public function getResponsible(){
		return $this->responsible;
	}
	
	public function setResponsible(Member $responsible){
		$this->responsible = $responsible;
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