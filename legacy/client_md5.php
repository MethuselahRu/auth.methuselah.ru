<?php
define('METHUSELAH_INCLUDE_CHECK', true);
include "legacy.php";

$username = filter_input(INPUT_GET, 'user',   FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	or die("NO");
$client   = filter_input(INPUT_GET, 'client', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	or die("NO");
$gamemd5  = filter_input(INPUT_GET, 'hash',   FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
	or die("NO");

// Тоже так-то надо бы удалить
foreach($admins as $admin)
{
	if(strtolower($username) == strtolower($admin))
	{
		die("OK");
	}
}

$md5list = array(
	// md5_file($dataFolder . 'clients/minecraft_v1.6.4_base.jar'),
	// md5_file($dataFolder . 'clients/minecraft_v1.6.4_plus.jar'),
	// md5_file($dataFolder . 'clients/minecraft_v1.6.4_director.jar'),
	// md5_file($dataFolder . 'clients/minecraft_v1.7.2_base.jar'),
	// md5_file($dataFolder . 'clients/minecraft_v1.7.2_plus.jar'),
	// md5_file($dataFolder . 'clients/minecraft_v1.7.10_base.jar'),
	// md5_file($dataFolder . 'clients/minecraft_v1.7.10_plus.jar'),
	md5_file($dataFolder . 'clients/minecraft_v1.8_base.jar'),
	md5_file($dataFolder . 'clients/minecraft_v1.8_plus.jar'),
);

foreach($md5list as $hash)
{
	if($gamemd5 === strtolower($hash))
	{
		die("OK");
	}
}
die("NO");
