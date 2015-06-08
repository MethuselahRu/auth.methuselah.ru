<?php
/*
 * https://auth.methuselah.ru/toolbox/isNameFree.php?name=<name to check>
 */
define('METHUSELAH_INCLUDE_CHECK', true);
require_once "toolbox_internal.php";

$name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	or responseWithError(
		"Method Not Allowed",
		"Good bye.");

die(isNameFree($name) ? "FREE" : "BUSY");
