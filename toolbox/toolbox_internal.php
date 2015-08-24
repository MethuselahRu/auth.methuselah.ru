<?php
if(!defined('METHUSELAH_INCLUDE_CHECK'))
{
	die("Access denied!");
}
require_once "../yggdrasil.php";

function getAllBusyNicknames()
{
	global $authserver;
	$query = "SELECT `uuid`, `name` FROM `authserver`.`account_names` "
		. "WHERE (`uuid`, `timestamp`) IN "
		. "(SELECT `uuid`, MAX(`timestamp`) FROM `authserver`.`account_names` GROUP BY `uuid`);";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	$names = array();
	while($row = $result->fetch_row())
	{
		$names[] = array('uuid' => $row[0], 'name' => $row[1]);
	}
	return $names;
}

function generateProjectCode()
{
	return strtoupper(substr(md5(uniqid("", true)), 0, 5));
}

$voteMoneyToAdd = 100;

function voteOnTopAccepted($uuid, $top = null)
{
	// Настройки подключения к БД игрового сервера. Да, так быть не должно :)
	$dbHostname = $methuselah["primary-hostname"];
	$dbDatabase = $methuselah["primary-database"];
	$dbUsername = $methuselah["primary-username"];
	$dbPassword = $methuselah["primary-password"];
	
	// Настройки поощрения за голосования
	global $voteMoneyToAdd;
	$voteEconomyTable       = 'fe_accounts';
	$voteEconomyColumnUser  = 'name';
	$voteEconomyColumnUUID  = 'uuid';
	$voteEconomyDashedUUID  = true;
	$voteEconomyColumnMoney = 'money';
	
	// Уточняем детали запроса в БД
	$target = $voteEconomyDashedUUID
		? preg_replace("/([0-9a-f]{8})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{12})/", "$1-$2-$3-$4-$5", $uuid)
		: $uuid;
		
	// Соединиться и выполнить денежное поощрение
	$economyConn = mysqli_connect($dbHostname, $dbUsername, $dbPassword, $dbDatabase);
	if(mysqli_connect_errno())
	{
		responseWithError("Internal server error (topcraft 1)");
	}
	$query = "UPDATE `$dbDatabase`.`$voteEconomyTable` SET `$voteEconomyColumnMoney` = `$voteEconomyColumnMoney` + $voteMoneyToAdd"
		. " WHERE `$voteEconomyColumnUUID` = '$target';";
	$economyConn->query($query)
		or responseWithError("Internal server error (topcraft 2)");
	$economyConn->close();
	
	// Дополнительное поощрение: по 1 реальному рублю за 1 голос
	if($top != null)
	{
		addProfileMoney($uuid, 1.0, "Поощрение за голосование на топе $top");
	}
}
