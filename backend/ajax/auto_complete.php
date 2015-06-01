<?php
header('Access-Control-Allow-Origin: *');
require '../class_includes.php';
$db = Database::getConnection();

$q = strtolower($_GET["term"]);
if (!$q){
	Logger::getLogger()->logCrit("auto_complete: No querry string given");
	return;
}

$type = $_GET["type"];
if (!$type){
	Logger::getLogger()->logCrit("auto_complete: No type given");
	return;
}

$items = array();
$searchKey = '%'.$q.'%';

$sqlStatement=array();

$actualDate = date('Y-m-d');
$actualDate = "$actualDate 00:00:00";
							
$sqlStatement["ort"]="select ID_Ort, Name
							from tbl_Orte
							where Name like ?";																						

$results=array();
							
$sql = $sqlStatement[$type];		
$stmt = $db->prepare($sql);
if (!$stmt) {
	echo $db->error;
	return false;
}
if(!$stmt->bind_param('s', $searchKey)){
	$str = $stmt->error;
	$stmt->close();
	echo $str;
	return false;			
}
if (!$stmt->execute()) {
	$str = $stmt->error;
	$stmt->close();
	echo $str;
	return false;
}
if(!$stmt->store_result()){
	$str = $stmt->error;
	$stmt->close();
	echo $str;
	return false;			
}
if(!$stmt->bind_result($id,$name)){
	$str = $stmt->error;
	$stmt->close();
	echo $str;
	return false;
}			
while($stmt->fetch()) {
	array_push($results,array("id"=>$id, "label"=>$name, "value" => $name));
}
$stmt->close();

echo json_encode($results);
?>