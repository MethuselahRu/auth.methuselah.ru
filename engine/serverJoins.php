<?php
if(!defined('METHUSELAH_INCLUDE_CHECK'))
{
	die("Access denied!");
}

function serverJoin($accessToken, $serverHash)
{
	global $authserver;
	$query = "INSERT INTO `authserver`.`account_server_joins`(`accessToken`, `serverHash`) VALUES ('$accessToken', '$serverHash')"
		. " ON DUPLICATE KEY UPDATE `accessToken` = VALUES(`accessToken`), `serverHash` = VALUES(`serverHash`), `timestamp` = NULL;";
	$authserver->query($query)
		or responseWithError("InternalDatabaseError");
	return ($authserver->affected_rows > 0);
}
function serverHasJoined($accessToken, $serverHash)
{
	global $authserver;
	$query = "INSERT INTO `authserver`.`account_server_joins`(`accessToken`, `serverHash`) VALUES ('$accessToken', '$serverHash')"
		. " ON DUPLICATE KEY UPDATE `accessToken` = VALUES(`accessToken`), `serverHash` = VALUES(`serverHash`), `timestamp` = NULL;";
	$authserver->query($query)
		or responseWithError("InternalDatabaseError");
	return ($authserver->affected_rows > 0);
}
