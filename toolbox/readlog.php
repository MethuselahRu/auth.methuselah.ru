<?php
/*
 * https://auth.methuselah.ru/toolbox/readlog.php?uuid=<uuid>
 */
define('METHUSELAH_INCLUDE_CHECK', true);
require_once "toolbox_internal.php";

$uuid = filter_input(INPUT_GET, 'uuid', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

switch($uuid)
{
	case false:
		$log = readCommonLog();
		echo "Reading log for all (" . count($log). " messages):\r\n";
		break;
	case "users":
		$log = readUsersLog();
		echo "Reading users log (" . count($log). " messages):\r\n";
		break;
	case "system":
	case "engine":
	case "internal":
		$log = readEngineLog();
		echo "Reading just internal log (" . count($log). " messages):\r\n";
		break;
	default:
		if(!isProfileExist($uuid))
		{
			responseWithError("Wrong uuid: " . $uuid);
		}
		echo "Reading log (" . count($log). " messages) for " . $uuid . " (" . getProfileName($uuid) . "):\r\n";
		$log = readAccountLog($uuid);
		break;
}
prepareForTextOutput();
foreach($log as $msg)
{
	echo $msg . "\r\n";
}
die();
