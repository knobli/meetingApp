<?php
class LocationService {
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $entityManager;
	
	public function __construct(Doctrine\ORM\EntityManager $entityManager){
	    $this->entityManager = $entityManager;
	}

    /**
     * @param $name
     * @return Location|null
     */
	public function getLocationWithName($name){
        $location=null;
        if(!empty($name)) {
            $location = $this->entityManager->getRepository('Location')->findOneBy(array("name" => $name));
            if ($location == NULL) {
                $location = new Location($name);
                $this->entityManager->persist($location);
                $this->entityManager->flush();
            }
        }
		return $location;
	}
		
}
?>