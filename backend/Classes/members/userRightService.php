<?php
class UserRightService {
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $entityManager;
	
	public function __construct(Doctrine\ORM\EntityManager $entityManager){
	    $this->entityManager = $entityManager;
	}

    /**
     * @return bool
     */
    public function hasRight(Member $member, UserRight $right){
        $rightEntryRepo=$this->entityManager->getRepository('UserRightEntry');
        $entry = $rightEntryRepo->findOneBy(array("account" => $member->getAccount(), "userRight" => $right));
        if($entry == null){
            return false;
        }
        return true;
    }

    /**
     * @param Account $account
     * @param $right
     * @return \helper\ServiceResult
     */
    public function addGlobalRight(Account $account, $right)
    {
        $serviceResult = new \helper\ServiceResult(\helper\ResultEnum::SUCCESS);
        $rightRepo=$this->entityManager->getRepository('UserRight');
        $right = $rightRepo->findOneByName($right);
        if($right !== null){
            $userRightEntry = new UserRightEntry();
            $userRightEntry->setAccount($account);
            $userRightEntry->setUserRight($right);
            try{
                $this->entityManager->persist($userRightEntry);
                $this->entityManager->flush();
            } catch (Exception $e){
                $serviceResult->addErrorMessage("Berechtigung konnte nicht gesetzt werden!");
                Logger::getLogger()->logError("Could not save userRight: " . $e->getMessage());
            }
        } else {
            $serviceResult->addErrorMessage("Keine Berechtigung '$right' gefunden!");
        }
        return $serviceResult;
    }

}
?>