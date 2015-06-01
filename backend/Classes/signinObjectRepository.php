<?php
use Doctrine\ORM\EntityRepository;

class SigninObjectRepository extends EntityRepository
{

	public function getFuturSigninObjectsForMember(Member $member){
		return $this->getSigninObjects("futur", $member);
	}
	
	public function getPastSigninObjectsForMember(Member $member){
		return $this->getSigninObjects("past", $member);
	}

	private function getSigninObjects($type, Member $member = null){
		if($type == "futur"){
			$operator = ">=";
			$sort = "ASC";
		} else if ($type == "past") {
			$operator = "<";
			$sort = "DESC";
		}
		$actualDate = Helper::getActualDate();
		$entity = $this->getEntityName();
        $qbSub = $this->_em->createQueryBuilder();
        $qbSub->select('IDENTITY(se.signinObject)')
              ->from('SigninEntry', 'se')
              ->leftJoin('se.signinObject', 'signO');
        $qbSub->where("se.member = :member");
        $qbSub->andWhere('signinObject instance of :entity');
        $subQuery = $qbSub->getQuery();
		
		$qb = $this->_em->createQueryBuilder();
		$qb->select('signinObject')
			->from('SigninObject', 'signinObject');
		$qb->where("signinObject.startDate $operator :startDate");
        if($entity != "SigninObject") {
            $qb->andWhere('signinObject instance of :entity');
        }
        $qb->andWhere("signinObject in ( " . $subQuery->getDql() . ")");
        $qb->setParameter('member', $member);
		$qb->addOrderBy('signinObject.startDate', $sort);
		$qb->setParameter('startDate', $actualDate);
		if($entity != "SigninObject") {
			$qb->setParameter('entity', $entity);
		}
		$query = $qb->getQuery();
		$signinObjects = $query->getResult();
	    return $signinObjects;			
	}
}
?>