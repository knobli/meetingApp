<?php
use Doctrine\Common\Collections\Collection;
function getUserID($db = null) {
    if(AccountService::getInstance()->isLoggedIn()){
        return AccountService::getInstance()->getCurrentAccountId();
    }
    return null;
}
/**
 * @return Member
 */
function getMitglied(){
    $memberId = AccountService::getInstance()->getCurrentMemberId();
    if(AccountService::getInstance()->isLoggedIn() && $memberId !== null) {
        global $entityManager;
        $member = $entityManager->find('Member', $memberId);
        return $member;
    }
    return null;
}
function getMitgliedID($dummy = "") {
    if(AccountService::getInstance()->isLoggedIn()) {
        return AccountService::getInstance()->getCurrentMemberId();
    }
    return null;
}
function hasRight($db, $right) {
    if(AccountService::getInstance()->isLoggedIn() && AccountService::getInstance()->getCurrentPermission() !== null) {
        return AccountService::getInstance()->getCurrentPermission()->hasRight($right);
    }
    return false;
}
?>