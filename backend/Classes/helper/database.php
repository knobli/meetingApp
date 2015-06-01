<?php

class Database {

    private static $db = null;
    private $connection;

    private function __construct() {
		$dbname="meetingApp"; //Datenbankname
		$dbserver="localhost"; //Servername
		$dbuser="meetingApp"; //Username bzw. Datenbank-loginname
		$dbpass="E^78u8+HJ7jTuGJcCh"; //Passwort der Datenbank
		    	
        $this->connection = new MySQLi($dbserver,$dbuser,$dbpass,$dbname);
		if (mysqli_connect_errno()) {
		    $ret = 'Konnte keine Verbindung zu Datenbank aufbauen, MySQL meldete: '.mysqli_connect_error();
			echo"$ret<br>";
			return;
		}
		$this->connection->set_charset("utf8");
    }

    function __destruct() {
        $this->connection->close();
    }

	/**
	 * @return MySQLi 
	 */
    public static function getConnection() {
        if (static::$db == null) {
            static::$db = new Database();
        }
        return static::$db->connection;
    }

    /**
     * @return \Doctrine\DBAL\Driver\Connection
     */
	public static function getPDOConnection(){
		global $entityManager;
		return $entityManager->getConnection()->getWrappedConnection();
	}

    /**
     * @return \Doctrine\ORM\EntityManager
     * @throws Exception
     */
    public static function getEntityManager(){
        global $entityManager;
        if($entityManager == null){
            throw new Exception("No entity manager initialized");
        }
        return $entityManager;
    }
}

?>