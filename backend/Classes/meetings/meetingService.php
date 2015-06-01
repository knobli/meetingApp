<?php
use Doctrine\Common\Collections\ArrayCollection;
use Helper\ServiceResult;
use MailItems\MailBody;
class MeetingService {
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $entityManager;
	
	public function __construct(Doctrine\ORM\EntityManager $entityManager){
	    $this->entityManager = $entityManager;
	}

    public function saveOrUpdate(Meeting $meeting, $finalMemberList){
        /** @var ServiceResult $serviceResult */
        $serviceResult = $this->validateMeeting($meeting, $finalMemberList);
        $meetingId = $meeting->getId();
        if($serviceResult->isSuccess()) {
            try {
                $this->entityManager->persist($meeting);
                $this->entityManager->flush();
                $this->addMembers($meeting,$finalMemberList);
                if ($meetingId === null) {
                    $serviceResult->addSuccessMessage("Sitzung wurde erstellt.");
                } else {
                    $serviceResult->addSuccessMessage("Sitzung wurde angepasst.");
                }
            } catch (Exception $e) {
                if ($meetingId === null) {
                    $serviceResult->addErrorMessage("Sitzung konnte nicht erstellt werden.");
                } else {
                    $serviceResult->addErrorMessage("Sitzung konnte nicht angepasst werden.");
                }
                Logger::getLogger()->logError("Could not save meeting: " . $e->getMessage());
            }
        }
        return $serviceResult;
    }

    /**
     * @param Meeting $meeting
     * @return ServiceResult
     */
    private function validateMeeting(Meeting $meeting, $finalMemberList){
        $serviceResult = new ServiceResult(\helper\ResultEnum::SUCCESS);
        if ($meeting->getName() === ""){
            $serviceResult->addErrorMessage("Kein Betreff angegeben.");
        }
        if ($meeting->getStart() === ""){
            $serviceResult->addErrorMessage("Keine Startzeit angegeben.");
        }
        if ($meeting->getResponsible() === null){
            $serviceResult->addErrorMessage("Kein Verantwortlicher angegeben.");
        }
        if ($meeting->getLocation() === null){
            $serviceResult->addErrorMessage("Kein Ort angegeben.");
        }
        if (count($finalMemberList) === 0){
            $serviceResult->addErrorMessage("Kein Mitglieder ausgewählt.");
        }
        return $serviceResult;
    }

	public function addMembers(Meeting $meeting, $memberList){
		$signinEntryService=new SigninEntryService($this->entityManager);
		
		if($meeting->getResponsible() != null){
			$responsibleId=$meeting->getResponsible()->getId();
			if (!in_array($responsibleId, $memberList)){
				$memberList[]=$responsibleId;
			}		
		}	
		
		$members=array();
		foreach($memberList as $memberId){
			$member=$this->entityManager->find('Member', $memberId);
			if($member != null){
				$members[]=$member;
			}
		}
		
		$assignedMemberIds=array();
		if($meeting->getId() != null){
			$assignedMembers=$this->entityManager->getRepository('Member')->getMemberForSignInObject($meeting);
			foreach($assignedMembers as $assignedMember){
				$assignedMemberIds[]=$assignedMember->getId();
			}
		}
		
		$matchedMembers=array();
		foreach ($members as $member){
    		if(!in_array($member->getId(),$assignedMemberIds)){
    			$signinEntryService->subscriptionForSigninObject($meeting, $member, "", SigninEntry::NO_STATUS);
			} else {
				$matchedMembers[$member->getId()]=$member;
			}
		}
		
		foreach($assignedMembers as $assignedMember){
			if(!array_key_exists($assignedMember->getId(), $matchedMembers)){
				$signinEntryService->removeSubscription($meeting, $assignedMember);
			}
		}
	}	 
}
	