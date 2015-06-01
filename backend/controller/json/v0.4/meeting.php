<?php
require 'generalController.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
        $serviceResult = new \helper\ServiceResult();
        $meeting = new Meeting();
        $meetingId = null;
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $meetingId = $_POST['id'];
            $meeting = $entityManager->find('Meeting', $meetingId);
            $oldMeeting = clone $meeting;
        }

        if (isset($_POST['start'])) {
            $meeting->setStart(new DateTime($_POST['start']));
        }

        if (isset($_POST['end'])) {
            $meeting->setEnd(new DateTime($_POST['end']));
        }

        if (isset($_POST['subject'])) {
            $meeting->setName($_POST['subject']);
        }

        if (isset($_POST['description'])) {
            $meeting->setDescription($_POST['description']);
        }

        if (isset($_POST['responsible'])) {
            $responsible = $entityManager->find('Member', $_POST['responsible']);
            $meeting->setResponsible($responsible);
        }

        if (isset($_POST['location'])) {
            $locationService = new LocationService($entityManager);
            $location = $locationService->getLocationWithName($_POST['location']);
            $meeting->setLocation($location);
        }

        $finalMemberList = array();
        $group = "";
        if(isset($_POST['members'])){
            if(isset($_POST['members'])){
                $memberArray = $_POST['members'];
                if ($memberArray != ""){
                    foreach($memberArray as $memberId){
                        if (!in_array($memberId, $finalMemberList)){
                            $finalMemberList[]=$memberId;
                        }
                    }
                }
            }
        }

        $meetingService = new MeetingService($entityManager);
        $serviceResult->merge($meetingService->saveOrUpdate($meeting, $finalMemberList));
        $output = $serviceResult->getJsonData();
    } else if(isset($_GET['id'])){
		$meetingItem=$entityManager->find('Meeting', $_GET['id']);
        $member=getMitglied();

		$status="";
		$signinEntries=array();
		if($member != null){
			$entry=$entityManager->getRepository('SigninEntry')->findOneBy(array("signinObject" => $meetingItem, "member" => $member));
			if($entry != null){
				$status=$entry->getStatus();
			}
			$entries=$entityManager->getRepository('SigninEntry')->getSigninEntries($meetingItem);
			foreach($entries as $entry){
				$signinEntries[]=$entry->getJsonData();
			}	
		}
		$output=array("object" => $meetingItem->getJsonData(), "status" => $status, "entries" => $signinEntries);
	} else {
        $member=getMitglied();

		$meetingItems=$entityManager->getRepository('Meeting')->getFuturSigninObjectsForMember($member);
		$output=array();		
		foreach($meetingItems as $meetingItem){
			$status="";
			if($member != null){
				$entry=$entityManager->getRepository('SigninEntry')->findOneBy(array("signinObject" => $meetingItem, "member" => $member));
				if($entry != null){
					$status=$entry->getStatus();
				}
			}
			$meetingItem->getLocation();
			$meetingItem->getResponsible();
			$output[]=array("object" => $meetingItem->getJsonData(), "status" => $status);
		}
	}
	echo json_encode($output);
?>