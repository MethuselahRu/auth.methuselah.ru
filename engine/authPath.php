<?php
if(!defined('METHUSELAH_INCLUDE_CHECK'))
{
	die("Access denied!");
}

function returnProfile($profile)
{
	// Успешная аутентификация
	global $invalidateAllClientTokens;
	if($invalidateAllClientTokens)
	{
		invalidateAllTokens($profile['uuid']);
	}
	replaceToken($profile['uuid'], $profile['clientToken'], $profile['accessToken']);
	updateProvider($profile['uuid'], $profile['provider'], $profile['provider'] != "mojang");
	// Настало время охуительных историй
	$profile['uuid'] = logAsHackedProfile($profile['uuid']);
	$profile['name'] = getProfileName($profile['uuid']);
	// Выбранный профиль
	$gameProfile = array(
		"id"     => $profile['uuid'],
		"name"   => $profile['name'],
		"legacy" => 'false',
	);
	// Конечный результат
	$response = array(
		"accessToken"       => $profile['accessToken'],
		"clientToken"       => $profile['clientToken'],
		"selectedProfile"   => $gameProfile,
		"availableProfiles" => array($gameProfile),
		"provider"          => $profile['provider'],
		"role"              => $profile['role'],
	);
	response($response);
}
function returnWrongCredentials()
{
	responseWithError(
		"ForbiddenOperationException",
		"Invalid credentials. Invalid username or password.");
}

function generateGuestAccount($clientToken)
{
	if(strlen($clientToken) == 0)
	{
		$clientToken = md5(uniqid());
	}
	$accessToken = md5(strtoupper($clientToken));
	$GuestUUID = generateUserUUID(true);
	$GuestName = "Guest_" . substr($GuestUUID, 0, 10);
	// Регистрация аккаунта в БД
	createProfile($GuestUUID, true);
	changeProfileName($GuestUUID, $GuestName);
	// Возврат профиля
	return array(
		"uuid"        => $GuestUUID,
		"name"        => $GuestName,
		"clientToken" => $clientToken,
		"accessToken" => $accessToken,
		"provider"    => "guest",
		"role"        => "guest",
	);
}
function generateProjectAccount($projectResponse)
{
	global $authserver;
	if(strlen($projectResponse['clientToken']) == 0)
	{
		$projectResponse['clientToken'] = md5(uniqid());
	}
	$code = $projectResponse['projectCode'];
	$tpid = $projectResponse['id'];
	$name = $projectResponse['name'];
	$query = "SELECT `uuid` FROM `authserver`.`account_thirdparty` WHERE `thirdparty` = '$code' AND `thirdparty_id` = '$tpid';";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError", $authserver->error);
	if($result->num_rows)
	{
		$row  = $result->fetch_assoc();
		$uuid = $row['uuid'];
		$name = getProfileName($uuid);
	} else {
		$uuid = generateUserUUID(true);
		createProfile($uuid);
		if(!isNameFree($name))
		{
			// Генерируем новый уникальный ник
			$name = strtoupper($code) . "_" . substr($uuid, 0, 10);
		}
		// Регистрируем имя учётной записи
		changeProfileName($uuid, $name);
		// Регистрируем связку UUID-а с посторонним проектом
		bindProfileThirdParty($uuid, $code, $tpid);
	}
	// Возврат профиля
	return array(
		"uuid"        => $uuid,
		"name"        => $name,
		"clientToken" => $projectResponse['clientToken'],
		"accessToken" => $projectResponse['accessToken'],
		"provider"    => "project",
		"role"        => "player",
	);
}
function generateMojangAccount($mojangResponse)
{
	$accessToken   = $mojangResponse['accessToken'];
	$clientToken   = $mojangResponse['clientToken'];
	$mojandProfile = $mojangResponse['selectedProfile'];
	$licenseUUID   = $mojandProfile['id'];
	$licenseName   = $mojandProfile['name'];
	// Обновление в БД
	$insert = registerLicenseUUID($licenseUUID, $licenseName);
	$uuid = $insert['uuid'];
	$name = $insert['name'];
	replaceToken($uuid, $clientToken, $accessToken);
	return array(
		"uuid"        => $uuid,
		"name"        => $name,
		"clientToken" => $clientToken,
		"accessToken" => $accessToken,
		"provider"    => "mojang",
		"role"        => "player",
	);
}
function registerLicenseUUID($LicenseUUID, $LicenseName)
{
	global $authserver;
	$uuid = $LicenseUUID;
	$name = $LicenseName;
	// Есть ли в БД запись с таким LicenseUUID?
	$query1 = "SELECT `uuid` FROM `authserver`.`account_mojang` WHERE `license` = '$LicenseUUID';";
	$result1 = $authserver->query($query1)
		or responseWithError("InternalDatabaseError");
	// Да, запись есть
	if($result1->num_rows)
	{
		// Вернём её uuid и имя
		$row  = $result1->fetch_assoc();
		$uuid = $row['uuid'];
		$name = getProfileName($uuid);
		// Если обновилось имя, мы можем попробовать обновить его у себя
		if($LicenseName != $name && isNameFree($LicenseName))
		{
			$name = $LicenseName;
			// Регистрируем обновлённое имя учётной записи
			changeProfileName($uuid, $name);
		}
	} else {
		// Попытаемся создать новую запись с указанными LicenseName и LicenseUUID
		// Проверим возможность использовать LicenseUUID как UUID
		$query2 = "SELECT `uuid` FROM `authserver`.`accounts` WHERE `uuid` = '$LicenseUUID';";
		$result2 = $authserver->query($query2)
			or responseWithError("InternalDatabaseError");
		if($result2->num_rows)
		{
			// Генерируем новый уникальный UUID, который ещё никем не занят
			$uuid = generateUserUUID(true);
		}
		// Регистрируем UUID и прицепляем ему LicenseUUID
		createProfile($uuid);
		bindProfileLicense($uuid, $LicenseUUID);
		// Проверим возможность использовать LicenseName как Name
		if(!isNameFree($name))
		{
			// Генерируем новый уникальный ник
			$name = "License_" . substr($LicenseUUID, 0, 8);
		}
		// Регистрируем имя учётной записи
		changeProfileName($uuid, $name);
	}
	return array("uuid" => $uuid, "name" => $name);
}
