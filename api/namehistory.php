<?php
/*
 * IMPLEMENTATION OF:  https://api.mojang.com/user/profiles/<uuid>/names
 * Read about it here: http://wiki.vg/Mojang_API#UUID_-.3E_Name_history
 */

define('METHUSELAH_INCLUDE_CHECK', true);
require_once "../yggdrasil.php";

$subquery = strpos($uuid, "/names");
if($subquery !== false)
{
	$break = explode("/names", $uuid);
	$uuid = $break[0];
}

$history = getProfileNameHistory($uuid);
if($history == false)
{
	responseWithError("Profile not found");
}

response($history);
