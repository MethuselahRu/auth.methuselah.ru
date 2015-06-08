<?php
/*
 * IMPLEMENTATION OF: https://authserver.mojang.com/refresh
 */
define('METHUSELAH_INCLUDE_CHECK', true);
require_once "yggdrasil.php";

$payload = filterPostPayload();

$accessToken = $payload['accessToken']
	or responseWithError("accessToken is empty!");
$clientToken = $payload['clientToken']
	or responseWithError("clientToken is empty!");

// Обновление лицензионного accessToken-а
$mojangResponse = mojangRefresh($accessToken, $clientToken);
// Новый accessToken
$newToken = (($mojangResponse != false) ? $mojangResponse['accessToken'] : md5(uniqid()));
// Обновление в БД и получение учётной записи
$uuid = refreshToken($accessToken, $clientToken, $newToken);
// Возврат результата выполнения
if($uuid != false)
{
	$result = array(
		"accessToken" => $newToken,
		"clientToken" => $clientToken,
		"selectedProfile" => array(
			"id"     => $uuid,
			"name"   => getProfileName($uuid),
			/* "legacy" => 'false', */
		),
	);
	response($result);
}

responseWithError(
	"ForbiddenOperationException",
	"Invalid accessToken or clientToken.");
