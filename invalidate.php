<?php
/*
 * IMPLEMENTATION OF: https://authserver.mojang.com/invalidate
 */
define('METHUSELAH_INCLUDE_CHECK', true);
require_once "yggdrasil.php";

$payload = filterPostPayload();

if(isset($payload['accessToken']) == false)
{
	responseWithError("accessToken is empty!");
}
if(isset($payload['clientToken']) == false)
{
	responseWithError("clientToken is empty!");
}
$accessToken = $payload['accessToken'];
$clientToken = $payload['clientToken'];

if(invalidateToken($accessToken, $clientToken))
{
	response();
}
responseWithError(
	"Method Not Allowed",
	"Good bye.");
