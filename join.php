<?php
/*
 * IMPLEMENTATION OF: https://sessionserver.mojang.com/session/minecraft/join
 */
define('METHUSELAH_INCLUDE_CHECK', true);
require_once "yggdrasil.php";

$payload = filterPostPayload();

if(isset($payload['accessToken']) == false)
{
	responseWithError("accessToken is empty/wrong!");
}
if(isset($payload['serverId']) == false)
{
	responseWithError("serverId is empty/wrong!");
}
if(isset($payload['selectedProfile']) == false)
{
	responseWithError("selectedProfile is empty/wrong!");
}

$accessToken = $payload['accessToken'];
$serverHash  = $payload['serverId'];
$uniqueId    = $payload['selectedProfile'];

if(serverJoin($accessToken, $serverHash))
{
	$result = array("error" => "");
	response($result);
}

responseWithError("Internal server error");
