<?php
/*
 * IMPLEMENTATION OF: https://authserver.mojang.com/signout
 */
define('METHUSELAH_INCLUDE_CHECK', true);
require_once "yggdrasil.php";

$payload = filterPostPayload();

$username = filter_var($payload['username'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	or responseWithError("Username is empty!");
$password = filter_var($payload['password'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	or responseWithError("Password is empty!");

// CONVERT username/password into id HERE!
$uuid = md5(strtoupper(($username . $password)));

if(true)
{
	invalidateAllTokens($uuid);
	response();
}

responseWithError(
	"ForbiddenOperationException",
	"Invalid credentials. Invalid username or password.");
