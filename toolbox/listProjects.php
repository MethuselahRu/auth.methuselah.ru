<?php
define('METHUSELAH_INCLUDE_CHECK', true);
require_once "toolbox_internal.php";

function getProjectsList()
{
	global $authserver;
	$query = "SELECT `code`, `caption` FROM `projects`.`projects` WHERE `active` = b'1';";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	$return = array();
	while($row = $result->fetch_assoc())
	{
		$return[] = $row;
	}
	return $return;
}

response(array("projects" => getProjectsList()));
