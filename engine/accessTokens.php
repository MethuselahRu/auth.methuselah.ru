<?php
if(!defined('METHUSELAH_INCLUDE_CHECK'))
{
	die("Access denied!");
}
function replaceToken($uuid, $clientToken, $newToken)
{
	global $authserver;
	$query = "INSERT INTO `authserver`.`account_access_tokens` (`uuid`, `accessToken`, `clientToken`)"
		. " VALUES ('$uuid', '$newToken', '$clientToken')"
		. " ON DUPLICATE KEY UPDATE `accessToken` = '$newToken';";
	$authserver->query($query)
		or responseWithError("InternalDatabaseError");
	return ($authserver->affected_rows > 0);
}
function refreshToken($accessToken, $clientToken, $newToken)
{
	global $authserver;
	$query1 = "SELECT `id`, `uuid` FROM `authserver`.`account_access_tokens` WHERE `accessToken` = '$accessToken' AND `clientToken` = '$clientToken';";
	$result = $authserver->query($query1)
		or responseWithError("InternalDatabaseError");
	if($result->num_rows != 1)
	{
		return false;
	}
	$row = $result->fetch_assoc();
	$recordId = $row['id'];
	$uuid = $row['uuid'];
	$query2 = "UPDATE `authserver`.`account_access_tokens` SET `accessToken` = '$newToken' WHERE `id` = '$recordId';";
	$authserver->query($query2)
		or responseWithError("InternalDatabaseError");
	return ($authserver->affected_rows > 0) ? $uuid : false;
}
function validateAccessToken($accessToken)
{
	global $authserver;
	$query = "SELECT * FROM `authserver`.`accounts` WHERE `accessToken` = '$accessToken';";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	return ($result->num_rows > 0);
}
function invalidateToken($accessToken, $clientToken)
{
	global $authserver;
	$query = "DELETE FROM `authserver`.`account_access_tokens` WHERE `accessToken` = '$accessToken' AND `clientToken` = '$clientToken';";
	$authserver->query($query)
		or responseWithError("InternalDatabaseError");
	return ($authserver->affected_rows > 0);
}
function invalidateAllTokens($uuid)
{
	global $authserver;
	$query = "DELETE FROM `authserver`.`account_access_tokens` WHERE `uuid` = '$uuid';";
	$authserver->query($query)
		or responseWithError("InternalDatabaseError");
}
function updateProvider($uuid, $provider, $launcher)
{
	global $authserver;
	$launcherBit = ($launcher ? '1' : '0');
	$query = "UPDATE `authserver`.`account_access_tokens` SET `provider` = '$provider', `launcher` = b'$launcherBit' WHERE `uuid` = '$uuid';";
	$authserver->query($query)
		or responseWithError("InternalDatabaseError");
}
