<?php
if(!defined('METHUSELAH_INCLUDE_CHECK'))
{
	die("Access denied!");
}

function cleanupJoins($accessToken)
{
	global $authserver;
	$query = "DELETE FROM `authserver`.`account_server_joins` WHERE `accessToken` = '$accessToken' OR `timestamp` < NOW() - INTERVAL 1 DAY;";
	$authserver->query($query)
		or responseWithError("InternalDatabaseError", $authserver->error);
	if($authserver->affected_rows > 0)
	{
		writeAccountLog(null, "Очищено " . $authserver->affected_rows . " временных учётных записей");
	}
}

function cleanupGuests()
{
	global $authserver;
	$select = "SELECT `uuid` FROM `authserver`.`accounts` WHERE `guest` = b'1' AND `timestamp` < NOW() - INTERVAL 1 DAY";
	$deleteTokens   = "DELETE FROM `authserver`.`account_server_joins` WHERE `uuid` IN ($select);";
	$deleteNames    = "DELETE FROM `authserver`.`account_names` WHERE `uuid` IN ($select);";
	$deleteAccounts = "DELETE FROM `authserver`.`accounts` WHERE `uuid` IN ($select);";
	$authserver->query($deleteTokens)
		or responseWithError("InternalDatabaseError", $authserver->error);
	$authserver->query($deleteNames)
		or responseWithError("InternalDatabaseError", $authserver->error);
	$authserver->query($deleteAccounts)
		or responseWithError("InternalDatabaseError", $authserver->error);
}
