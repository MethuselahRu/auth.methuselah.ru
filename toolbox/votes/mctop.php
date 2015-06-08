<?php
define('METHUSELAH_INCLUDE_CHECK', true);
define('METHUSELAH_INCLUDE_VOTES_CHECK', true);
require_once "../toolbox_internal.php";

// Чтение входных данных
$project  = filter_input(INPUT_GET, 'project',  FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
$nickname = filter_input(INPUT_GET, 'nickname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
$token    = filter_input(INPUT_GET, 'token',    FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

// Хак, пока на МЦТопе не поправят запрос по тикету...
if(strstr($project, "?nickname=") != false)
{
	$exploded = explode("?nickname=", $project);
	$project  = $exploded[0];
	$nickname = $exploded[1];
}

// Проверка корректности кода проекта
if(isset($project) && strlen($project) == 5)
{
	$project = getProjectDetails($project);
} else {
	responseWithError("You cannot vote without project code");
}

if(isset($nickname) && isset($token))
{
	$calcToken = md5($nickname . $project['secret_keyword']);
	if($token == $calcToken)
	{
		$uuid = findProfileByName($nickname);
		if($uuid == false)
		{
			responseWithError("Profile not found");
		}
		// Поощрение
		voteOnTopAccepted($uuid, "mctop.su");
		// Запись лога
		writeAccountLog($uuid, "Пользователь $nickname проголосовал на mctop.su и получил $voteMoneyToAdd монет.");
		die("OK");
	}
}

responseWithError("Parameters are incorrect");
