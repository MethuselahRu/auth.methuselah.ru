<?php
define('METHUSELAH_INCLUDE_CHECK', true);
define('METHUSELAH_INCLUDE_VOTES_CHECK', true);
require_once "../toolbox_internal.php";

// Это должно быть где-то в опциональных настройках проекта
$topcraft_secret = $methuselah["votes-topcraft-secret"];

// Чтение входных данных
$project   = filter_input(INPUT_GET,  'project',   FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
$username  = filter_input(INPUT_POST, 'username',  FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
$timestamp = filter_input(INPUT_POST, 'timestamp', FILTER_VALIDATE_INT);
$token     = filter_input(INPUT_POST, 'signature', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

// Проверка корректности кода проекта
if(isset($project) && strlen($project) == 5)
{
	$project = getProjectDetails($project);
} else {
	responseWithError("You cannot vote without project code");
}

if(isset($username) && isset($timestamp) && isset($token))
{
	$calcToken = sha1($username . $timestamp . $topcraft_secret);
	if($token == $calcToken)
	{
		$uuid = findProfileByName($username);
		if($uuid == false)
		{
			responseWithError("Profile not found");
		}
		// Поощрение
		voteOnTopAccepted($uuid, "topcraft.ru");
		// Запись лога
		writeAccountLog($uuid, "Пользователь $username проголосовал на topcraft.ru и получил $voteMoneyToAdd монет.");
		die("OK");
	}
}

responseWithError("Parameters are incorrect");
