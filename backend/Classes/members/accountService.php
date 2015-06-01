<?php

/**
 * Created by PhpStorm.
 * User: knobli
 * Date: 16.11.2014
 * Time: 17:11
 */
use members\PermissionContainer;
use helper\ServiceResult;
class AccountService
{

    //1 month
    const SESSION_LIFE_TIME = 2630000;
    //in seconds
    const MAX_PERMISSION_DURATION = 600;
    const DEFAULT_ERROR_MSG = "Username und/oder Passwort ist ungültig";
    const ACCOUNT_NAME = "accountName";
    const ACCOUNT_ID = "accountId";
    const MEMBER_ID = "memberId";
    const PERMISSION = "permission";
    const USER_AGENT = "userAgent";

    private static $instance = null;

    public function __construct()
    {
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     * @throws Exception
     */
    public static function getEntityManager(){
        return Database::getEntityManager();
    }

    /**
     * @return AccountService
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new AccountService();
        }
        return self::$instance;
    }

    public function logout()
    {
        $serviceResult = new ServiceResult();

        //TODO: remove this in a few months
        if (isset($_COOKIE['UserID'])) {
            setcookie('UserID', '', strtotime('-1 day'));
            setcookie('Password', '', strtotime('-1 day'));
            unset($_COOKIE['UserID']);
            unset($_COOKIE['Password']);
        }

        // Setze alle Session-Werte zurück
        $_SESSION = array();

        // hole Session-Parameter
        $params = session_get_cookie_params();

        // Lösche das aktuelle Cookie.
        setcookie(session_name(), '', time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]);

        session_destroy();
        $serviceResult->addSuccessMessage("Erfolgreich abgemeldet");
        return $serviceResult;
    }

    /**
     * @param $username
     * @param $password
     * @return ServiceResult
     */
    public function login($username, $password)
    {
        $serviceResult = new ServiceResult();

        /** @var Account $account */
        $account = $this->getEntityManager()->getRepository('Account')->findOneBy(array("username" => $username));

        if ($account == null) {
            Logger::getLogger()->logCrit("Wrong username " . $username . "!");
            $serviceResult->addErrorMessage(self::DEFAULT_ERROR_MSG);
        } else {
            $userRightService = new UserRightService($this->getEntityManager());
            $loginRight = $this->getEntityManager()->getRepository('UserRight')->findOneByName('Login');
            if (!$userRightService->hasRight($account->getMember(), $loginRight)) {
                Logger::getLogger()->logCrit("Try to login with user " . $account->getUsername() . " but is locked!");
                $serviceResult->addErrorMessage("Der Account ist geblockt!");
            } else {
                $serviceResult->merge($this->checkSaltedPassword($password, $account));
            }
        }
        return $serviceResult;
    }

    public function isLoggedIn()
    {
        if (isset($_SESSION[self::MEMBER_ID],
            $_SESSION[self::ACCOUNT_ID],
            $_SESSION[self::ACCOUNT_NAME],
            $_SESSION[self::PERMISSION],
            $_SESSION[self::USER_AGENT])) {
            if(!$this->checkHijacking()) {

                $permission = $_SESSION[self::PERMISSION];
                if ($permission->hasRight("Login")) {
                    return true;
                } else {
                    $username = $_SESSION[self::ACCOUNT_NAME];
                    Logger::getLogger()->logError("User" . $username . " has no login rights");
                    return false;
                }
            } else {
                Logger::getLogger()->logCrit("Session hijacked");
                return false;
            }
        } else {
            return false;
        }
    }

    private function checkHijacking()
    {
        if( $_SESSION[self::USER_AGENT] !== $_SERVER['HTTP_USER_AGENT']) {
            Logger::getLogger()->logCrit("" . $_SESSION[self::USER_AGENT] ." !== " . $_SERVER['HTTP_USER_AGENT']);
            return true;
        }
        return false;
    }

    public function startSession() {
        // Set to true if using https.
        $secure = (isset($_SERVER['HTTPS'])) ? true : false;
        // This stops javascript being able to access the session id.
        $httpOnly = true;
        // Forces sessions to only use cookies.
        ini_set('session.use_only_cookies', 1);
        // Gets current cookies params.
        $cookieParams = session_get_cookie_params();

        session_set_cookie_params(self::SESSION_LIFE_TIME, $cookieParams["path"], $cookieParams["domain"], $secure, $httpOnly);

        // Start the php session
        session_start();
    }

    /**
     * @param $password
     * @throws Exception
     * @return string
     */
    public function hashPassword($password)
    {
        $options = [
            'cost' => 10
        ];
        if (($hashedPassword = password_hash($password, PASSWORD_BCRYPT, $options)) === false) {
            throw new Exception("Could not hash password");
        }
        return $hashedPassword;
    }

    /**
     * @param $password
     * @param Account $account
     * @return ServiceResult
     */
    public function checkSaltedPassword($password, Account $account)
    {
        $serviceResult = new ServiceResult(\helper\ResultEnum::SUCCESS);
        if (password_verify($password, $account->getPassword())) {
            $this->createSession($account);
            $serviceResult->addSuccessMessage("Erfolgreich angemeldet");
        } else {
            Logger::getLogger()->logCrit("hashed method: Wrong password for " . $account->getUsername());
            $serviceResult->addErrorMessage(self::DEFAULT_ERROR_MSG);
        }
        return $serviceResult;
    }

    /**
     * @return PermissionContainer
     */
    public function getCurrentPermission()
    {
        /** @var PermissionContainer $permissionContainer */
        $permissionContainer = $this->getCurrentSessionValue(self::PERMISSION);
        $loadingDate = $permissionContainer->getLoadingDate();
        $actualDate = \Helper::getActualDate();
        if($loadingDate->modify("+ " . self::MAX_PERMISSION_DURATION . " seconds") < $actualDate){
            $account = $this->getEntityManager()->find('Account', $this->getCurrentAccountId());
            $this->setPermissions($account);
        }
        return $this->getCurrentSessionValue(self::PERMISSION);
    }

    /**
     * @return int
     */
    public function getCurrentMemberId()
    {
        return $this->getCurrentSessionValue(self::MEMBER_ID);
    }

    /**
     * @return int
     */
    public function getCurrentAccountId()
    {
        return $this->getCurrentSessionValue(self::ACCOUNT_ID);
    }

    /**
     * @return int
     */
    public function getCurrentAccountName()
    {
        return $this->getCurrentSessionValue(self::ACCOUNT_NAME);
    }

    private function getCurrentSessionValue($key){
        if(isset($_SESSION[$key])){
            return $_SESSION[$key];
        }
        return null;
    }

    /**
     * @param Account $account
     */
    private function createSession(Account $account)
    {
        $_SESSION[self::USER_AGENT] = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION[self::MEMBER_ID] = $account->getMember()->getId();
        $_SESSION[self::ACCOUNT_ID] = $account->getId();
        $_SESSION[self::ACCOUNT_NAME] = $account->getUsername();
        $this->setPermissions($account);
    }

    /**
     * @param Account $account
     */
    private function setPermissions(Account $account)
    {
        $permissionContainer = new PermissionContainer();

        foreach ($account->getRightEntries() as $userRightEntry) {
            $permissionContainer->addPermission($userRightEntry->getUserRight()->getName());
        }
        $_SESSION[self::PERMISSION] = $permissionContainer;
    }

    /**
     * @param $mitgliedID
     * @deprecated
     */
    public function createSessionWithMemberId($mitgliedID)
    {
        $account = $this->getEntityManager()->getRepository('Account')->findOneByMember($mitgliedID);
        AccountService::getInstance()->createSession($account);
    }

    public function createNewAccount($username, $password, Member $member)
    {
        $serviceResult = new ServiceResult(\helper\ResultEnum::SUCCESS);
        if($member->getAccount() !== null){
            $serviceResult->addErrorMessage("Es ist bereits ein Account für dieses Mitglieder erfasst.");
        }

        $serviceResult->merge($this->checkUsername($username));

        if($serviceResult->isSuccess()){
            $account = new Account();
            $account->setUsername($username);
            $account->setPassword($this->hashPassword($password));
            $account->setMember($member);
            try{
                $this->getEntityManager()->persist($account);
                $this->getEntityManager()->flush();
            } catch (Exception $e){
                $serviceResult->addErrorMessage("Account konnte nicht gespeichert werden");
                Logger::getLogger()->logError("Could not save account: " . $e->getMessage());
            }
        }

        $userRightService = new UserRightService($this->getEntityManager());
        $serviceResult->merge($userRightService->addGlobalRight($account, "Login"));

        return $serviceResult;
    }

    /**
     * @param $username
     * @param $mail
     * @return ServiceResult|void
     */
    public function resetPassword($firstname, $surname, $mail)
    {
        $serviceResult = new ServiceResult(\helper\ResultEnum::SUCCESS);
        $member=$this->getEntityManager()->getRepository('Member')->findMemberByNameAndMail($firstname, $surname, $mail);
        if($member !== null && $member->getAccount() !== null) {
            $password = $this->createRandomPassword();
            $account = $member->getAccount();
            $account->setPassword($this->hashPassword($password));
            try {
                $this->getEntityManager()->persist($account);
                $this->getEntityManager()->flush();
                $serviceResult->merge($this->sendNewPasswordToMember($member, $password));
            } catch (Exception $e) {
                $serviceResult->addErrorMessage("Passwort konnte nicht zurückgesetzt werden!");
                Logger::getLogger()->logError("Could not save password to account: " . $e->getMessage());
            }
        } else {
            $serviceResult->addErrorMessage("Account konnte nicht gefunden werden!");
        }
        return $serviceResult;
    }

    private function createRandomPassword() {
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
        srand((double)microtime()*1000000);
        $i = 0;
        $pass = '' ;

        while ($i <= 7) {
            $num = rand() % 33;
            $tmp = substr($chars, $num, 1);
            $pass = $pass . $tmp;
            $i++;
        }
        return $pass;
    }

    /**
     * @param string $newUsername
     * @param string $newPassword
     * @param string $oldPassword
     * @param bool $checkOldPassword
     * @param Account $account
     * @return ServiceResult
     * @throws Exception
     */
    public function updateAccount($newUsername, $newPassword, $oldPassword, $checkOldPassword, Account $account)
    {
        $serviceResult = new ServiceResult(\helper\ResultEnum::SUCCESS);
        if($account->getUsername() !== $newUsername) {
            $serviceResult->merge($this->checkUsername($newUsername));
            if($serviceResult->isSuccess()){
                $account->setUsername($newUsername);
            }
        }
        if($checkOldPassword){
            if(!$this->checkSaltedPassword($oldPassword, $account)->isSuccess()){
                $serviceResult->addErrorMessage("Das alte Password ist nicht korrekt!");
            }
        }
        if($serviceResult->isSuccess()){
            $account->setPassword($this->hashPassword($newPassword));
            try {
                $this->getEntityManager()->persist($account);
                $this->getEntityManager()->flush();
            } catch (Exception $e) {
                $serviceResult->addErrorMessage("Account konnte nicht angepasst werden werden!");
                Logger::getLogger()->logError("Could not update account: " . $e->getMessage());
            }
        }
        return $serviceResult;
    }

    /**
     * @param $username
     * @return ServiceResult
     */
    public function checkUsername($username)
    {
        $serviceResult = new ServiceResult(\helper\ResultEnum::SUCCESS);
        if (!preg_match('~\A\S{3,30}\z~', $username)) {
            $serviceResult->addErrorMessage("Der Benutzername darf nur aus 3 bis 30 Zeichen bestehen und keine Leerzeichen enthalten.");
        }

        $account = $this->getEntityManager()->getRepository('Account')->findOneByUsername($username);
        if ($account !== null) {
            $serviceResult->addErrorMessage("Der Username wird bereits verwendet.");
        }
        return $serviceResult;
    }

}

?>