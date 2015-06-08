<?php
/*
 * IMPLEMENTATION OF: https://authserver.mojang.com/validate
 */
define('METHUSELAH_INCLUDE_CHECK', true);
require_once "yggdrasil.php";

$payload = filterPostPayload();

$accessToken = $payload['accessToken']
	or responseWithError("accessToken is empty!");

/* TO DO
$responce = mojangValidate($accessToken);
if($responce != false)
{
	$responce = "WRONG ACCESS TOKEN";
}
*/

// Проверить правильность токена
$query = "SELECT `name` FROM `authserver`.`account_access_tokens` WHERE `accessToken` = '$accessToken';";
$result = $authserver->query($query)
	or responseWithError("Wrong accessToken!");
if($result->num_rows != 1)
{
	responseWithError("Wrong accessToken!");
}

response();
