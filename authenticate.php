<?php
/*
 * IMPLEMENTATION OF: https://authserver.mojang.com/authenticate
 */
define('METHUSELAH_INCLUDE_CHECK', true);
require_once "yggdrasil.php";

$payload = filterPostPayload();

$username = filter_var($payload['username'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	or responseWithError(
		"Method Not Allowed",
		"The method specified in the request is not allowed for the resource identified by the request URI.");
$password = filter_var($payload['password'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	or responseWithError(
		"Method Not Allowed",
		"The method specified in the request is not allowed for the resource identified by the request URI.");

$accessToken = md5(uniqid());
$clientToken = isset($payload['clientToken'])
	? filter_var($payload['clientToken'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	: null;
$invalidateAllClientTokens = (strlen($clientToken) == 0);

// Предзагрузка информации о выбранном проекте
$projectCode = isset($payload['project'])
	? strtoupper(filter_var($payload['project'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))
	: $methuselah["auth-default-project"];
$projectInfo = getProjectDetails($projectCode);

// Регистрация гостевого аккаунта
if(isset($payload['guest']))
{
	$guestMode = filter_var($payload['guest'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
	if($guestMode)
	{
		if($projectInfo['allow_guest_auth'])
		{
			$guestProfile = generateGuestAccount($clientToken);
			returnProfile($guestProfile);
		} else {
			// Если игрок пытается авторизоваться гостем, но проект запрещает, нет смысла продолжать выполнение скрипта
			returnWrongCredentials();
		}
	}
}
// Регистрация лицензионного аккаунта
if($projectInfo['allow_license_auth'])
{
	$mojangResponse = mojangAuthenticate($username, $password, $clientToken);
	if($mojangResponse != false && isset($mojangResponse["OK"]) == false)
	{
		$mojangProfile = generateMojangAccount($mojangResponse);
		returnProfile($mojangProfile);
	}
}
// Попытка авторизации через предоставленный скрипт проекта
if(isset($projectCode) && $projectInfo['allow_script_auth'])
{
	$script = $projectInfo['url_auth_script'];
	// Генерация зашифрованного сообщения
	$encryption = setupEncryption($projectInfo['code'], $projectInfo['secret_keyword']);
	$payload = json_encode(array(
		"username" => $username,
		"password" => $password,
		"rnd_salt" => md5(uniqid()),
	));
	$encoded = third_party_encrypt($payload, $encryption);
	// Сделать вызов на сторонний скрипт
	$projectResponse = curlPostRequest($script, $encoded);
	if(is_array($projectResponse))
	{
		$projectResponse["projectCode"] = $projectCode;
		$projectResponse["accessToken"] = $accessToken;
		$projectResponse["clientToken"] = $clientToken;
		$projectProfile = generateProjectAccount($projectResponse);
		returnProfile($projectProfile);
	}
}

returnWrongCredentials();
