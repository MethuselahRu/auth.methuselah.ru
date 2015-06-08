<?php
if(!defined('METHUSELAH_INCLUDE_CHECK'))
{
	die("Access denied!");
}

function writeAccountLog($uuid, $message)
{
	global $authserver;
	$query = "INSERT INTO `authserver`.`account_log` (`uuid`, `message`) VALUES ('$uuid', '$message');";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	return ($result != false);
}
function readAccountLog($uuid)
{
	global $authserver;
	$query = "SELECT `timestamp`, `message` FROM `authserver`.`account_log` WHERE `uuid` = '$uuid' ORDER BY `timestamp` DESC LIMIT 0, 200;";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	$log = array();
	while($row = $result->fetch_assoc())
	{
		$log[] = "[" . $row['timestamp'] . "] " . $row['message'];
	}
	return $log;
}
function readCommonLog()
{
	global $authserver;
	$query = "SELECT `timestamp`, `message` FROM `authserver`.`account_log` ORDER BY `timestamp` DESC LIMIT 0, 200;";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	$log = array();
	while($row = $result->fetch_assoc())
	{
		$log[] = "[" . $row['timestamp'] . "] " . $row['message'];
	}
	return $log;
}
function readUsersLog()
{
	global $authserver;
	$query = "SELECT `timestamp`, `message` FROM `authserver`.`account_log` WHERE `uuid` != '' ORDER BY `timestamp` DESC LIMIT 0, 200;";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	$log = array();
	while($row = $result->fetch_assoc())
	{
		$log[] = "[" . $row['timestamp'] . "] " . $row['message'];
	}
	return $log;
}
function readEngineLog()
{
	global $authserver;
	$query = "SELECT `timestamp`, `message` FROM `authserver`.`account_log` WHERE `uuid` = '' ORDER BY `timestamp` DESC LIMIT 0, 200;";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	$log = array();
	while($row = $result->fetch_assoc())
	{
		$log[] = "[" . $row['timestamp'] . "] " . $row['message'];
	}
	return $log;
}
