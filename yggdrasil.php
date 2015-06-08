<?php
define('METHUSELAH_INCLUDE_CHECK', true);
if(!defined('METHUSELAH_INCLUDE_CHECK'))
{
	die("Access denied!");
}
// Settings
require_once 'config/yggdrasilSettings.php';

// Check for debugging mode
if($methuselah["debug"])
{
	ini_set('error_reporting',        E_ALL);
	ini_set('display_errors',         true);
	ini_set('display_startup_errors', true);
}

// Include engine files
require_once 'engine/curls.php';
require_once 'engine/accounts.php';
require_once 'engine/money.php';
require_once 'engine/mojangAccounts.php';
require_once 'engine/thirdPartyScripts.php';
require_once 'engine/accessTokens.php';
require_once 'engine/authPath.php';
require_once 'engine/serverJoins.php';
require_once 'engine/cleanups.php';
require_once 'engine/projects.php';
require_once 'engine/accountLog.php';
require_once 'engine/responses.php';

// Набор нескольких постоянных соединений
$authserver = mysqli_connect(
	$methuselah["mysql-hostname"],
	$methuselah["mysql-username"],
	$methuselah["mysql-password"],
	$methuselah["mysql-database"], 3306)
	or responseWithError("Cannot connect to database (authentication server).");
$authserver->set_charset("utf8");


function filterPostPayload()
{
	return json_decode(file_get_contents("php://input"), true);
}
function getBusyNicknames()
{
	global $authserver;
	$query = "SELECT `name` FROM `authserver`.`account_names`
		WHERE (`uuid`, `timestamp`) IN
		( SELECT `uuid`, MAX(`timestamp`) FROM `authserver`.`account_names` GROUP BY `uuid` );";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	$names = array();
	while($row = $result->fetch_row())
	{
		$names[] = $row[0];
	}
	return $names;
}
function getActualNicknamesAndUUIDs()
{
	global $authserver;
	$query = "SELECT `uuid`, `name` FROM `authserver`.`account_names`
		WHERE (`uuid`, `timestamp`) IN
		( SELECT `uuid`, MAX(`timestamp`) FROM `authserver`.`account_names` GROUP BY `uuid` );";
	$result = $authserver->query($query)
		or responseWithError("InternalDatabaseError");
	$names = array();
	while($row = $result->fetch_row())
	{
		$names[] = array('uuid' => $row[0], 'name' => $row[1]);
	}
	return $names;
}
function generateSessionId()
{
	srand(time());
	$randNum = rand(1000000000, 2147483647) . rand(1000000000, 2147483647) . rand(0, 9);
	return $randNum;
}
function generateUserUUID($compact = false)
{
	return sprintf(
		(($compact == false)
			? '%04x%04x-%04x-%04x-%04x-%04x%04x%04x'
			: '%04x%04x%04x%04x%04x%04x%04x%04x'),
		// 32 bits for "time_low"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),
		// 16 bits for "time_mid"
		mt_rand(0, 0xffff),
		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand(0, 0x0fff) | 0x4000,
		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand(0, 0x3fff) | 0x8000,
		// 48 bits for "node"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
	);
}
function checkPass($realPass, $password)
{
	if(strlen($realPass) == 32)
	{
		$cp = md5($password);
	} else {
		if(strpos($realPass, ' $SHA$') != false)
		{
			$ar = preg_split("/\\$/", $realPass);
			$salt = $ar[2];
			$cp = '$SHA$' . $salt . '$' . hash('sha256', hash('sha256', $password) . $salt);
		} else {
			$saltPos = (strlen($password) >= strlen($realPass) ? strlen($realPass) : strlen($password));
			$salt = substr($realPass, $saltPos, 12);
			$hash = hash('whirlpool', $salt . $password);
			$cp = substr($hash, 0, $saltPos) . $salt . substr($hash, $saltPos);
		}
	}
	return ($realPass === $cp);
}
