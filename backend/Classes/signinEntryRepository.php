<?php
use Doctrine\ORM\EntityRepository;

class SigninEntryRepository extends EntityRepository
{
	
	public function getSigninEntries(SigninObject $signinObject){
		return $this->getEntries($signinObject, SigninEntry::SING_IN);				
	}
	
	public function getSignoutEntries(SigninObject $signinObject){
		return $this->getEntries($signinObject, SigninEntry::SING_OUT);				
	}	
	
	private function getEntries(SigninObject $signinObject, $status){
		$query = $this->_em->createQuery("SELECT
	                signinEntry
	            FROM
	                SigninEntry signinEntry
	            Join
	            	signinEntry.member member
	            WHERE
					signinEntry.signinObject = :signinObject
				AND
					signinEntry.status = :status
				ORDER BY
					member.firstname ASC, member.surname ASC");	
		$query->setParameter('signinObject', $signinObject);
		$query->setParameter('status', $status);
		$signinEntries = $query->getResult();
	    return $signinEntries;			
	}
	
}
?>