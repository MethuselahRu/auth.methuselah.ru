<?php
if(!defined('METHUSELAH_INCLUDE_CHECK'))
{
	die("Access denied!");
}

function getProfileMoney($uuid)
{
	global $authserver;
	$query = "SELECT `money` FROM `authserver`.`account_money` WHERE `uuid` = '$uuid';";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError", $authserver->error);
	if($result != false)
	{
		if($result->num_rows)
		{
			$row = $result->fetch_assoc();
			return $row['money'];
		}
		return 0.0;
	}
	return false;
}
function writeMoneyLog($uuid, $money, $reason)
{
	global $authserver;
	$query = "INSERT INTO `authserver`.`account_money_history` (`uuid`, `money`, `reason`) VALUES('$uuid', $money, '$reason');";
	$authserver->query($query)
		or responseWithError("InternalDatabaseError", $authserver->error);
}
function addProfileMoney($uuid, $money = 0.0, $reason = "addProfileMoney")
{
	global $authserver;
	$query = "INSERT INTO `authserver`.`account_money` (`uuid`, `money`) VALUES('$uuid', $money) ON DUPLICATE KEY UPDATE `money` = `money` + VALUES(`money`);";
	$authserver->query($query)
		or responseWithError("InternalDatabaseError", $authserver->error);
	writeMoneyLog($uuid, $money, $reason);
	return true;
}
function spendProfileMoney($uuid, $money = 0.0, $reason = "spendProfileMoney")
{
	global $authserver;
	// Профиль существует?
	if(!isProfileExist($uuid))
	{
		return false;
	}
	// Денег достаточно?
	$available = getProfileMoney($uuid);
	if($available === false || $available < $money)
	{
		return false;
	}
	// Снять!
	$query = "UPDATE `authserver`.`account_money` SET `money` = `money` - '$money' WHERE `udid` = '$uuid';";
	$authserver->query($query)
		or responseWithError("InternalDatabaseError", $authserver->error);
	// Записать в лог
	writeMoneyLog($uuid, -$money, $reason);
	return true;
}
