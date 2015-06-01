<?php
function getMitgliedName($db,$mitgliedId) {
    if (!is_object($db)) {
        return false;
    }
    if (!($db instanceof MySQLi)) {
        return false;
    }
    $sql = 'SELECT
                Vorname, Nachname
            FROM
                tbl_Mitglieder
            WHERE
                Mitglied_ID = ?';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        return $db->error;
    }
    $stmt->bind_param('i', $mitgliedId);
    if (!$stmt->execute()) {
        $str = $stmt->error;
        $stmt->close();
        return $str;
    }
    $stmt->bind_result($vorname, $nachname);
    if (!$stmt->fetch()) {
        $stmt->close();
        return false;
    }
    $stmt->close();
    return array($vorname,$nachname);
}
function getAccountName($db,$mitgliedId) {
    if (!is_object($db)) {
        return false;
    }
    if (!($db instanceof MySQLi)) {
        return false;
    }
    $sql = 'SELECT
                Username
            FROM
            	tbl_Accounts
            WHERE
                FK_Mitglied = ?';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        return $db->error;
    }
    $stmt->bind_param('i', $mitgliedId);
    if (!$stmt->execute()) {
        $str = $stmt->error;
        $stmt->close();
        return $str;
    }
    $stmt->bind_result($accountName);
    if (!$stmt->fetch()) {
        $stmt->close();
        return false;
    }
    $stmt->close();
    return $accountName;
}
function getMitgliedNameAndPicutre($db,$mitgliedId) {
    if (!is_object($db)) {
        return false;
    }
    if (!($db instanceof MySQLi)) {
        return false;
    }
	//TODO add picture
	
	list($vorname,$nachname)=getMitgliedName($db,$mitgliedId);
	$returnOutput = "<a href=\"./personal&submenu=3&mitglied=$mitgliedId\">$vorname $nachname</a>";
    return $returnOutput;
}
function getWettkampfInfos($db,$wettkampfid) {
	$sql = 'SELECT
                Name, DATE_FORMAT(Start, \'%d.%m.%Y %H:%i\'), DATE_FORMAT(Ende, \'%d.%m.%Y %H:%i\'), DATE_FORMAT(Anmeldeschluss, \'%d.%m.%Y\'), Wettkampfort, Beschreibung, 
				FK_Wettkampftyp, Verantwortlicher, File1, File2
			FROM 
				tbl_Wettkaempfe
			WHERE 
                ID = ?';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
    	echo $db->error;
        return false;
    }
    $stmt->bind_param('i', $wettkampfid);
    if (!$stmt->execute()) {
        $str = $stmt->error;
		echo $str;
        $stmt->close();
        return false;
    }
    $stmt->bind_result($wettkampfname, $datum, $end, $anmeldeschluss, $ort, $beschreibung, $typ, $verantwortlicher, $file1, $file2);
    if (!$stmt->fetch()) {
        $stmt->close();
        return false;
    }
    $stmt->close();
	
	return array($wettkampfname, $datum, $end, $anmeldeschluss, $ort, stripslashes($beschreibung), $typ, $verantwortlicher, $file1, $file2);
}
function getResultatForDisziplin($betId,$memberId,$disciplineId) {
	$db = Database::getPDOConnection();
    $result = null;
	$sql = 'SELECT
                 Resultat
			FROM 
				tbl_Resultate
			WHERE 
				Wettkampf = ?
			AND
				Mitglied_ID = ?
			AND
                FK_Disziplin = ?';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        Logger::getLogger()->logError("Could not load result for betId $betId, memberId $memberId and disciplineId $disciplineId: " . $db->error);
        return $result;
    }
    if (!$stmt->execute(array($betId,$memberId,$disciplineId))) {
        Logger::getLogger()->logError("Could not load result for betId $betId, memberId $memberId and disciplineId $disciplineId: " . $stmt->error);
        return $result;
    }
    $result = $stmt->fetchColumn();
    if(!$result){
        return null;
    }
	return 	$result;
}
function inVorstand($memberId,$riegeId,$db) {
	if (!($db instanceof MySQLi)) {
    	throw new Exception('Erster Parameter muss ein MySQLi-Objekt sein.');
    }
    $sql = 'SELECT
                ID_Funktion
            FROM
                tbl_Vorstand_History, tbl_Vorstandsamt
            WHERE
            	tbl_Vorstand_History.FK_Amt = tbl_Vorstandsamt.ID_Amt 
            AND
                FK_Mitglied = ? 
			AND
				FK_Riege = ?
			AND
                BisDatum is NULL
            AND
            	tbl_Vorstandsamt.Vorstand = 1';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        return $db->error;
    }
    $stmt->bind_param('ii', $memberId, $riegeId);
    $stmt->execute();
    $stmt->bind_result($blubber); // interessiert eh keinen
    $ret = $stmt->fetch(); // ist true wenn ein Datensatz vorhanden ist oder
                           // NULL wenn nicht (und false wenn ein fehler auftrat)
    $stmt->close();
    return (bool)$ret;
}
function getRangForDisziplin($betId,$memberId,$disciplineId) {
	$db = Database::getPDOConnection();
	$rank = null;
	$sql = 'SELECT
                 Resultat
			FROM 
				tbl_Resultate
			WHERE 
				Wettkampf = ?
			AND
				Mitglied_ID = ?
			AND
                FK_Disziplin = ?';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        Logger::getLogger()->logError("Could not load result for rank for betId $betId, memberId $memberId and disciplineId $disciplineId: " . $db->error);
        return $rank;
    }
    if (!$stmt->execute(array($betId,$memberId,$disciplineId))) {
        Logger::getLogger()->logError("Could not load result for rank for betId $betId, memberId $memberId and disciplineId $disciplineId: " . $stmt->error);
        return $rank;
    }
    $result = $stmt->fetchColumn();
    if(!$result){
        Logger::getLogger()->logError("Could not load result for rank for betId $betId, memberId $memberId and disciplineId $disciplineId: No result");
        return $rank;
    }
	
	$sql = 'SELECT
                 Sort
			FROM 
				tbl_Disziplinen
			WHERE 
                Disziplin_ID = ?';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        Logger::getLogger()->logError("Could not load sort for rank for betId $betId, memberId $memberId and disciplineId $disciplineId: " . $db->error);
        return $rank;
    }
    if (!$stmt->execute(array($disciplineId))) {
        Logger::getLogger()->logError("Could not load sort for rank for betId $betId, memberId $memberId and disciplineId $disciplineId: " . $stmt->error);
        return $rank;
    }
    $orderType = $stmt->fetchColumn();
    if(!$orderType){
        Logger::getLogger()->logError("Could not load sort for rank for betId $betId, memberId $memberId and disciplineId $disciplineId: No sort for discipline");
        return $rank;
    }
	
	if ( $orderType == "ASC" ){
		$sql = 'SELECT
	                 Resultat, Mitglied_ID
				FROM 
					tbl_Resultate
				WHERE 
					Wettkampf = ?
				AND
	                FK_Disziplin = ?
				ORDER BY Resultat ASC';
	} elseif ( $orderType == "DESC" ) {
		$sql = 'SELECT
	                 Resultat, Mitglied_ID
				FROM 
					tbl_Resultate
				WHERE 
					Wettkampf = ?
				AND
	                FK_Disziplin = ?
				ORDER BY Resultat DESC';		
	}
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        Logger::getLogger()->logError("Could not load results for rank for betId $betId, memberId $memberId and disciplineId $disciplineId: " . $db->error);
        return $rank;
    }
    if (!$stmt->execute(array($betId,$disciplineId))) {
        Logger::getLogger()->logError("Could not load results for rank for betId $betId, memberId $memberId and disciplineId $disciplineId: " . $stmt->error);
        return $rank;
    }
	$results=array();
    while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
    	$results[$row[1]]=$row[0];
	}
	
	$rank=1;
	$currResult="";
	foreach ($results as $memberId => $tmpResult){
		if($currResult == ""){
			$currResult = $tmpResult;
		}
		if($currResult != $tmpResult){
            $rank++;
		}
		if ($memberId == $memberId && $tmpResult == $result){
			break;
		}
		$currResult=$tmpResult;
	}
	
	return 	$rank;

}
function getRiegenForItem($db,$itemId) {
    if (!is_object($db)) {
        return false;
    }
    if (!($db instanceof MySQLi)) {
        return false;
    }
	
    $sql = 'SELECT
                FK_Riege
            FROM
                vtbl_Fundgegenstand_Riege
            WHERE
                FK_Fundgegenstand = ?
				';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        return $db->error;
    }
    $stmt->bind_param('i', $itemId);
    if (!$stmt->execute()) {
        $str = $stmt->error;
        $stmt->close();
        return $str;
    }
    $stmt->bind_result($riege);
    if (!$stmt->fetch()) {
        $stmt->close();
        return false;
    }
    do {
		$riegen[]=$riege;
    } while ($stmt->fetch());
    $stmt->close();
	
    return $riegen;     
	
}
function getItemInfos($db,$itemId) {
	$sql = 'SELECT 
               Name, DATE_FORMAT(Datum, \'%d.%m.%Y\'), Ort, Beschreibung, Oeffentlich, Anzeiger, Type, Status
            FROM
                tbl_Fundgegenstaende
            WHERE
                ID_Fundgegenstand = ?';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        return $db->error;
    }
    $stmt->bind_param('i', $itemId);
    if (!$stmt->execute()) {
        $str = $stmt->error;
        $stmt->close();
        return $str;
    }
    $stmt->bind_result($name,$datum,$ort,$beschreibung,$oeffentlich,$anzeiger,$type,$status);
    if (!$stmt->fetch()) {
        $stmt->close();
        return false;
    }
    $stmt->close();

	return array($name,$datum,$ort,$beschreibung,$oeffentlich,$anzeiger,$type,$status);
}
function getKategorienForSchiedsrichter($db,$schiedsrichterId) {
    if (!is_object($db)) {
        return false;
    }
    if (!($db instanceof MySQLi)) {
        return false;
    }
	$kategorien=array();
    $sql = 'SELECT
                FK_Kategorie
            FROM
                vtbl_Kb_Schiedsrichter_Kat
            WHERE
                FK_Schiedsrichter = ?';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        return $db->error;
    }
    $stmt->bind_param('i', $schiedsrichterId);
    if (!$stmt->execute()) {
        $str = $stmt->error;
        $stmt->close();
        return $str;
    }
    $stmt->bind_result($kategorie);
    while ($stmt->fetch()){
		$kategorien[]=$kategorie;
    }
    $stmt->close();
	
    return $kategorien;     	
}
function getKategorienName($db,$kategorieId) {
    if (!is_object($db)) {
        return false;
    }
    if (!($db instanceof MySQLi)) {
        return false;
    }
	
    $sql = 'SELECT
                Kategorie
            FROM
                tbl_Korbball_Kategorien
            WHERE
                ID_Kategorie = ?';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        return $db->error;
    }
    $stmt->bind_param('i', $kategorieId);
    if (!$stmt->execute()) {
        $str = $stmt->error;
        $stmt->close();
        return $str;
    }
    $stmt->bind_result($kategorieName);
    if (!$stmt->fetch()) {
        $stmt->close();
        return false;
    }
    $stmt->close();
	
    return $kategorieName;     	
}
function getSchiriInfos($db,$schiedsrichterId) {
	$sql = 'SELECT
                 FK_Schiedsrichter, Team, Bemerkung, Status 
			FROM 
				tbl_Korbball_Schiedsrichter
            WHERE
				ID_Schiedsrichter = ?';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        return $db->error;
    }
    $stmt->bind_param('i', $schiedsrichterId);
    if (!$stmt->execute()) {
        $str = $stmt->error;
        $stmt->close();
        return $str;
    }
    $stmt->bind_result($kontakt,$team,$bemerkung,$status);
    if (!$stmt->fetch()) {
        $stmt->close();
        return false;
    }
    $stmt->close();
	
	return array($kontakt,$team,$bemerkung,$status);
}
function getVereinInfos($db,$anmeldungId) {
	$sql = 'SELECT
                 Verein, FK_Staerkeklass, FK_Kategorie, Bemerkung, FK_Anlass, FK_Kontakt, IBAN, Status 
			FROM 
				tbl_Korbball_Vereine
            WHERE
				ID_Anmeldung = ?';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        return $db->error;
    }
    $stmt->bind_param('i', $anmeldungId);
    if (!$stmt->execute()) {
        $str = $stmt->error;
        $stmt->close();
        return $str;
    }
    $stmt->bind_result($verein,$staerkeklasse,$kategorie,$bemerkung,$wettkampf,$kontakt,$iban,$status);
    if (!$stmt->fetch()) {
        $stmt->close();
        return false;
    }
    $stmt->close();
	
	return array($verein,$staerkeklasse,$kategorie,$bemerkung,$wettkampf,$kontakt,$iban,$status);
}
function getMemberIdsOfRiegen($riegenList,$db) {
    if (!is_object($db)) {
        return false;
    }
    if (!($db instanceof MySQLi)) {
        return false;
    }
	$memberList=array();
	foreach ($riegenList as $riege){
		$sql = 'SELECT distinct
					Mitglied_ID
				FROM
					tbl_Mitglieder, tbl_Mitglieder_History
				WHERE
					tbl_Mitglieder.Mitglied_ID = tbl_Mitglieder_History.FK_Mitglied
				AND 
					tbl_Mitglieder_History.FK_Riege = ?
				AND 
					tbl_Mitglieder_History.bisDatum is NULL
				AND
					(
						(`e-mail1` is not NULL AND `e-mail1` != "")
					OR 
						(`e-mail2` is not NULL AND `e-mail2` != "")
					)
				
					';
		$stmt = $db->prepare($sql);
		if (!$stmt) {
			return $db->error;
		}
		$stmt->bind_param('i', $riege);
		if (!$stmt->execute()) {
			$str = $stmt->error;
			$stmt->close();
			return $str;
		}
		$stmt->bind_result($id);
		while ($stmt->fetch()) {
			$memberList[]=$id;
		}
		$stmt->close();
	}	
    return $memberList;
}
function getMemberIdsOfRiegenAktiv($riegenList,$db) {
    if (!is_object($db)) {
        return false;
    }
    if (!($db instanceof MySQLi)) {
        return false;
    }
	$memberList=array();
	foreach ($riegenList as $riege){
		$sql = 'SELECT distinct
					Mitglied_ID
				FROM
					tbl_Mitglieder, tbl_Mitglieder_History
				WHERE
					tbl_Mitglieder.Mitglied_ID = tbl_Mitglieder_History.FK_Mitglied
				AND 
					tbl_Mitglieder_History.FK_Riege = ?
				AND 
					tbl_Mitglieder_History.bisDatum is NULL
				AND
					(aktiv_turnend = 1
						OR
					FK_Status = 1)
				AND
					(`e-mail1` is not NULL OR `e-mail2` is not NULL)
				Order by Vorname ASC, Nachname ASC
					';
		$stmt = $db->prepare($sql);
		if (!$stmt) {
			return $db->error;
		}
		$stmt->bind_param('i', $riege);
		if (!$stmt->execute()) {
			$str = $stmt->error;
			$stmt->close();
			return $str;
		}
		$stmt->bind_result($id);
		while ($stmt->fetch()) {
			$memberList[]=$id;
		}
		$stmt->close();
	}	
    return $memberList;
}
function getMemberIdsOfRiegenTurnend($riegenList,$db) {
    if (!is_object($db)) {
        return false;
    }
    if (!($db instanceof MySQLi)) {
        return false;
    }
	$memberList=array();
	foreach ($riegenList as $riege){
		$sql = 'SELECT distinct
					Mitglied_ID
				FROM
					tbl_Mitglieder, tbl_Mitglieder_History
				WHERE
					aktiv_turnend = 1
				AND 
					tbl_Mitglieder.Mitglied_ID = tbl_Mitglieder_History.FK_Mitglied
				AND 
					tbl_Mitglieder_History.FK_Riege = ?
				AND 
					tbl_Mitglieder_History.bisDatum is NULL
				Order by Vorname ASC, Nachname ASC
					';
		$stmt = $db->prepare($sql);
		if (!$stmt) {
			return $db->error;
		}
		$stmt->bind_param('i', $riege);
		if (!$stmt->execute()) {
			$str = $stmt->error;
			$stmt->close();
			return $str;
		}
		$stmt->bind_result($id);
		while ($stmt->fetch()) {
			$memberList[]=$id;
		}
		$stmt->close();
	}	
    return $memberList;
}
function getMemberIdsOfRiegenVorstand($riegenList,$db) {
    if (!is_object($db)) {
        return false;
    }
    if (!($db instanceof MySQLi)) {
        return false;
    }
	$memberList=array();
	foreach ($riegenList as $riege){
		$sql = 'SELECT distinct
					Mitglied_ID
				FROM
					tbl_Mitglieder, tbl_Vorstand_History, tbl_Vorstandsamt
				WHERE
					tbl_Mitglieder.Mitglied_ID = tbl_Vorstand_History.FK_Mitglied
				AND
					tbl_Vorstand_History.FK_Amt = tbl_Vorstandsamt.ID_Amt
				AND 
					tbl_Vorstand_History.FK_Riege = ?
				AND 
					tbl_Vorstand_History.bisDatum is NULL
				AND
					tbl_Vorstandsamt.Vorstand = 1
				Order by Vorname ASC, Nachname ASC
					';
		$stmt = $db->prepare($sql);
		if (!$stmt) {
			return $db->error;
		}
		$stmt->bind_param('i', $riege);
		if (!$stmt->execute()) {
			$str = $stmt->error;
			$stmt->close();
			return $str;
		}
		$stmt->bind_result($id);
		while ($stmt->fetch()) {
			$memberList[]=$id;
		}
		$stmt->close();
	}	
    return $memberList;
}
function getMembersFromShift($id,$db) {
    if (!is_object($db)) {
        return false;
    }
    if (!($db instanceof MySQLi)) {
        return false;
    }
	//Array of member
	$sql = 'SELECT 
				FK_Mitglied
			FROM
				tbl_Mitglieder, vtbl_Mitglied_Schicht
			WHERE
				tbl_Mitglieder.Mitglied_ID = vtbl_Mitglied_Schicht.FK_Mitglied
			AND
				FK_Schicht = ?
			AND
				Status = 1
			Order by Vorname ASC, Nachname ASC';
	$memberList=array();
	$stmt = $db->prepare($sql);
	if (!$stmt) {
		return $db->error;
	}
	$stmt->bind_param('i', $id);
	if (!$stmt->execute()) {
		$str = $stmt->error;
		$stmt->close();
		return $str;
	}
	$stmt->bind_result($userid);
	while ($stmt->fetch()) {
		$memberList[]=$userid;
	}
	$stmt->close();

    return $memberList;
}
function getMembersAndItemForMaterial($id,$db) {
    if (!is_object($db)) {
        return false;
    }
    if (!($db instanceof MySQLi)) {
        return false;
    }
	//Array of member
	$sql = 'SELECT 
				ID_Eintrag, FK_Mitglied, Name
			FROM
				vtbl_Mitglied_Material
			WHERE
				FK_Material = ?
			Order by FK_Mitglied ASC, Name ASC
				';
	$memberList=array();
	$stmt = $db->prepare($sql);
	if (!$stmt) {
		return $db->error;
	}
	$stmt->bind_param('i', $id);
	if (!$stmt->execute()) {
		$str = $stmt->error;
		$stmt->close();
		return $str;
	}
	$stmt->bind_result($itemId, $userid, $name);
	$counter=0;
	while ($stmt->fetch()) {
		$memberList[$userid][$counter]["ItemId"]=$itemId;
		$memberList[$userid][$counter]["Item"]=$name;
		$counter++;
	}
	$stmt->close();

    return $memberList;
}
function materialAction($db, $materialId) {
if (!($db instanceof MySQLi)) {
    	throw new Exception('Erster Parameter muss ein MySQLi-Objekt sein.');
    }
    if (!isset($materialId)) {
    	throw new Exception('Die Material ID muss gsetzt sein.');
    }	

	$outputAb="<a onclick=\"$('#materialId').val($materialId); $('#eintragen').dialog('open');\" href=\"javascript:void(0);\" >";
	$statusOutputAction=$outputAb . "Eintragen</a>";

	
    return array($statusOutputAction);
}
function getPreferredRiege($db,$mitgliedId) {
    if (!is_object($db)) {
        return false;
    }
    if (!($db instanceof MySQLi)) {
        return false;
    }
	if ($mitgliedId == ""){
		if (isset($_COOKIE['riege'])){
			return $_COOKIE['riege'];
		}
		return;
	}
    $sql = 'SELECT
                Preferred_Riege
            FROM
                tbl_Mitglieder
            WHERE
                Mitglied_ID = ?';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        return $db->error;
    }
    $stmt->bind_param('i', $mitgliedId);
    if (!$stmt->execute()) {
        $str = $stmt->error;
        $stmt->close();
        return $str;
    }
    $stmt->bind_result($riege);
    if (!$stmt->fetch()) {
        $stmt->close();
        return false;
    }
    $stmt->close();
    return $riege;
}
function setPreferredRiege($db,$mitgliedId,$riege) {
    if (!is_object($db)) {
        return false;
    }
    if (!($db instanceof MySQLi)) {
        return false;
    }
	if ($mitgliedId == ""){
		setcookie('riege', $riege);
		return false;
	}
    $sql = 'Update
				tbl_Mitglieder
			Set
                Preferred_Riege = ?
            WHERE
                Mitglied_ID = ?';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        return $db->error;
    }
    $stmt->bind_param('ii', $riege, $mitgliedId);
    if (!$stmt->execute()) {
        $str = $stmt->error;
        $stmt->close();
        return $str;
    }
    $stmt->close();
    return;
}
function presiBanner($db) {
    if (!is_object($db)) {
        return false;
    }
    if (!($db instanceof MySQLi)) {
        return false;
    }
	$output="";
	$status=1;
    $sql = 'SELECT
                Slogan
            FROM
                tbl_Presibanner
            WHERE
                Aktiv = ?';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        return $db->error;
    }
    $stmt->bind_param('i', $status);
    if (!$stmt->execute()) {
        $str = $stmt->error;
        $stmt->close();
        return $str;
    }
    $stmt->bind_result($slogan);
    while($stmt->fetch()) {
		if ($output == ""){
	    	$output.=$slogan;
		} else {
			$output.="  +++  " . $slogan;
		}
    }
    $stmt->close();
	
	$actualDate = date('m-d');
	$actualDate = "%-$actualDate 00:00:00";
    $sql = 'SELECT
                Vorname, Nachname
            FROM
                tbl_Mitglieder
            WHERE
                Geburtsdatum like ?
			AND
				Todesdatum is NULL';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        return $db->error;
    }
    $stmt->bind_param('s', $actualDate);
    if (!$stmt->execute()) {
        $str = $stmt->error;
        $stmt->close();
        return $str;
    }
    $stmt->bind_result($vorname,$nachname);
	$coutner = 0;
	$outputList = "";
    while($stmt->fetch()) {
		if ($outputList == ""){
	    	$outputList.=" " . $vorname . " " . $nachname;
		} else {
			$outputList.=", " . $vorname . " " . $nachname;
		}
		$coutner++;
    }
    $stmt->close();	
	if($coutner > 0){
		if ($output == ""){
			$output.="Wir wünschen" . $outputList . " alles Gute zum Geburtstag";
		} else {
			$output.="  +++  Wir wünschen" . $outputList . " alles Gute zum Geburtstag";
		}
	}
	
    return $output;
}
function getRumors($value,$db) {
    if (!is_object($db)) {
        return false;
    }
    if (!($db instanceof MySQLi)) {
        return false;
    }
    if($value == "all"){
		$sql = 'SELECT
	                ID_Geruecht, Name, Kommentar, DATE_FORMAT(Datum, "%d.%m.%Y")
	            FROM
	                tbl_Geruechtekueche
	            WHERE
	            	Spam = 0
	            Order by Datum DESC
					';
	    $stmt = $db->prepare($sql);
	    if (!$stmt) {
	        echo $db->error;
			return false;
	    }	
    } else {
		$sql = 'SELECT
	                ID_Geruecht, Name, Kommentar, DATE_FORMAT(Datum, "%d.%m.%Y")
	            FROM
	                tbl_Geruechtekueche
	            WHERE
	            	Spam = 0
	            Order by Datum DESC
	            Limit ?
					';
	    $stmt = $db->prepare($sql);
	    if (!$stmt) {
	        echo $db->error;
			return false;
	    }
		$stmt->bind_param('i', $value); 	
    }
    if (!$stmt->execute()) {
        $str = $stmt->error;
        $stmt->close();
        echo $str;
		return false;
    }
    $stmt->bind_result($id, $name, $comment, $date);
	$counter=0;
    while ($stmt->fetch()){
    	$geruechte[$counter]["ID"]=$id;
		$geruechte[$counter]["NAME"]=stripslashes($name);
		$geruechte[$counter]["COMMENT"]=stripslashes($comment);
		$geruechte[$counter]["DATUM"]=$date;
		$counter++;
    } 
    $stmt->close();	  
	return $geruechte;
}
function randomMember($riege,$db){
	$riegenList[]=$riege;
	$memberArrays=getMemberIdsOfRiegenAktiv($riegenList,$db);
	$memberKey=array_rand($memberArrays);
	return $memberArrays[$memberKey];
}
function getRiegenItemTypeId($typeName,$db){
	$sql="SELECT 
			ID_Riegengegenstandtyp
		FROM 
			tbl_Riegengegenstandtypen
		WHERE
			Name = ?";
	$stmt = $db->prepare($sql);
	if (!$stmt) {
		echo "$db->error  <br>";
		return $db->error;
	}
	$stmt->bind_param('s', $typeName);
	if (!$stmt->execute()) {
	    $str = $stmt->error;
	    $stmt->close();
		echo "$str";
	    return $str;
	}
	$stmt->bind_result($riegenItemTypeId);
	$stmt->store_result();
    if (!$stmt->fetch()) {
        $stmt->close();
        return false;
    }
	$stmt->close();
	return $riegenItemTypeId;
}
function getRiegenItem($member,$year,$riege,$riegenItemTypeId,$db){
	if($riegenItemTypeId != NULL){
		$sql="SELECT 
				ID_Riegengegenstand, Name, Bild, Rang
			FROM 
				tbl_Riegengegenstaende, vtbl_Mitglied_Riegengegenstand, vtbl_Gegenstand_Riege
			WHERE
				vtbl_Mitglied_Riegengegenstand.FK_Gegenstand = tbl_Riegengegenstaende.ID_Riegengegenstand
			AND
				tbl_Riegengegenstaende.ID_Riegengegenstand = vtbl_Gegenstand_Riege.FK_Riegengegenstand		
			AND
				vtbl_Mitglied_Riegengegenstand.FK_Mitglied = ?
			AND
				vtbl_Mitglied_Riegengegenstand.Jahr = ?
			AND
				vtbl_Gegenstand_Riege.FK_Riege = ?
			AND
				tbl_Riegengegenstaende.FK_Typ = ?			
			ORDER BY Rang ASC, Name ASC";
		$stmt = $db->prepare($sql);
		if (!$stmt) {
			echo "$db->error  <br>";
			return $db->error;
		}
		$stmt->bind_param('isii',  $member, $year, $riege, $riegenItemTypeId);
		if (!$stmt->execute()) {
		    $str = $stmt->error;
		    $stmt->close();
			echo "$str";
		    return $str;
		}
		$stmt->bind_result($itemId, $itemName, $itemPicture, $rank);
		$stmt->store_result();
		$stmt->fetch();
		$stmt->close();
	} else {
		//TODO check if used
		echo "Should not be used!!!<br>";
		$sql="SELECT 
				ID_Riegengegenstand, Name, Bild, Rang
			FROM 
				tbl_Riegengegenstaende, vtbl_Mitglied_Riegengegenstand, vtbl_Gegenstand_Riege
			WHERE
				vtbl_Mitglied_Riegengegenstand.FK_Gegenstand = tbl_Riegengegenstaende.ID_Riegengegenstand
			AND
				tbl_Riegengegenstaende.ID_Riegengegenstand = vtbl_Gegenstand_Riege.FK_Riegengegenstand						
			AND
				vtbl_Mitglied_Riegengegenstand.FK_Mitglied = ?
			AND
				vtbl_Mitglied_Riegengegenstand.Jahr = ?
			AND
				vtbl_Gegenstand_Riege.FK_Riege = ?		
			ORDER BY Rang ASC, Name ASC";
		$stmt = $db->prepare($sql);
		if (!$stmt) {
			echo "$db->error  <br>";
			return $db->error;
		}
		$stmt->bind_param('isi',  $member, $year, $riege);
		if (!$stmt->execute()) {
		    $str = $stmt->error;
		    $stmt->close();
			echo "$str";
		    return $str;
		}
		$stmt->bind_result($itemId, $itemName, $itemPicture, $rank);
		$stmt->store_result();
		$stmt->fetch();
		$stmt->close();		
	}
	return array($itemId,$itemName,$itemPicture,$rank);
}
function getYearAwardSettings($riege,$db){
	$sql="SELECT 
			FK_Riege, FK_Typ, Limitwert, Wert_ENT, Wert_UNENT, Vorjahr
		FROM 
			tbl_Anwesenheitsauswertungseinstellungen
		WHERE 
			FK_Riege = ?";
	$stmt = $db->prepare($sql);
	if (!$stmt) {
		echo "$db->error  <br>";
		return $db->error;
	}
	$stmt->bind_param('i',  $riege);
	if (!$stmt->execute()) {
	    $str = $stmt->error;
	    $stmt->close();
		echo "$str";
	    return $str;
	}
	$stmt->bind_result($riegeId, $typeId, $limit, $valueEnt, $valueUnent, $prevYear);
	$stmt->store_result();
	$stmt->fetch();
	$stmt->close();
	return array($typeId, $limit, $valueEnt, $valueUnent, $prevYear);	
}
function getNextItem($riege,$riegenItemTypeId,$prevItemRank,$db){
	$sql="SELECT 
			ID_Riegengegenstand, Name, Bild, Rang
		FROM 
			tbl_Riegengegenstaende, vtbl_Gegenstand_Riege
		WHERE
			tbl_Riegengegenstaende.ID_Riegengegenstand = vtbl_Gegenstand_Riege.FK_Riegengegenstand						
		AND		
			vtbl_Gegenstand_Riege.FK_Riege = ?
		AND
			FK_Typ = ?
		AND
			Rang = ?			
		ORDER BY Rang DESC";
	$stmt = $db->prepare($sql);
	if (!$stmt) {
		echo "$db->error  <br>";
		return $db->error;
	}
	$stmt->bind_param('isi',  $riege, $riegenItemTypeId, $prevItemRank);
	if (!$stmt->execute()) {
	    $str = $stmt->error;
	    $stmt->close();
		echo "$str";
	    return $str;
	}
	$stmt->bind_result($itemId, $itemName, $itemPicture, $rank);
	$stmt->store_result();
	$stmt->fetch();
	if($itemId == ""){
		$prevItemRank = $itemRank - 1;
		$stmt->bind_param('isi',  $riege, $riegenItemTypeId, $prevItemRank);
		if (!$stmt->execute()) {
		    $str = $stmt->error;
		    $stmt->close();
			echo "$str";
		    return $str;
		}
		$stmt->store_result();
		$stmt->bind_result($itemId, $itemName, $itemPicture, $rank);
		$stmt->fetch();	
	}	
	$stmt->close();
	return array($itemId,$itemName,$itemPicture);	
}
function getActualRiegenItem($member,$riege,$riegenItemTypeId,$prevItemRank,$counterAnwesend,$counterEntschuldigt,$counterUnentschuldigt,$rank,$saveFlag,$db){
	list($typeId, $limit, $valueEnt, $valueUnent, $prevYearFlag)=getYearAwardSettings($riege,$db);
	$itemId="";
	$itemName="";
	$itemPicture="";
	$saved=0;
	if($typeId == 1){
		//Rank
		if($rank <= $limit){
			list($itemId,$itemName,$itemPicture)=getNextItem($riege,$riegenItemTypeId,$rank,$db);
		} else {
			$itemName = "<img src=\"img/false_small.png\"> Bedingungen für Belohnung nicht erfüllt";
		}
	} else if($typeId == 2){
		//Attendance
		$value = $counterEntschuldigt * $valueEnt + $counterUnentschuldigt * $valueUnent;
		if($value <= $limit){
			if($prevItemRank != ""){
				list($itemId,$itemName,$itemPicture)=getNextItem($riege,$riegenItemTypeId,$prevItemRank + 1,$db);					
			} else {
				$rank=1;
				list($itemId,$itemName,$itemPicture)=getNextItem($riege,$riegenItemTypeId,$rank,$db);
			}			
		} else {
			$itemName = "Bedingungen für Belohnung nicht erfüllt";
		}
	} else {
		echo "Error type<br>";
	}
	if($saveFlag == 1){
		if($itemId != ""){
			$actualYear = date('Y');
			saveRiegenItemToMember($itemId,$member,$riege,$actualYear,$db);
			$saved=1;
		}
	}
	return array($itemId,$itemName,$itemPicture,$saved);
}
function getKbTournementRanking($trounementId,$categoryId,$db){
	$teamArray = array();

	$sql="Select distinct
				ID_Anmeldung, Verein
			from 
				tbl_Korbball_Vereine, tbl_Korbball_Spiele
			Where tbl_Korbball_Spiele.FK_Mannschaft1 = tbl_Korbball_Vereine.ID_Anmeldung
			AND tbl_Korbball_Spiele.FK_Typ = 0
			AND tbl_Korbball_Vereine.FK_Spielkategorie = ?
			AND tbl_Korbball_Spiele.FK_Turnier = ?
			
			Union
			
			Select distinct
				ID_Anmeldung, Verein
			from 
				tbl_Korbball_Vereine, tbl_Korbball_Spiele
			Where tbl_Korbball_Spiele.FK_Mannschaft2 = tbl_Korbball_Vereine.ID_Anmeldung
			AND tbl_Korbball_Spiele.FK_Typ = 0
			AND tbl_Korbball_Vereine.FK_Spielkategorie = ?
			AND tbl_Korbball_Spiele.FK_Turnier = ?";	
	$stmt = $db->prepare($sql);
	if (!$stmt) {
		echo "$db->error  <br>";
		return $db->error;
	}
	$stmt->bind_param('iiii',  $categoryId, $trounementId, $categoryId, $trounementId);
	if (!$stmt->execute()) {
	    $str = $stmt->error;
	    $stmt->close();
		echo "$str";
	    return $str;
	}
	$stmt->bind_result($teamId,$team);
	$stmt->store_result();
	while($stmt->fetch()){
		$teamArray[$teamId]=$team;
	}
	$stmt->close();	

	
	return getKbRanking($teamArray,$trounementId,$db);
}
function getKbRanking($teamArray,$trounementId,$db){
	$CONST_NAME="NAME";	
	$CONST_WIN="WIN";
	$CONST_TIE="TIE";
	$CONST_LOSE="LOSE";
	$CONST_GOAL_PLUS="GOAL_PLUS";
	$CONST_GOAL_MINUS="GOAL_MINUS";
	$CONST_POINTS="POINTS";
	$CONST_GAMES="GAMES";
	
	$rankingArray=array();
	$rankingArrayOutput=array();
	$teamIdArray=array();
	$pointArray=array();
	foreach ($teamArray as $teamId => $teamName){
		$teamIdArray[]=$teamId;
		$rankingArray[$teamId][$CONST_NAME]=$teamName;
		list($wins,$ties,$loses)=getKbMatchResultCount($trounementId,$teamId,$db);
		$rankingArray[$teamId][$CONST_WIN]=$wins;
		$rankingArray[$teamId][$CONST_TIE]=$ties;
		$rankingArray[$teamId][$CONST_LOSE]=$loses;
		$rankingArray[$teamId][$CONST_GAMES]=$wins + $ties + $loses;
		
		$rankingArray[$teamId][$CONST_GOAL_PLUS]=getKbGoal($trounementId,$teamId,0,$db);
		$rankingArray[$teamId][$CONST_GOAL_MINUS]=getKbGoal($trounementId,$teamId,1,$db);
		
		$points=calcKbPoint($wins,$ties,$loses);
		$rankingArray[$teamId][$CONST_POINTS]=$points;
		$pointArray[$teamId]=$points;
	}
	$rankArray=calcRanks($trounementId,$pointArray,$teamArray,$db);
	foreach($rankArray as $teamId => $rank){
		$rankingArrayOutput[$rank]=$rankingArray[$teamId];
	}

	ksort($rankingArrayOutput);
		
	return $rankingArrayOutput;
}
function getKbGoal($trounementId,$teamId,$type,$db){
	if($type == 0){
		$sql="SELECT 
				Sum(Tore1)
			FROM 
				tbl_Korbball_Spiele
			WHERE
				FK_Mannschaft1 = ?
			AND
				FK_Turnier = ?";
		$stmt = $db->prepare($sql);
		if (!$stmt) {
			echo "$db->error  <br>";
			return $db->error;
		}
		$stmt->bind_param('ii',  $teamId, $trounementId);
		if (!$stmt->execute()) {
		    $str = $stmt->error;
		    $stmt->close();
			echo "$str";
		    return $str;
		}
		$stmt->bind_result($goalCounter1);
		$stmt->store_result();
		$stmt->fetch();
		$stmt->close();	
		
		$sql="SELECT 
				Sum(Tore2)
			FROM 
				tbl_Korbball_Spiele
			WHERE
				FK_Mannschaft2 = ?
			AND
				FK_Turnier = ?";
		$stmt = $db->prepare($sql);
		if (!$stmt) {
			echo "$db->error  <br>";
			return $db->error;
		}
		$stmt->bind_param('ii',  $teamId, $trounementId);
		if (!$stmt->execute()) {
		    $str = $stmt->error;
		    $stmt->close();
			echo "$str";
		    return $str;
		}
		$stmt->bind_result($goalCounter2);
		$stmt->store_result();
		$stmt->fetch();
		$stmt->close();				
	} else if ($type == 1) {
		$sql="SELECT 
				Sum(Tore2)
			FROM 
				tbl_Korbball_Spiele
			WHERE
				FK_Mannschaft1 = ?
			AND
				FK_Turnier = ?";
		$stmt = $db->prepare($sql);
		if (!$stmt) {
			echo "$db->error  <br>";
			return $db->error;
		}
		$stmt->bind_param('ii',  $teamId, $trounementId);
		if (!$stmt->execute()) {
		    $str = $stmt->error;
		    $stmt->close();
			echo "$str";
		    return $str;
		}
		$stmt->bind_result($goalCounter1);
		$stmt->store_result();
		$stmt->fetch();
		$stmt->close();	
		
		$sql="SELECT 
				Sum(Tore1)
			FROM 
				tbl_Korbball_Spiele
			WHERE
				FK_Mannschaft2 = ?
			AND
				FK_Turnier = ?";
		$stmt = $db->prepare($sql);
		if (!$stmt) {
			echo "$db->error  <br>";
			return $db->error;
		}
		$stmt->bind_param('ii',  $teamId, $trounementId);
		if (!$stmt->execute()) {
		    $str = $stmt->error;
		    $stmt->close();
			echo "$str";
		    return $str;
		}
		$stmt->bind_result($goalCounter2);
		$stmt->store_result();
		$stmt->fetch();
		$stmt->close();			
	}
	return $goalCounter1 + $goalCounter2;
}
function getKbMatchResultCount($trounementId,$teamId,$db){
	$wins=0;
	$ties=0;
	$loses=0;
	$actualTime = date('H:i');
	$gameTime=getGameTimeForTournement($trounementId,$db);
	$sql="SELECT 
			Zeit, FK_Mannschaft1, FK_Mannschaft2, Tore1, Tore2
		FROM 
			tbl_Korbball_Spiele
		WHERE
			FK_Mannschaft1 = ?
		AND
			FK_Turnier = ?";
	$stmt = $db->prepare($sql);
	if (!$stmt) {
		echo "$db->error  <br>";
		return $db->error;
	}
	$stmt->bind_param('ii',  $teamId, $trounementId);
	if (!$stmt->execute()) {
	    $str = $stmt->error;
	    $stmt->close();
		echo "$str";
	    return $str;
	}
	$stmt->bind_result($start,$team1,$team2,$goal1,$goal2);
	$stmt->store_result();
	while($stmt->fetch()){
		if(!is_null($goal1) && !is_null($goal2)){
			if($goal1 == $goal2){
				$ties++;	
			} else if($goal1 > $goal2){
				if($team1 == $teamId){
					$wins++;
				} else {
					$loses++;
				}
			} else {
				if($team1 == $teamId){
					$loses++;
				} else {
					$wins++;
				}			
			}
		}
	}
	$stmt->close();
	$sql="SELECT 
			Zeit, FK_Mannschaft1, FK_Mannschaft2, Tore1, Tore2
		FROM 
			tbl_Korbball_Spiele
		WHERE
			FK_Mannschaft2 = ?
		AND
			FK_Turnier = ?";
	$stmt = $db->prepare($sql);
	if (!$stmt) {
		echo "$db->error  <br>";
		return $db->error;
	}
	$stmt->bind_param('ii',  $teamId, $trounementId);
	if (!$stmt->execute()) {
	    $str = $stmt->error;
	    $stmt->close();
		echo "$str";
	    return $str;
	}
	$stmt->bind_result($start,$team2,$team1,$goal1,$goal2);
	$stmt->store_result();
	while($stmt->fetch()){
		if(!is_null($goal1) && !is_null($goal2)){
			if($goal1 == $goal2){
				$ties++;	
			} else if($goal1 < $goal2){
				if($team1 == $teamId){
					$wins++;
				} else {
					$loses++;
				}
			} else {
				if($team1 == $teamId){
					$loses++;
				} else {
					$wins++;
				}			
			}
		} 		
	}
	$stmt->close();
	
	return array($wins,$ties,$loses);
}
function calcKbPoint($wins,$ties,$loses){
	return $wins * 2 + $ties * 1;
}
function calcRanks($trounementId,$pointArray,$teamArray,$db){
	$rankArray=array();
	arsort($pointArray);
	$samePoints=array();
	$someEqualPoints=false;
	$oldPoints=-1;
	$rank=1;
	foreach($pointArray as $teamId => $points){
		unset($pointArray[$teamId]);
		if($oldPoints != $points && $oldPoints != -1 && $someEqualPoints){
			$rankingOrder=calcOrderForEquals($trounementId,$samePoints,$db);
			foreach($rankingOrder as $teamIdOfRankingOrder){
				$rankArray[$teamIdOfRankingOrder]=$rank;
				$rank++;
			}
			$samePoints=array();
			$someEqualPoints=false;		
		}			
		if(in_array($points, $pointArray) || $oldPoints == $points){
			$samePoints[]=$teamId;
			$someEqualPoints=true;
		} else {
			$rankArray[$teamId]=$rank;
			$rank++;		
		}
		$oldPoints=$points;
	}
	if($someEqualPoints){
		$rankingOrder=calcOrderForEquals($trounementId,$samePoints,$db);
		foreach($rankingOrder as $teamIdOfRankingOrder){
			$rankArray[$teamIdOfRankingOrder]=$rank;
			$rank++;
		}		
	}
	return $rankArray;
} 
function calcOrderForEquals($trounementId,$samePoints,$db){
	for ($n = count($samePoints); $n > 1; $n--){
		for($i = 0; $i < $n - 1; $i++){
			if(checkWhichTeamIsBetter($samePoints[$i],$samePoints[$i + 1],$trounementId,$db) != $samePoints[$i]){
				$tmp = $samePoints[$i + 1];
				$samePoints[$i + 1] = $samePoints[$i];
				$samePoints[$i] =  $tmp;
			}
		}
	}
	return $samePoints;
}
function checkWhichTeamIsBetter($team1,$team2,$trounementId,$db){
	$winsTeam1=0;
	$goalDiffrence1=0;
	$goalsTeam1=0;
	$winsTeam2=0;
	$goalDiffrence2=0;
	$goalsTeam2=0;
	$sql="SELECT 
			Tore1, Tore2
		FROM 
			tbl_Korbball_Spiele
		WHERE
			FK_Mannschaft1 = ?
		AND
			FK_Mannschaft2 = ?
		AND
			FK_Turnier = ?";
	$stmt = $db->prepare($sql);
	if (!$stmt) {
		echo "$db->error  <br>";
		return $db->error;
	}
	$stmt->bind_param('iii',  $team1, $team2, $trounementId);
	if (!$stmt->execute()) {
	    $str = $stmt->error;
	    $stmt->close();
		echo "$str";
	    return $str;
	}
	$stmt->bind_result($goal1,$goal2);
	$stmt->store_result();
	while($stmt->fetch()){
		$goalsTeam1 += $goal1;
		$goalsTeam2 += $goal2;
		$goalDiffrence1=$goal1 - $goal2;
		$goalDiffrence2=$goal2 - $goal1;		
		if($goal1 > $goal2){
			$winsTeam1++;
		} else if ($goal1 < $goal2) {
			$winsTeam2++;
		}
	}
	$stmt->close();

	$sql="SELECT 
			Tore1, Tore2
		FROM 
			tbl_Korbball_Spiele
		WHERE
			FK_Mannschaft1 = ?
		AND
			FK_Mannschaft2 = ?
		AND
			FK_Turnier = ?";
	$stmt = $db->prepare($sql);
	if (!$stmt) {
		echo "$db->error  <br>";
		return $db->error;
	}
	$stmt->bind_param('iii',  $team2, $team1, $trounementId);
	if (!$stmt->execute()) {
	    $str = $stmt->error;
	    $stmt->close();
		echo "$str";
	    return $str;
	}
	$stmt->bind_result($goal1,$goal2);
	$stmt->store_result();
	while($stmt->fetch()){		
		$goalsTeam1 += $goal2;
		$goalsTeam2 += $goal1;
		$goalDiffrence1=$goal2 - $goal1;
		$goalDiffrence2=$goal1 - $goal2;		
		if($goal1 > $goal2){
			$winsTeam2++;
		} else if ($goal1 < $goal2) {
			$winsTeam1++;
		}
	}
	$stmt->close();
	
	list($wins1,$ties1,$loses1)=getKbMatchResultCount($trounementId,$team1,$db);
	$gameCount1=$wins1 + $ties1 + $loses1;
	list($wins2,$ties2,$loses2)=getKbMatchResultCount($trounementId,$team2,$db);
	$gameCount2=$wins2 + $ties2 + $loses2;
	//falls noch nicht fertig (Anzahl Spiele)
	if($gameCount1 < $gameCount2){
		return $team1;
	} else if($gameCount1 > $gameCount2){
		return $team2;
	} else {
		//direkt Begegnung
		if($winsTeam1 > $winsTeam2){
			return $team1;
		} else if($winsTeam1 < $winsTeam2){
			return $team2;
		} else {
			//Tor differenz		
			if($goalDiffrence1 > $goalDiffrence2){
				return $team1;
			} else if ($goalDiffrence1 < $goalDiffrence2){
				return $team2;
			} else {				
				//Tor Total
				if($goalsTeam1 > $goalsTeam2){
					return $team1;
				} else if($goalsTeam1 < $goalsTeam2){
					return $team2;			
				} else {
					$plusGoal1=getKbGoal($trounementId,$team1,0,$db);
					$minusGoal1=getKbGoal($trounementId,$team1,1,$db);
					$diff1= $plusGoal1 - $minusGoal1;
					$plusGoal2=getKbGoal($trounementId,$team2,0,$db);
					$minusGoal2=getKbGoal($trounementId,$team2,1,$db);
					$diff2= $plusGoal2 - $minusGoal2;
					if($diff1 > $diff2){
						return $team1;
					} else if($diff2 > $diff1){
						return $team2;
					} else {
						if($plusGoal1 > $plusGoal2){
							return $team1;
						} else if($plusGoal2 > $plusGoal1){
							return $team2;
						} else {
							//echo "ERRRRRRRRROOOOOORRRRRR<br>";
							return false;
						}
					}
				}
			}
		}		
	}

}
function getGameTimeForTournement($trounementId,$db){
	$sql="SELECT 
			Spieldauer
		FROM 
			tbl_Turnier
		WHERE 
			ID_Turnier = ?";
	$stmt = $db->prepare($sql);
	if (!$stmt) {
		echo "$db->error  <br>";
		return $db->error;
	}
	$stmt->bind_param('i',  $trounementId);
	if (!$stmt->execute()) {
	    $str = $stmt->error;
	    $stmt->close();
		echo "$str";
	    return $str;
	}
	$stmt->bind_result($gameTime);
	$stmt->store_result();
	$stmt->fetch();
	$stmt->close();	
	return $gameTime;
}
function getKbTeamsFromGame($gameId,$db){
	$sql="Select 
			ID_Spiel, FK_Typ, FK_Kategorie1, FK_Mannschaft1, FK_Mannschaft2, FK_Kategorie2, Tore1, Tore2, FK_Turnier
		from 
			tbl_Korbball_Spiele
		Where ID_Spiel = ?";
	$stmt = $db->prepare($sql);
	if (!$stmt) {
		echo "$db->error  <br>";
		return $db->error;
	}
	$stmt->bind_param('i',  $gameId);
	if (!$stmt->execute()) {
	    $str = $stmt->error;
	    $stmt->close();
		echo "$str";
	    return $str;
	}
	$stmt->bind_result($gameId,$type,$category1,$team1,$team2,$category2,$goal1,$goal2,$trounementId);
	$stmt->store_result();
	$stmt->fetch();
	$stmt->close();
	if($type == 0){
		$team1Output=getKbTeamName($team1,$db);
		$team2Output=getKbTeamName($team2,$db);
	} else if ($type == 1){
		list($team1Output,$team2Output)=evaluateTeams($team1,$category1,$team2,$category2,$trounementId,$db);
	}	
	return array($gameId,$team1Output,$team2Output,$goal1,$goal2);	

}
function getKbTeamName($teamId,$db){
	$sql="Select 
			Verein
		from 
			tbl_Korbball_Vereine
		Where ID_Anmeldung = ?";
	$stmt = $db->prepare($sql);
	if (!$stmt) {
		echo "$db->error  <br>";
		return $db->error;
	}
	$stmt->bind_param('i',  $teamId);
	if (!$stmt->execute()) {
	    $str = $stmt->error;
	    $stmt->close();
		echo "$str";
	    return $str;
	}
	$stmt->bind_result($teamName);
	$stmt->store_result();
	$stmt->fetch();
	$stmt->close();	
	return $teamName;		
}
function getKbTeamGameCategory($teamId){
	$db = Database::getConnection();
	$sql="Select 
			FK_Spielkategorie
		from 
			tbl_Korbball_Vereine
		Where ID_Anmeldung = ?";
	$stmt = $db->prepare($sql);
	if (!$stmt) {
		echo "$db->error  <br>";
		return $db->error;
	}
	$stmt->bind_param('i',  $teamId);
	if (!$stmt->execute()) {
	    $str = $stmt->error;
	    $stmt->close();
		echo "$str";
	    return $str;
	}
	$stmt->bind_result($categoryId);
	$stmt->store_result();
	$stmt->fetch();
	$stmt->close();	
	return $categoryId;		
}
function getColorOfCategory($categoryId){
	$db = Database::getConnection();
	$sql="Select 
			Farbe
		from 
			tbl_Korbball_Kategorien
		Where ID_Kategorie = ?";
	$stmt = $db->prepare($sql);
	if (!$stmt) {
		echo "$db->error  <br>";
		return $db->error;
	}
	$stmt->bind_param('i',  $categoryId);
	if (!$stmt->execute()) {
	    $str = $stmt->error;
	    $stmt->close();
		echo "$str";
	    return $str;
	}
	if(!$stmt->bind_result($color)){
	    $str = $stmt->error;
	    $stmt->close();
		echo "$str";
	    return $str;		
	}
	if(!$stmt->store_result()){
	    $str = $stmt->error;
	    $stmt->close();
		echo "$str";
	    return $str;		
	}
	if(!$stmt->fetch()){
	    $str = $stmt->error;
	    $stmt->close();
		echo "$str";
	    return $str;		
	}
	$stmt->close();	
	return $color;		
}
function getKbCategoryName($categoryId,$db){
	$sql="Select 
			Kategorie
		from 
			tbl_Korbball_Kategorien
		Where ID_Kategorie = ?";
	$stmt = $db->prepare($sql);
	if (!$stmt) {
		echo "$db->error  <br>";
		return $db->error;
	}
	$stmt->bind_param('i',  $categoryId);
	if (!$stmt->execute()) {
	    $str = $stmt->error;
	    $stmt->close();
		echo "$str";
	    return $str;
	}
	$stmt->bind_result($categoryName);
	$stmt->store_result();
	$stmt->fetch();
	$stmt->close();	
	return $categoryName;		
}
function evaluateTeams($team1,$category1,$team2,$category2,$tournamentId,$db){
	$team1Output="";
	$team2Output="";
	$CONST_NAME="NAME";
	$qualifyingFinished = true;
	$categories[]=$category1;
	if($category1 != $category2){
		$categories[]=$category2;
	}
	
	$notFilledIn = 0;
	$sql="Select distinct
				ID_Spiel, Tore1, Tore2, Zeit
			from 
				tbl_Korbball_Vereine, tbl_Korbball_Spiele
			Where tbl_Korbball_Spiele.FK_Mannschaft1 = tbl_Korbball_Vereine.ID_Anmeldung
			AND tbl_Korbball_Spiele.FK_Typ = 0
			AND tbl_Korbball_Vereine.FK_Spielkategorie = ?
			AND tbl_Korbball_Spiele.FK_Turnier = ?
			
			Union
			
			Select distinct
				ID_Spiel, Tore1, Tore2, Zeit
			from 
				tbl_Korbball_Vereine, tbl_Korbball_Spiele
			Where tbl_Korbball_Spiele.FK_Mannschaft2 = tbl_Korbball_Vereine.ID_Anmeldung
			AND tbl_Korbball_Spiele.FK_Typ = 0
			AND tbl_Korbball_Vereine.FK_Spielkategorie = ?
			AND tbl_Korbball_Spiele.FK_Turnier = ?
			
			Order by Zeit DESC";	
	$stmt = $db->prepare($sql);
	if (!$stmt) {
		echo "$db->error  <br>";
		return $db->error;
	}
	foreach($categories as $categoryId){
		$stmt->bind_param('iiii',  $categoryId, $tournamentId, $categoryId, $tournamentId);
		if (!$stmt->execute()) {
		    $str = $stmt->error;
		    $stmt->close();
			echo "$str";
		    return $str;
		}
		$stmt->bind_result($gameId,$goal1,$goal2,$time);
		$stmt->store_result();
		while($stmt->fetch()){
			if($goal1 === null || $goal2 === null){
				//echo "Game: $gameId at $time is not ready yet<br>";
				$notFilledIn++;
				break;
			}
		}
		if($notFilledIn > 0){
			$qualifyingFinished = false;
			break;
		}
	}
	$stmt->close();	
	
	if($qualifyingFinished){
		$rankingArray1=getKbTournementRanking($tournamentId,$category1,$db);
		$team1Output=$rankingArray1[$team1][$CONST_NAME];
		if($category1 == $category2){
			$rankingArray2 = $rankingArray1;
		} else {
			$rankingArray2=getKbTournementRanking($tournamentId,$category2,$db);
		}
		$team2Output=$rankingArray2[$team2][$CONST_NAME];
	} else {
		$categoryName1=getKbCategoryName($category1,$db);
		$team1Output="$team1. $categoryName1";
		$categoryName2=getKbCategoryName($category2,$db);
		$team2Output="$team2. $categoryName2";
	}
	return array($team1Output,$team2Output);
}
function getTeamFromReferee($refereeId){
	$db = Database::getConnection();
	$sql = 'SELECT
                FK_Team
            FROM
                tbl_Korbball_Schiedsrichter
            WHERE
				ID_Schiedsrichter = ?
				';
    $stmt = $db->prepare($sql);
    if (!$stmt) {
    	echo $db->error;
        return false;
    }
	if(!$stmt->bind_param('i', $refereeId)){
        $str = $stmt->error;
		echo $str;
        $stmt->close();
        return false;		
	}
    if (!$stmt->execute()) {
        $str = $stmt->error;
		echo $str;
        $stmt->close();
        return false;
    }
    $stmt->bind_result($teamId);
    if (!$stmt->fetch()) {
    	$str = $stmt->error;
		echo $str;
        $stmt->close();
        return false;
    }
    $stmt->close();	
	return $teamId;	
}
?>