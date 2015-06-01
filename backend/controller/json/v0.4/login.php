<?php
require 'generalController.php';

    $jsonFeedback["success"]=0;
    $jsonFeedback["error_message"]="Could not login!";
	if ('POST' == $_SERVER['REQUEST_METHOD']) {
		if(isset($_POST['username']) && isset($_POST['password'])){
            $result = AccountService::getInstance()->login($_POST['username'], $_POST['password']);
            if($result->isSuccess()){
                $jsonFeedback["success"]=1;
                $jsonFeedback["memberId"]=AccountService::getInstance()->getCurrentMemberId();
                $jsonFeedback["username"]=AccountService::getInstance()->getCurrentAccountName();
            } else {
                $jsonFeedback["success"]=0;
                $jsonFeedback["error_message"]="Username und/oder Passwort ist ungültig!";
            }
		}
	} else {
		Logger::getLogger()->logCrit("No post request");
	}
    echo json_encode($jsonFeedback);
?>