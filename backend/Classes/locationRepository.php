<?php
use Doctrine\ORM\EntityRepository;

class LocationRepository extends EntityRepository
{
	public function getLocationsForMeetings(){
		return $this->getLocationForType(Meeting::CONST_TYPE);						
	}
	
	private function getLocationForType($type){
		$query = $this->_em->createQuery('SELECT
	                location
	            FROM
	                Location location
	            Join
	            	location.signInObjects sio
	            WHERE
					sio instance of :type
				ORDER BY
					location.name
				');
		$query->setParameter('type', $type);				
		$locations = $query->getResult();
	    return $locations;		
	}		
		
}