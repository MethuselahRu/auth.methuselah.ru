<?php
/*
 * https://auth.methuselah.ru/toolbox/allNames.php
 */
define('METHUSELAH_INCLUDE_CHECK', true);
require_once "toolbox_internal.php";

$uuid = filter_input(INPUT_GET, 'uuid', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	or responseWithError(
		"Profile is not specified",
		"Good bye.");

$skin = filter_input(INPUT_GET, 'skin', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	or responseWithError(
		"Skin URL is not specified",
		"Good bye.");

if(isProfileExist($uuid) && !isProfileGuest($uuid))
{
	setProfileClothesSkin($uuid, $skin, false);
	response();
}
responseWithError("Profile doesn't not exist");
