<?php
class SigninEntryService {
	
	const ACTION_IN = 1;
	const ACTION_OUT = 2;
	const ACTION_DEL = 3;
	const ACTION_IN_AND_OUT = 4;
	const ACTION_REMARK = 5;
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $entityManager;
	
	public function __construct(Doctrine\ORM\EntityManager $entityManager){
	    $this->entityManager = $entityManager;
	}
	
	public function canChangeRemark(SigninObject $signinObject, Member $member, $adminOverwrite = false){
		$actualMember=getMitglied();
		if($adminOverwrite){
			if($this->hasAdminRights($signinObject, $member)){
				return true;
			}
		}
		if($member != $actualMember){
			return "Falscher User";
		}
		return true;	
	}

	public function hasAdminRights(SigninObject $signinObject, Member $member){
		$actualMember=getMitglied();
		if($signinObject->getResponsible() == $actualMember){
			return true;
		}
		$rightService=new UserRightService($this->entityManager);
		if($signinObject instanceof Meeting){
			$right=$this->entityManager->getRepository('UserRight')->findOneByName("Sitzung_mod");
			if($rightService->hasRight($actualMember, $right)){									
				return true;
			}
		}	
	}
	
	public function canSubscripe(SigninObject $signinObject, Member $member, $adminOverwrite = false){
		if(($canChangeRemark = $this->canChangeRemark($signinObject, $member, $adminOverwrite)) !== true){
			return $canChangeRemark;
		}	
		if($adminOverwrite){
			if($this->hasAdminRights($signinObject, $member)){
				return true;
			}
		}		
		if(!$this->isAssigned($signinObject, $member)){
			return "Keine Berechtigung";
		}
		$actualDate = Helper::getActualDate();
		if($signinObject->getStartSQL() < $actualDate->format("Y-m-d H:i:s")){
			return "Anmelden nicht mehr m&ouml;glich";
		}
		return true;		
	}
	
	/**
	 * @return array(Member)
	 */
	public function isAssigned(SigninObject $signinObject, Member $member){
		$signinEntryRepo = $this->entityManager->getRepository('SigninEntry');
		$object = $signinEntryRepo->findOneBy(array("signinObject" => $signinObject, "member" => $member));
	    if($object != null)
			return true;
		return false;
	}

	
	public function subscriptionForSigninObject(SigninObject $signinObject, Member $member, $description, $status){
		if($status == SigninEntry::NO_STATUS && !($signinObject instanceof Meeting)){
			$this->removeSubscription($signinObject, $member);
			return;
		}
		$descInsert=$description;
		$descUpdate=$description;
		if($description == SigninEntry::ADMIN_DESCRITPION){
			$descInsert="Durch Admin hinzugefügt";
			$descUpdate="Durch Admin geändert";
		}
		$signinEntryRepo=$this->entityManager->getRepository('SigninEntry');
		$entry = $signinEntryRepo->findOneBy(array("member" => $member, "signinObject" => $signinObject));
		if($entry == null){
			$entry = new SigninEntry($member, $signinObject, $status);
			$entry->setDescription($descInsert);
			$this->entityManager->persist($entry);
			$this->entityManager->flush();
		} else if($entry->getStatus() != $status){
			$entry->setDescription($descUpdate);
			$entry->setStatus($status);
			$this->entityManager->persist($entry);
			$this->entityManager->flush();
		}
	}

	public function removeSubscription(SigninObject $signinObject, Member $member){
		$signinEntryRepo=$this->entityManager->getRepository('SigninEntry');
		$entry = $signinEntryRepo->findOneBy(array("member" => $member, "signinObject" => $signinObject));
		if($entry != null){
			$this->entityManager->remove($entry);
			$this->entityManager->flush();
		}
	}	
		
}
?>
