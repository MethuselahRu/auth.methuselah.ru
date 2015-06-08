<?php
/*
 * https://auth.methuselah.ru/toolbox/uuid2name.php?uuid=<uuid to find current name>
 */
define('METHUSELAH_INCLUDE_CHECK', true);
require_once "toolbox_internal.php";

$name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	or responseWithError(
		"Method Not Allowed",
		"Good bye.");

// Получить профиль по имени
$uuid = findProfileByName($name);

if($uuid == false)
{
	responseWithError("Profile not found.");
}

$clothes = getProfileClothesSimplified($uuid);

header('Location: ' . $clothes['skin']);
die();
