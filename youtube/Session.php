<?php
function ChangeValue() {
	if(isset($_COOKIE['youbuddies_record'])) {
		if ($_COOKIE['youbuddies_record'] == "on") setcookie("youbuddies_record" ,"off", mktime (0, 0, 0, 12, 31, 2017));
		else setcookie("youbuddies_record" ,"on", mktime (0, 0, 0, 12, 31, 2017));
	}
	else {
		setcookie("youbuddies_record" ,"on", mktime (0, 0, 0, 12, 31, 2017));
	}
}

function GetValue() {
	if(isset($_COOKIE['youbuddies_record'])) {
		return $_COOKIE['youbuddies_record'];
	}
	else {
		setcookie("youbuddies_record" ,"on", mktime (0, 0, 0, 12, 31, 2017));
		return $_COOKIE['youbuddies_record'];
	}
}
?>