<?php
require 'generalController.php';

	if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
		$signinObject=$entityManager->find('SigninObject', $_POST['signinObjectId']);
        $member=getMitglied();
		
		$signinEntryService=new SigninEntryService($entityManager);
		$canSubscripe=$signinEntryService->canSubscripe($signinObject, $member, false);
		if($canSubscripe !== true){
			$output=array("success" => 0, "error_message" => $canSubscripe);
		} else {
			$signinEntryService->subscriptionForSigninObject($signinObject, $member, $_POST['comment'], $_POST['status']);
			$output=array("success" => 1, "error_message" => "Erfolgreich eingetragen");	
		}
	} else {	
		$output=array();
	}
	echo json_encode($output);
?>