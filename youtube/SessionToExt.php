<?php
require 'Session.php';
$ReturnValue = GetValue();
echo $_GET["jsoncallback"] . "({\"Answer\":\"". $ReturnValue . "\"})";
?>