<?php
/**
 * Created by PhpStorm.
 * User: knobli
 * Date: 29.01.2015
 * Time: 07:46
 */

namespace members;


class PermissionContainer implements \Serializable {

    const PERMISSIONS = 0;
    const LOADING_DATE = "LOADING";

    private $permissions;

    function __construct()
    {
        $this->permissions[self::PERMISSIONS] = array();
        $this->permissions[self::LOADING_DATE] = \Helper::getActualDate();
    }

    public function setPermissions(array $permissions){
        $this->permissions = $permissions;
    }

    /**
     * @return array
     */
    public function getPermissions(){
        return $this->permissions[self::PERMISSIONS];
    }

    /**
     * @return \DateTime
     */
    public function getLoadingDate(){
        return $this->permissions[self::LOADING_DATE];
    }

    public function addPermission($permission){
        $this->permissions[self::PERMISSIONS][$permission] = "dummy";
    }

    public function hasRight($permission){
        if (isset($this->getPermissions()[$permission])) {
            return true;
        }
        return false;
    }


    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize($this->permissions);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        $this->setPermissions(unserialize($serialized));
    }
}