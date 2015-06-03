<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 'on');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
require '../../../class_includes.php';
require '../../../functions_permissions.php';

$memberId=null;
if(isset($_POST['memberId']) && $_POST['memberId'] !== ""){
    $memberId = $_POST['memberId'];
} else if(isset($_GET['memberId']) && $_GET['memberId'] !== ""){
    $memberId = $_GET['memberId'];
} else if (isset($post_vars['memberId']) && $post_vars['memberId'] !== ""){
    $memberId = $post_vars['memberId'];
}
if($memberId != null){
    AccountService::getInstance()->createSessionWithMemberId($memberId);
}

?>