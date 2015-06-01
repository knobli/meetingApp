<?php
/**
 * Created by PhpStorm.
 * User: knobli
 * Date: 02.02.2015
 * Time: 16:47
 */
use Doctrine\ORM\Tools\Console\ConsoleRunner;

// replace with file to your own project bootstrap
require_once 'bootstrap.php';
require_once "class_includes_only.php";

return ConsoleRunner::createHelperSet($entityManager);