<?php
require_once __DIR__ . "/vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$paths = array(__DIR__ . "/Classes");

// the connection configuration
if(isset($_SERVER['IS_DEVMODE']) && $_SERVER['IS_DEVMODE'] === true){
	$dbParams = array(
	    'driver'   => 'pdo_sqlite',
	    'memory '  => 'true',
	);
	$isDevMode = true;
} else {
	$isDevMode = false;
	$dbParams = array(
	    'driver'   => 'pdo_mysql',
	    'user'     => 'meetingApp',
	    'password' => 'E^78u8+HJ7jTuGJcCh',
	    'dbname'   => 'meetingApp',
	    'charset'  => 'utf8',
	);
}

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, __DIR__ . "/doctrineProxies");
$entityManager = EntityManager::create($dbParams, $config);

// $entityManager->getConnection()
  // ->getConfiguration()
  // ->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());



?>