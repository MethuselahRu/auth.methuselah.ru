<?php
/*
 * IMPLEMENTATION OF:  https://api.mojang.com/profiles/minecraft
 * Read about it here: http://wiki.vg/Mojang_API#Playernames_-.3E_UUIDs
 */

define('METHUSELAH_INCLUDE_CHECK', true);
require_once "../yggdrasil.php";

$payload = filterPostPayload();

$allProfiles = getActualNicknamesAndUUIDs();

$response = array();
if(is_array($payload))
{
	foreach($allProfiles as $profile)
	{
		foreach($payload as $find)
		{
			if($profile['name'] === $find)
			{
				$profile['id'] = $profile['uuid'];
				unset($profile['uuid']);
				$response[] = $profile;
			}
		}
	}
}

response($response);
