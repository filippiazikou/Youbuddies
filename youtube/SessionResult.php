<?php
require 'Session.php';
$return['msg'] = 'ok';
if ($_POST['request'] == "GetValue") $return['msg'] = GetValue() ;
else ChangeValue();

echo json_encode($return);
?>