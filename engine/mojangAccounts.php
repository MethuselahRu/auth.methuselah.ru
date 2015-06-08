<?php
if(!defined('METHUSELAH_INCLUDE_CHECK'))
{
	die("Access denied!");
}

function mojangAuthenticate($username, $password, $clientToken = null)
{
	$mojangPayload = array(
		"agent"       => array("name" => "minecraft", "version" => 1),
		"username"    => $username,
		"password"    => $password,
		"clientToken" => $clientToken,
	);
	$mojangResponce = curlPostRequest('https://authserver.mojang.com/authenticate', $mojangPayload);
	return (isset($mojangResponce['error']) ? false : $mojangResponce);
}

function mojangRefresh($accessToken, $clientToken)
{
	$mojangPayload = array(
		"accessToken" => $accessToken,
		"clientToken" => $clientToken,
	);
	$mojangResponce = curlPostRequest('https://authserver.mojang.com/refresh', $mojangPayload);
	return (isset($mojangResponce['error']) ? false : $mojangResponce);
}

function mojangValidate($accessToken)
{
	$mojangPayload = array(
		"accessToken" => $accessToken,
	);
	$mojangResponce = curlPostRequest('https://authserver.mojang.com/validate', $mojangPayload);
	return (isset($mojangResponce['error']) ? false : $mojangResponce);
}

function mojangInvalidate($accessToken)
{
	
}

function mojangSighout($accessToken)
{
	
}
