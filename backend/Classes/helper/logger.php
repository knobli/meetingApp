<?php

class Logger {

    private static $logger = null;
    private $kLogger;

    private function __construct() {   
		$this->kLogger = new KLogger ( dirname(__FILE__) . "/../../logs" , KLogger::INFO ); 	
    }

    function __destruct() {
    }

	/**
	 * @return Klogger
	 */
    public static function getLogger() {
        if (static::$logger == null) {
            static::$logger = new Logger();
        }
        return static::$logger->kLogger;
    }
}

?>