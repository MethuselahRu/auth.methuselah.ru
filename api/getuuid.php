<?php
/*
 * IMPLEMENTATION OF:  https://api.mojang.com/users/profiles/minecraft/<username>?at=<timestamp>
 * Read about it here: http://wiki.vg/Mojang_API#Username_-.3E_UUID_at_time
 */

define('METHUSELAH_INCLUDE_CHECK', true);
require_once "../yggdrasil.php";

$subquery = strpos($name, "?");
if($subquery !== false)
{
	$break = explode("?", $uuid);
	$name = $break[0];
}

$uuid = findProfileByName($name);

if($uuid == false)
{
	responseWithError("Profile not found");
}

response(getProfile($uuid, false));
