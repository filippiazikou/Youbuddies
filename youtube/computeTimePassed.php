<?php 
function computeTimePassed($date) {

// 	$vDate = DateTime::createFromFormat('Y-m-d H:i:s', $date);
	$vDate = date_parse_from_format('Y-m-d H:i:s', $date);

// 	echo "vDate";
// 	print_r($vDate);

	$today = getdate();
// 	echo "today";
// 	print_r($today);

	if ($today["year"] > $vDate["year"]) {
		$passedTime = $today["year"] - $vDate["year"];
		if ($passedTime == 1)
			return "last year";
		else 
			return $passedTime . " years ago";
	}
	if ($today["mon"] > $vDate["month"]) {
		$passedTime = $today["mon"] - $vDate["month"];
		$month = $vDate["month"];
		$day = $vDate["day"];
		$yearDay = date("z", mktime(0,0,0, $month, $day, 2011))+1;
		if ($passedTime == 1) {
			$passedTimeDays = $today["yday"] - $yearDay;
			if ($passedTimeDays == 1)
				return "yesterday" ;
			else if ($passedTimeDays == 7)
				return "1 week ago" ;
			else if ($passedTimeDays == 14)
				return "2 weeks ago" ;
			else if ($passedTimeDays == 21)
				return "3 weeks ago" ;
			else if ($passedTimeDays >= 30)
				return "1 month ago" ;
			else  
				return $passedTimeDays . " days ago";
		}
		else if ($passedTime == 2) {
			$passedTime = $today["yday"] - $yearDay;
			if ($passedTime < 45)
				return "1 month ago";
		}
		else
			return $passedTime . " months ago" ;
	}

	if ($today["mday"] > $vDate["day"]) {
		$passedTime = $today["mday"] - $vDate["day"];
		if ($passedTime == 1)
			return "yesterday" ;
		else if ($passedTime == 7)
			return "1 week ago" ;
		else if ($passedTime == 14)
			return "2 weeks ago" ;
		else if ($passedTime == 21)
			return "3 weeks ago" ;
		else 
			return $passedTime . " days ago" ;
	}

	if ($today['hours'] > $vDate['hour']) {
// 		echo "found it!";
		$passedTime = $today['hours'] - $vDate['hour'];
		if ($passedTime == 1)
			return "1 hour ago" ;
		else 
			return $passedTime . " hours ago" ;
	}

	if ($today["minutes"] > $vDate["minute"]) {
		$passedTime = $today["minutes"] - $vDate["minute"];
		if ($passedTime == 1)
			return "1 minute ago" ;
		return $passedTime . " minutes ago" ;
	}

	if ($today["seconds"] > $vDate["second"]) {
		$passedTime = $today["seconds"] - $vDate["second"];
		if ($passedTime == 1)
			return "1 second ago" ;
		return $passedTime . " seconds ago" ;
	}

	return "just now" ;
}
?>