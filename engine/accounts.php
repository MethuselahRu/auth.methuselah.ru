<?php
if(!defined('METHUSELAH_INCLUDE_CHECK'))
{
	die("Access denied!");
}
function createProfile($uuid, $guest = null)
{
	global $authserver;
	$guest = (isset($guest) && $guest);
	// Регистрация аккаунта в БД
	$query = "INSERT INTO `authserver`.`accounts` (`uuid`, `guest`) VALUES('$uuid', b'$guest');";
	// Запись в общий лог
	writeAccountLog($uuid, "Создана учётная запись " . $uuid . ($guest ? " (гостевая)" : ""));
	$authserver->query($query)
		or responseWithError("InternalDatabaseError", $authserver->error);
}
function isProfileExist($uuid)
{
	global $authserver;
	$query = "SELECT `uuid` FROM `authserver`.`accounts` WHERE `uuid` = '$uuid';";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	return $result->num_rows > 0;
}
function isProfileGuest($uuid)
{
	global $authserver;
	$query = "SELECT `guest` FROM `authserver`.`accounts` WHERE `uuid` = '$uuid';";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	if($result->num_rows > 0)
	{
		$row = $result->fetch_assoc();
		return $row['guest'];
	}
	return false;
}
function getProfileName($uuid)
{
	global $authserver;
	$query = "SELECT `name` FROM `authserver`.`account_names` WHERE `uuid` = '$uuid' ORDER BY `timestamp` DESC LIMIT 0, 1;";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	if($result->num_rows > 0)
	{
		$row = $result->fetch_assoc();
		return $row['name'];
	}
	return "Unnamed_" . substr(md5($uuid), 0, 8);
}
function findProfileByName($name)
{
	global $authserver;
	$query = "SELECT `uuid` FROM `authserver`.`account_names`"
		. "WHERE	(`uuid`, `timestamp`) IN (SELECT `uuid`, MAX(`timestamp`) FROM `authserver`.`account_names` GROUP BY `uuid`)"
		. "AND `name` = '$name';";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	if($result->num_rows)
	{
		$row = $result->fetch_assoc();
		return $row['uuid'];
	}
	return false;
}
function isNameFree($name)
{
	return (findProfileByName($name) == false);
}
function changeProfileName($uuid, $newName)
{
	global $authserver;
	$oldName = getProfileName($uuid);
	$query = "INSERT INTO `authserver`.`account_names` (`uuid`, `name`) VALUES ('$uuid', '$newName');";
	$authserver->query($query)
		or responseWithError("InternalDatabaseError", $authserver->error);
	// Запись в общий лог
	writeAccountLog($uuid, "Установлено новое имя учётной записи " . $uuid . ": " . $oldName . " → " . $newName);
}
function getProfileNameHistory($uuid)
{
	global $authserver;
	$query = "SELECT `name`, unix_timestamp(`timestamp`) as `changedToAt` FROM `authserver`.`account_names` "
		. "WHERE `uuid` = '$uuid' ORDER BY `timestamp` DESC;";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError", $authserver->error);
	if($result->num_rows)
	{
		$names = array();
		while($row = $result->fetch_assoc())
		{
			$names[] = $row;
		}
		return $names;
	}
	return false;
}
function bindProfileLicense($uuid, $LicenseUUID)
{
	global $authserver;
	$query = "INSERT IGNORE INTO `authserver`.`account_mojang` (`uuid`, `license`) VALUES ('$uuid', '$LicenseUUID');";
	$authserver->query($query)
		or responseWithError("InternalDatabaseError");
	// Запись в общий лог
	writeAccountLog($uuid, "К учётной записи " . $uuid . " присоединена лицензионная " . $LicenseUUID);
}
function bindProfileThirdParty($uuid, $tp, $tpid)
{
	global $authserver;
	$query = "INSERT INTO `authserver`.`account_thirdparty` (`uuid`, `thirdparty`, `thirdparty_id`) VALUES ('$uuid', '$tp', '$tpid');";
	$authserver->query($query)
		or responseWithError("InternalDatabaseError");
	// Запись в общий лог
	writeAccountLog($uuid, "К учётной записи " . $uuid . " присоединена проектная " . $tp . ":" . $tpid);
}
function getProfile($uuid, $includeProps = false)
{
	if(isProfileExist($uuid))
	{
		$name = getProfileName($uuid);
		$response = array(
			"id"   => $uuid,
			"name" => $name,
		);
		if(isProfileGuest($uuid))
		{
			$response['guest'] = true;
		}
		if($includeProps)
		{
			$response['properties'] = getProfileProps($uuid, $name);
		}
		return $response;
	}
	return false;
}
function getProfileProps($uuid, $name)
{
	// Установка текстур игроку
	$propTextures = array(
		'timestamp'   => time() * 1000,
		'profileId'   => $uuid,
		'profileName' => $name,
		'isPublic'    => true,
		'textures'    => getProfileClothes($uuid),
	);
	$properties = array(
		array(
			"name"      => "textures",
			"value"     => base64_encode(json_encode($propTextures)),
			"signature" => "signed © methuselah.ru",
		),
	);
	return $properties;
}
function getProfileClothes($uuid)
{
	global $authserver;
	$defaultSkinGuests     = "http://voxile.ru/skins/default_guest.png";
	$defaultSkinRegistered = "http://voxile.ru/skins/dinnerbone.png";
	$query = "SELECT `skin`, `cape`, `slim` FROM `authserver`.`account_props` WHERE `uuid` = '$uuid';";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	if($result->num_rows == 0)
	{
		return array(
			'SKIN' => array('url' => (isProfileGuest($uuid) ? $defaultSkinGuests : $defaultSkinRegistered)),
			);
	}
	$row = $result->fetch_assoc();
	$skin = null;
	if(isset($row['skin']))
	{
		$skin = array('url' => $row['skin']);
		if($row['slim'] == 1)
		{
			$skin['model'] = "slim";
		}
	}
	$cape = isset($row['cape']) ? array('url' => $row['cape']) : null;
	return array('SKIN' => $skin, 'CAPE' => $cape);
}
function getProfileClothesSimplified($uuid)
{
	global $authserver;
	$defaultSkinGuests     = "http://voxile.ru/skins/default_guest.png";
	$defaultSkinRegistered = "http://voxile.ru/skins/dinnerbone.png";
	$query = "SELECT `skin`, `cape`, `slim` FROM `authserver`.`account_props` WHERE `uuid` = '$uuid';";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	if($result->num_rows)
	{
		return $result->fetch_assoc();
	}
	return array(
		'skin' => isProfileGuest($uuid) ? $defaultSkinGuests : $defaultSkinRegistered,
	);
}
function setProfileClothesSkin($uuid, $skinURL, $slim)
{
	global $authserver;
	$query = "INSERT INTO `authserver`.`account_props`(`uuid`, `skin`, `slim`) VALUES('$uuid', '$skinURL', b'$slim') ON DUPLICATE KEY UPDATE `skin` = '$skinURL', `slim` = b'$slim';";
	$authserver->query($query)
		or responseWithError("InternalDatabaseError");
	/*
	// Запись в общий лог
	$name = getProfileName($uuid);
	writeAccountLog($uuid, "Учётной записи " . $uuid . " (" . $name . ") установлен скин: " . $skinURL);
	*/
}
function setProfileClothesCape($uuid, $capeURL)
{
	global $authserver;
	$query = "INSERT INTO `authserver`.`account_props`(`uuid`, `cape`) VALUES('$uuid', '$capeURL') ON DUPLICATE KEY UPDATE `cape` = '$capeURL';";
	$authserver->query($query)
		or responseWithError("InternalDatabaseError");
	/*
	// Запись в общий лог
	$name = getProfileName($uuid);
	writeAccountLog($uuid, "Учётной записи " . $uuid . " (" . $name . ") установлен плащ: " . $capeURL);
	*/
}
function setProfileClothes($uuid, $propTextures)
{
	if(isset($propTextures['SKIN']))
	{
		$skin = $propTextures['SKIN'];
		$url  = $skin['url'];
		$slim = isset($skin['model']);
		setProfileClothesSkin($uuid, $url, $slim);
	}
	if(isset($propTextures['CAPE']))
	{
		$cape = $propTextures['CAPE'];
		$url  = $cape['url'];
		setProfileClothesCape($uuid, $url);
	}
}
function logAsHackedProfile($uuid)
{
	global $authserver;
	$query = "SELECT `target_uuid` FROM `authserver`.`account_hacks` WHERE `hacker_uuid` = '$uuid';";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError", $authserver->error);
	if($result->num_rows)
	{
		$row = $result->fetch_assoc();
		return $row['target_uuid'];
	}
	return $uuid;
}
