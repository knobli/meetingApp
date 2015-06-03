<?php
use Doctrine\Common\Collections\Collection;
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
function hasRight($db, $right) {
    if(AccountService::getInstance()->isLoggedIn() && AccountService::getInstance()->getCurrentPermission() !== null) {
        return AccountService::getInstance()->getCurrentPermission()->hasRight($right);
    }
    return false;
}
?>