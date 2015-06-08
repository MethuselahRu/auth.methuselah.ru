<?php
if(!defined('METHUSELAH_INCLUDE_CHECK'))
{
	die("Access denied!");
}

$methuselah = array();

// Отладка
$methuselah["debug"] = false;

// Настройки подключения к основной БД
$methuselah["mysql-hostname"] = "p:localhost";
$methuselah["mysql-database"] = "authserver";
$methuselah["mysql-username"] = "USERNAME";
$methuselah["mysql-password"] = "PASSWORD";

// Код проекта по умолчанию
$methuselah["auth-default-project"] = "A1B2C";

// Интеграция с рейтингами серверов:
if(defined('METHUSELAH_INCLUDE_VOTES_CHECK'))
{
	$methuselah["votes-topcraft-secret"] = "bla-bla-bla-topcraft-secret-enter-here";
	// $methuselah["votes-mctop-secret"] = "/* УЖЕ В БД: `projects`(`secret-keyword`). */";
}

// Интеграция с платёжными системами:
if(defined('METHUSELAH_TOP_SECRET_CHECK'))
{
	// Список допустимых секретов Яндекс-Денег
	$methuselah["yandex-money-secrets"] = array(
		"YaSecret#1",
		"YaSecret#2",
	);
}

// Прямое соединение к БД игрового сервера.
// Его тут быть не должно, просто у меня всё ещё костыли для этого.
$methuselah["game-server-hostname"] = 'ПЫЩЬ';
$methuselah["game-server-database"] = 'ПЫЩЬ';
$methuselah["game-server-username"] = 'ПЫЩЬ';
$methuselah["game-server-password"] = 'ПЫЩЬ';
