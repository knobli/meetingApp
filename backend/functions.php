<?php
function createSmallerPicture($uploadFile,$path,$max,$uploadFileName){
	$image = new SimpleImage();
	$image->load($uploadFile);
	if($image->getWidth() > $max && $image->getHeight() > $max){
		if ($image->getWidth() > $image->getHeight()){
			$image->resizeToWidth($max);
		} else {
			$image->resizeToHeight($max);
		}
	}
	$image->save($path . $uploadFileName,100);
	return array(0,$uploadFileName);
}
function convertToHundredth($result) {
	$negative = false;
	if ($result < 0){
		$negative = true;
		$result = removeMinus($result);
	}
	$hmsh = '/^([\d]+)\.([\d]+)\.([\d]+)\.([\d]+)$/';
	$msh = '/^([\d]+)\.([\d]+)\.([\d]+)$/';
	$sh = '/^([\d]+)\.([\d]+)$/';
	$h = '/^\.([\d]+)$/';
    $onlyHundredths = '/^([\d]+)$/';
    $oneDigitHundredths = '/^([\d]?)$/';

    $checkOneDigit = true;

	$hours=0;
	$minutes=0;
	$seconds=0;
	$hundredths=0;
	if (preg_match($hmsh,$result,$matches)){
		$hours=$matches[1];
		$minutes=$matches[2];
		$seconds=$matches[3];
		$hundredths=$matches[4];
	} elseif (preg_match($msh,$result,$matches)){
		$minutes=$matches[1];
		$seconds=$matches[2];
		$hundredths=$matches[3];
	} elseif (preg_match($sh,$result,$matches)){
		$seconds=$matches[1];
		$hundredths=$matches[2];
	} elseif (preg_match($h,$result,$matches)) {
        $hundredths = $matches[1];
    } elseif (preg_match($onlyHundredths,$result,$matches)){
        $hundredths=$result;
        $checkOneDigit=false;
	} else {
		echo "error convertToHundredth<br>";
	}

    if ($checkOneDigit && preg_match($oneDigitHundredths,$hundredths,$matches)){
        $hundredths=$hundredths*10;
    }

	$end_value=($hours * 360000) + ($minutes * 6000) + ($seconds * 100) + $hundredths;
	if($negative){
		$end_value = addMinus($end_value);
	}	
	return $end_value;
}
function convertToTimeStamp($hundredths) {
	$restCheck = '/([\d]+)([\.\d]*)/';
	$restCheckWithoutRest = '/([\d]+)[\.]?/';
	$outputNumber = "";
	$calc1=$hundredths/100;
	if ($calc1 > 1){
		preg_match($restCheck,$calc1,$matches);
		$seconds=$matches[1];
		$leftover=$matches[2];
		$hundredthsLeft=$leftover * 100;
		//vielleicht unnötig
		$eineStelleBeiHundertstel = '/^([\d]?)$/';
		$hundredthsLeft=round($hundredthsLeft);
		if (preg_match($eineStelleBeiHundertstel,$hundredthsLeft,$matches)){
			$hundredthsLeft="0$hundredthsLeft";
		}	
		//echo "$hundredthsLeft<br>";			
		$calc2=$seconds/60;
		//echo "$calc2<br>";
		if ($calc2 > 1){
			if (preg_match($restCheck,$calc2,$matches)){
				$minutes=$matches[1];
				$leftover=$matches[2];		
			} else if (preg_match($restCheckWithoutRest,$calc2,$matches)){
				$minutes=$matches[1];
				$leftover=0;					
			}
			$secondsLeft=$leftover * 60;
			$secondsLeft=round($secondsLeft);
			if (preg_match($eineStelleBeiHundertstel,$secondsLeft,$matches)){
				$secondsLeft="0$secondsLeft";
			}	
			$calc3=$minutes/60;
			if ($calc3 > 1){
				preg_match($restCheck,$calc3,$matches);
				$hours=$matches[1];
				$leftover=$matches[2];
				$minutesLeft=$leftover * 60;
				$minutesLeft=round($minutesLeft);
				if (preg_match($eineStelleBeiHundertstel,$minutesLeft,$matches)){
					$minutesLeft="0$minutesLeft";
				}	
				$outputNumber = "$hours.$minutesLeft.$secondsLeft.$hundredthsLeft";			
			} else {
				$outputNumber = "$minutes.$secondsLeft.$hundredthsLeft";	
			}				
		} else {
			$outputNumber = "$seconds.$hundredthsLeft";	
		}		
	} else {	
		$outputNumber = $hundredths;	
	}
	
	return $outputNumber;
}
function date4mysql($date) {
     
	$date = explode(".",$date);
	if (count($date) == 3){
		$days=intval($date[0]);
		$months=intval($date[1]);
		$years=intval($date[2]);
		return $n_date=date('Y-m-d H:i:s',mktime(0,0,0,$months,$days,$years));
	} else {
		return "Invalid input date";	
	}
}
function time4mysql($date) {
     
	$date = explode(":",$date);
	$hours=intval($date[0]);
	$minutes=intval($date[1]);
	
	if ( $hours > 10){
		$hours = "0$hours";
	}
	if ( $minutes > 10){
		$minutes = "0$minutes";
	}

	return $n_time=date('H:i:s',mktime($hours,$minutes,0));
}
function datetime4mysql($datetime){
	$datetime=trim($datetime);
	
	$parts = explode(" ",$datetime);

	$date = explode(".",$parts[0]);
	$days=intval($date[0]);
	$months=intval($date[1]);
	$years=intval($date[2]);

	$time = explode(":",$parts[1]);
	$hours=intval($time[0]);
	$minutes=intval($time[1]);
	
	if ( $hours > 10){
		$hours = "0$hours";
	}
	if ( $minutes > 10){
		$minutes = "0$minutes";
	}
	
	return $n_date=date('Y-m-d H:i:s',mktime($hours,$minutes,0,$months,$days,$years));
}
function getTimeFromDate($datetime){
	$parts = explode(" ",$datetime);
	return $parts[1];
}
function valueToReadableString($result,$resultType) {
    if($result === null){
        return null;
    }
	if ($resultType == "Distanz"){
		$meter=0;
		$centimeter=0;
		
		$teile = explode(".", $result);
		
		$centimeter=array_pop($teile);
		$centimeter=round($centimeter);
		if ($teile){
			$meter=array_pop($teile);
			$meter=round($meter);
		}
		
		$outputString = "";
		if ($meter != 0){
			$outputString .= "${meter}m ";
		}
		if ($centimeter != 0 || ($meter == 0 && $centimeter == 0)){
			$outputString .= "${centimeter}cm";
		}
		
	} else {
		$hours=0;
		$minutes=0;
		$secondes=0;
		$hundredths=0;
		
		$teile = explode(".", $result);
		
		$hundredths=array_pop($teile);
		$hundredths=round($hundredths);
		if ($teile){
			$secondes=array_pop($teile);
			$secondes=round($secondes);
		}
		if ($teile){
			$minutes=array_pop($teile);
			$minutes=round($minutes);
		}
		if ($teile){
			$hours=array_pop($teile);
			$hours=round($hours);
		}
		
		$outputString = "";
		if ($hours != 0){
			if($outputString == ""){
				$outputString .= "${hours}h";
			} else {
				$outputString .= " ${hours}h";
			}
		}
		if ($minutes != 0){
			if($outputString == ""){
				$outputString .= "${minutes}min";
			} else {
				$outputString .= " ${minutes}min";
			}			
		}
		if ($secondes != 0){
			if($outputString == ""){
				$outputString .= "${secondes}s";
			} else {
				$outputString .= " ${secondes}s";
			}						
		}
		if ($hundredths != 0){
			if($outputString == ""){
				$outputString .= "${hundredths}hs";
			} else {
				$outputString .= " ${hundredths}hs";
			}									
		}
	}
	
	return $outputString;
}
function valueToReadableStringForDifference($difference,$resultType,$betTypeId){
    if($difference === null){
        return null;
    }
    $differenceWithoutMinus=removeMinus($difference);
    if ($resultType == "Distanz"){
        $differenceAsString = valueToReadableString($differenceWithoutMinus, $resultType);
    } else {
        $differenceAsString = valueToReadableString(convertToTimeStamp($differenceWithoutMinus), $resultType);
    }
    $outputAdd = getBetAdjective($difference, $resultType, $betTypeId);
    return 	$differenceAsString . $outputAdd;
}
function removeMinus($value){
	if(preg_match("/\-(.*)/", $value,$result)){
		return $result[1];
	}
    return $value;
}
function addMinus($value){
	return "-" . $value;
}
/**
 * @param $result
 * @param $resultType
 * @param $betTypeId
 * @return array
 */
function getBetAdjective($result, $resultType, $betTypeId)
{
    if ($result < 0 && ($betTypeId == 1 || $betTypeId == 2)) {
        if ($resultType == "Distanz" && $betTypeId == 1) {
            $outputAdd = " schlechter";
        } else {
            $outputAdd = " besser";
        }
    } else if ($betTypeId == 1 || $betTypeId == 2) {
        if ($resultType == "Distanz" && $betTypeId == 1) {
            $outputAdd = " besser";
        } else {
            $outputAdd = " schlechter";
        }
    } else {
        $outputAdd = "";
    }
    return $outputAdd;
}

//TODO: refactor
function uploadFile($fileLocation,$fileName,$targetFolder, $silentMode = false) {
	$fileName = replaceSpecialChars($fileName);
	$serverRoot = $_SERVER['DOCUMENT_ROOT'];
	$targetFolder = $serverRoot. "/" . $targetFolder;
	$target = $targetFolder . $fileName ;
	if(file_exists($target)){
		$date = date("YmdHis");
		$renamingFileName = $date . $fileName;
		$renamingTarget = $targetFolder . $renamingFileName;
		rename($target, $renamingTarget);
		if(!$silentMode) { ?>
			<div class="alert alert-info">Datei <?= $fileName ?> ist bereits vorhanden, alte Datei wird umbennent in <?= $renamingFileName ?></div>
		<?php }
	}
	if(move_uploaded_file($fileLocation, $target)){
		return array(0,$fileName);
	} else {
		return array(1,$fileName);
	}
}
function replaceSpecialChars($string){
	$string=preg_replace(array("/ /", "/\//", "/ä/", "/ö/", "/ü/", "/é/", "/è/", "/ê/", "/à/", "/â/","/Ö/", "/Ä/", "/Ü/"), array("_", "_", "ae", "oe", "ue", "e", "e", "e", "a", "a", "Oe", "Ae", "Ue"), $string);
	return $string;
}
function getYearFromDate($date){
	$splits=preg_split("/\./", $date);
	return $splits[2];
}
function getMonthFromDate($date){
	$splits=preg_split("/\./", $date);
	return $splits[1];
}
function getDayFromDate($date){
	$splits=preg_split("/\./", $date);
	return $splits[0];
}
if(!function_exists('date_diff')) {
    class DateInterval {
        public $y;
        public $m;
        public $d;
        public $h;
        public $i;
        public $s;
        public $invert;
		
		public static function createFromDateString($time){
			$interval = new DateInterval();
			$interval->y=0;
			$interval->m=0;
			$interval->d=0;
			$interval->h=0;
			$interval->i=0;
			$interval->s=0;
			
			if(preg_match('/([-|+]\d+)\s*year/', $time, $matches)){
				$interval->y = intval($matches[1]);
			}

			if(preg_match('/([-|+]\d+)\s*month/', $time, $matches)){
				$interval->m = intval($matches[1]);
			}
			
			if(preg_match('/([-|+]\d+)\s*day/', $time, $matches)){
				$interval->d = intval($matches[1]);
			}
			
			if(preg_match('/([-|+]\d+)\s*hour/', $time, $matches)){
				$interval->h = intval($matches[1]);
			}
			
			if(preg_match('/([-|+]\d+)\s*minute/', $time, $matches)){
				$interval->i = intval($matches[1]);
			}	

			if(preg_match('/([-|+]\d+)\s*second/', $time, $matches)){
				$interval->s = intval($matches[1]);
			}
			
			return $interval;															
		} 
        
        public function format($format) {
            $format = str_replace('%R%y', ($this->invert ? '-' : '+') . $this->y, $format);
            $format = str_replace('%R%m', ($this->invert ? '-' : '+') . $this->m, $format);
            $format = str_replace('%R%d', ($this->invert ? '-' : '+') . $this->d, $format);
            $format = str_replace('%R%h', ($this->invert ? '-' : '+') . $this->h, $format);
            $format = str_replace('%R%i', ($this->invert ? '-' : '+') . $this->i, $format);
            $format = str_replace('%R%s', ($this->invert ? '-' : '+') . $this->s, $format);
            
            $format = str_replace('%y', $this->y, $format);
            $format = str_replace('%m', $this->m, $format);
            $format = str_replace('%d', $this->d, $format);
            $format = str_replace('%h', $this->h, $format);
            $format = str_replace('%i', $this->i, $format);
            $format = str_replace('%s', $this->s, $format);
            
            return $format;
        }
    }

    function date_diff(DateTime $date1, DateTime $date2) {
        $diff = new DateInterval();
        if($date1 > $date2) {
            $tmp = $date1;
            $date1 = $date2;
            $date2 = $tmp;
            $diff->invert = true;
        }
        
        $diff->y = ((int) $date2->format('Y')) - ((int) $date1->format('Y'));
        $diff->m = ((int) $date2->format('n')) - ((int) $date1->format('n'));
        if($diff->m < 0) {
            $diff->y -= 1;
            $diff->m = $diff->m + 12;
        }
        $diff->d = ((int) $date2->format('j')) - ((int) $date1->format('j'));
        if($diff->d < 0) {
            $diff->m -= 1;
            $diff->d = $diff->d + ((int) $date1->format('t')) - 1;
        }
        $diff->h = ((int) $date2->format('G')) - ((int) $date1->format('G'));
        if($diff->h < 0) {
            $diff->d -= 1;
            $diff->h = $diff->h + 24;
        }
        $diff->i = ((int) $date2->format('i')) - ((int) $date1->format('i'));
        if($diff->i < 0) {
            $diff->h -= 1;
            $diff->i = $diff->i + 60;
        }
        $diff->s = ((int) $date2->format('s')) - ((int) $date1->format('s'));
        if($diff->s < 0) {
            $diff->i -= 1;
            $diff->s = $diff->s + 60;
        }
        
        return $diff;
    }
}
function date_add_withDateInterval($originalDate,DateInterval $addDateInterval){
	//echo $addDateInterval->format('+ %y years %m months %d days %h hours %i minutes') ."<br>";
	return date_add(new DateTime($originalDate),$addDateInterval);
}
if(!function_exists('date_add')) {
	function date_add(DateTime $originalDate,DateInterval $add){
		$date = strtotime(date("Y-m-d H:i", strtotime($originalDate->format('Y-m-d H:i:s'))) . $add->format('%y years %m months %d days %h hours %i minutes'));
		$dateFormated = date('Y-m-d H:i:s', $date);
		return new DateTime($dateFormated);
	}
}
function guid(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
        return $uuid;
    }
}
?>