<?php
use Doctrine\ORM\EntityRepository;

class MemberRepository extends EntityRepository
{
	public function getAllAliveMembers() {
		$query = $this->_em->createQuery("SELECT
	                m
	            FROM
	                Member m
	            WHERE
					m.firstname is not NULL
				AND
					m.surname is not NULL
				AND
					m.deathDate is NULL
				ORDER BY
					m.firstname ASC, m.surname ASC");			
		$members = $query->getResult();
	    return $members;
    }

    /**
     * @return array(Member)
     */
    public function getMemberForSignInObject(SigninObject $signinObject){
        $query = $this->_em->createQuery("SELECT
	                m
	            FROM
	                Member m
	            Join
	            	m.signEntries se
	            WHERE
	            	se.signinObject = :signInObject
				ORDER BY
					m.firstname ASC, m.surname ASC");
        $query->setParameter('signInObject', $signinObject);
        $members = $query->getResult();
        return $members;
    }

}
?>
