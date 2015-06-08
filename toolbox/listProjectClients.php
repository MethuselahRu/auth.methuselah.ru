<?php
/*
 * https://auth.methuselah.ru/toolbox/uuid2name.php?uuid=<uuid to find current name>
 */
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

function getProjectClients($code)
{
	global $authserver;
	$query = "SELECT `caption`, `localized_caption` AS `captionLocalized`, `folder`, `base_version` AS `baseVersion`,
		`jar_file` AS `jarFile`, `contents_file` AS `contentsFile`,
		`main_class` AS `mainClass`,
		`game_parameters` AS `additionalGameArguments`, `java_parameters` AS `additionalJavaArguments`
		FROM `projects`.`clients` WHERE `project` = '$code' AND `public` = b'1' ORDER BY `priority` ASC, `caption` ASC;";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	$return = array();
	while($row = $result->fetch_assoc())
	{
		$return[] = $row;
	}
	return $return;
}

response(array("clients" => getProjectClients($code)));
