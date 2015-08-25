<?php
define('METHUSELAH_INCLUDE_CHECK', true);
include "legacy.php";

$md5list = array(
	// Windows XP, 7, 8, 10
	md5_file($dataFolder . 'launcher/Launcher.exe'),
	// Linux, OSX, etc.
	md5_file($dataFolder . 'launcher/Launcher.jar'),
	// IDE-guided
	"48617665204c61756e63686572206265656e207374617274656420756e646572204944453f",
);

$variable = filter_input(INPUT_POST, 'launcherHash');
$launcher = isset($variable) ? strtolower($variable) : "";

foreach($admins as $admin)
{
	if($username == $admin)
	{
		die("OK");
	}
}
foreach($md5list as $hash)
{
	if($launcher == $hash)
	{
		die("OK");
	}
}
die("Corrupted");
