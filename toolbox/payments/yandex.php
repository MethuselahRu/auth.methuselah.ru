<?php
define('METHUSELAH_INCLUDE_CHECK', true);
define('METHUSELAH_TOP_SECRET_CHECK', true);
require_once "../toolbox_internal.php";

// Это должно быть где-то в опциональных настройках проекта
$yandex_secrets = $methuselah["yandex-money-secrets"];

// Допустимые параметры запроса
$parameters = array(
	// HTTP + HTTPS
	'notification_type' => FILTER_SANITIZE_STRING,
	'operation_id'      => FILTER_SANITIZE_STRING,
	'amount'            => FILTER_SANITIZE_STRING,
	'withdraw_amount'   => FILTER_SANITIZE_STRING,
	'currency'          => FILTER_SANITIZE_STRING,
	'datetime'          => FILTER_SANITIZE_STRING,
	'sender'            => FILTER_SANITIZE_STRING,
	'codepro'           => FILTER_SANITIZE_STRING,
	'label'             => FILTER_SANITIZE_STRING,
	'sha1_hash'         => FILTER_SANITIZE_STRING,
	'test_notification' => FILTER_SANITIZE_STRING,
	'unaccepted'        => FILTER_SANITIZE_STRING,
	// HTTPS
	'email'       => FILTER_VALIDATE_EMAIL,
	'lastname'    => FILTER_SANITIZE_STRING,
	'firstname'   => FILTER_SANITIZE_STRING,
	'fathersname' => FILTER_SANITIZE_STRING,
	'phone'       => FILTER_SANITIZE_STRING,
	'city'        => FILTER_SANITIZE_STRING,
	'street'      => FILTER_SANITIZE_STRING,
	'building'    => FILTER_SANITIZE_STRING,
	'suite'       => FILTER_SANITIZE_STRING,
	'flat'        => FILTER_SANITIZE_STRING,
	'zip'         => FILTER_SANITIZE_STRING,
);
// Чтение входных данных
$notification = filter_input_array(INPUT_POST, $parameters, true);
if($notification == false)
{
	responseWithError("Parameters are incorrect (1)");
}

// Проверка по секретному ключу
$secretCorrect = false;
foreach($yandex_secrets as $secret)
{
	$merged = sha1(""
		. $notification['notification_type'] . "&"
		. $notification['operation_id'] . "&"
		. $notification['amount'] . "&"
		. $notification['currency'] . "&"
		. $notification['datetime'] . "&"
		. $notification['sender'] . "&"
		. $notification['codepro'] . "&"
		. $secret . "&"
		. $notification['label']);
	if($merged == $notification['sha1_hash'])
	{
		$secretCorrect = true;
		// break;
	}
}
if(!$secretCorrect)
{
	responseWithError("Parameters are incorrect (2)");
}

// Является ли это тестовым запросом?
$isTest = false;
if(isset($notification['test_notification']) && $notification['test_notification'] == 'true')
{
	$isTest = true;
	// die('TEST-OK');
}

// Кошелёк может быть переполнен и не принимать переводы
if(isset($notification['unaccepted']) && $notification['unaccepted'] == 'true')
{
	die('METHOD TEMPORARY UNAVAILABLE');
}

// Не стоит принимать отрицательные переводы :)
$money = doubleval($notification['amount']);
if($money <= 0.0)
{
	responseWithError("Parameters are incorrect (3)");
}

$log = "Яндекс.Деньги: Принят платёж от " . $notification['sender'] . " в размере $money рублей.";

// В поле label мы храним uuid пополняемой учётной записи
$uuid = null;
if(isset($notification['label']))
{
	$uuid = $notification['label'];
	if(!isProfileExist($uuid))
	{
		echo('UUID IS NOT SET!\n');
	} else {
		if(!$isTest)
		{
			// Приём денежных средств на указанный счёт
			addProfileMoney($uuid, $money, $log);
			die('OK');
		}
	}
}

// Записываем приём в лог
writeAccountLog(null, $log);
die('OK');
