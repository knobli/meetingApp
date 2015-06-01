<?php
require 'generalController.php';

    $member=getMitglied();

    $outputArray = array();
    if($member !== null) {
        $members = $entityManager->getRepository('Member')->getAllAliveMembers();
        foreach ($members as $member) {
            $outputArray[] = $member->getJsonData();
        }
    }
	echo json_encode($outputArray);
?>