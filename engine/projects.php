<?php
if(!defined('METHUSELAH_INCLUDE_CHECK'))
{
	die("Access denied!");
}

function getProjectDetails($code)
{
	global $authserver;
	$projectInfo = array(
		'code'               => $code,
		'secret_keyword'     => md5(uniqid()),
		'allow_license_auth' => false,
		'allow_script_auth'  => false,
		'allow_guest_auth'   => false,
	);
	if(isset($code) && strlen($code) == 5)
	{
		$query = "SELECT * FROM `projects`.`projects` WHERE `code` = '$code';";
		$result = $authserver->query($query)
			or responseWithError("InternalDatabaseError", $authserver->error);
		if($result->num_rows)
		{
			$projectInfo = $result->fetch_assoc();
		}
	}
	return $projectInfo;
}
