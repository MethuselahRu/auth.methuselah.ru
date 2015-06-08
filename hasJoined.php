<?php
/*
 * IMPLEMENTATION OF: https://sessionserver.mojang.com/session/minecraft/hasJoined
 */
define('METHUSELAH_INCLUDE_CHECK', true);
require_once "yggdrasil.php";

$name = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	or responseWithError(
		"Method Not Allowed",
		"Good bye.");
$serverHash = filter_input(INPUT_GET, 'serverId', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	or responseWithError(
		"Method Not Allowed",
		"Good bye.");

$uuid = null;
$properties = null;

// Проверка входа на сервер через лицензионный лаунчер
$mojangRequest = http_build_query(array(
	"username" => $name,
	"serverId" => $serverHash,
));
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://sessionserver.mojang.com/session/minecraft/hasJoined' . '?' . $mojangRequest);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
$mojangResponse = curl_exec($ch);
if(curl_errno($ch) == 0)
{
	curl_close($ch);
	$mojangResponse = json_decode($mojangResponse, true);
	if(isset($mojangResponse['id']))
	{
		// Было обнаружено, что игрок пытается зайти в игру через лицензионные лаунчер и учётную запись
		$insert = registerLicenseUUID($mojangResponse['id'], $name);
		$uuid = $insert['uuid'];
		$name = $insert['name'];
		$properties = $mojangResponse['properties'];
		// Сообщаю системе, что игрок авторизовался через официальный лаунчер
		updateProvider($uuid, 'mojang', false);
	}
}

// Выдернуть информацию о игроке из нашей системы
if($uuid === null)
{
	$query = "SELECT `uuid`, `accessToken`"
		. " FROM `authserver`.`account_server_joins`  AS `j`"
		. " JOIN `authserver`.`account_access_tokens` AS `t` USING(`accessToken`)"
		. " WHERE `j`.`serverHash` = '$serverHash';";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError", $authserver->error);
	if($result->num_rows == 1)
	{
		// Информация найдена
		$row = $result->fetch_assoc();
		$uuid = $row['uuid'];
		$name = getProfileName($uuid);
		// Удаляю временную строку входа на сервер + очень устаревшие записи
		cleanupJoins($row['accessToken']);
	}
}
// Никого не найдено
if($uuid === null)
{
	responseWithError("No");
}
// Применить адское хакерство
$uuid = logAsHackedProfile($uuid);
$name = getProfileName($uuid);
$hasTextures = false;
// Если лицензия предоставляет скин, запомним его в нашей базе
if(is_array($properties))
{
	foreach($properties as $prop)
	{
		if($prop['name'] == "textures")
		{
			$decoded = json_decode(base64_decode($prop['value']), true);
			$propTextures = $decoded['textures'];
			setProfileClothes($uuid, $propTextures);
			$hasTextures = true;
		}
	}
}
if(!$hasTextures)
{
	$properties = getProfileProps($uuid, $name);
}
$response = array(
	"id"         => $uuid,
	"name"       => $name,
	"properties" => $properties,
);
response($response);
