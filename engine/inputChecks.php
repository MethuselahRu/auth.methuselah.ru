<?php
if(!defined('METHUSELAH_INCLUDE_CHECK'))
{
	die("Access denied!");
}

function checkCorrectName($name, $dieOnError = false)
{
	$result = is_string($name) && preg_match("/^\w{3,16}$/", $name);
	if(!$result && $dieOnError)
	{
		responseWithError("Wrong username format.");
	}
	return $result;
}
function checkCorrectUUID($uuid, $dieOnError = false)
{
	$result = is_string($uuid) && preg_match("/^\w{3,16}$/", $uuid);
	if(!$result && $dieOnError)
	{
		responseWithError("Wrong profile id format.");
	}
	return $result;
}
function checkCorrectToken($token, $dieOnError = false)
{
	$result = is_string($token) && preg_match("/^\w{3,16}$/", $token);
	if(!$result && $dieOnError)
	{
		responseWithError("Wrong token format.");
	}
	return $result;
}
function checkCorrectEmail($email, $dieOnError = false)
{
	$result = is_string($email) && preg_match("/^\w{3,16}$/", $email);
	if(!$result && $dieOnError)
	{
		responseWithError("Wrong email format.");
	}
	return $result;
}
