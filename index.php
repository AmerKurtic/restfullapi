<?php
require_once("db.php");
require_once("myapi.php");

if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) 
{
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try
{
	$API = new MyBeesWeb($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
}
catch(Exception $e)
{
	echo json_encode(array('error' => $e->getMessage()));
}

?>
