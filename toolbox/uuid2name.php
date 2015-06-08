<?php
/*
 * https://auth.methuselah.ru/toolbox/uuid2name.php?uuid=<uuid to find current name>
 */
define('METHUSELAH_INCLUDE_CHECK', true);
require_once "toolbox_internal.php";

$uuid = filter_input(INPUT_GET, 'uuid', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	or responseWithError(
		"Method Not Allowed",
		"Good bye.");

$result = getProfileName($uuid);

die(($result != false ? $result : "NOT FOUND"));
