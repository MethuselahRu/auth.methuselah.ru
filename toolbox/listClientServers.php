<?php
define('METHUSELAH_INCLUDE_CHECK', true);
require_once "toolbox_internal.php";

$payload = filterPostPayload();

$uuid = filter_var($payload['uuid'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	or responseWithError(
		"Method Not Allowed",
		"The method specified in the request is not allowed for the resource identified by the request URI.");

$accessToken = filter_var($payload['accessToken'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	or responseWithError(
		"Method Not Allowed",
		"The method specified in the request is not allowed for the resource identified by the request URI.");

$code = filter_var($payload['project'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	or responseWithError(
		"Method Not Allowed",
		"The method specified in the request is not allowed for the resource identified by the request URI.");

function getProjectServers($code)
{
	global $authserver;
	$query = "SELECT `caption`, CONCAT(`address`, ':', `port`) as `address` FROM `projects`.`servers`
		WHERE `project` = '$code' AND `production` = b'1' ORDER BY `id` ASC;";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	$return = array();
	while($row = $result->fetch_assoc())
	{
		$row['hideAddress'] = false;
		$return[] = $row;
	}
	return $return;
}

response(array("servers" => getProjectServers($code)));
