<?php
/*
 * IMPLEMENTATION OF:  https://sessionserver.mojang.com/session/minecraft/profile/<uuid>
 * Read about it here: http://wiki.vg/Mojang_API#UUID_-.3E_Profile_.2B_Skin.2FCape
 */

define('METHUSELAH_INCLUDE_CHECK', true);
require_once "../yggdrasil.php";

$subquery = strpos($uuid, "?");
if($subquery !== false)
{
	$break = explode("?", $uuid);
	$uuid = $break[0];
}

$profile = getProfile($uuid, true);

if($profile != false)
{
	response($profile);
}

responseWithError("Wrong profile identifier.");
