<?php
if(!defined('METHUSELAH_INCLUDE_CHECK'))
{
	die("Access denied!");
}

function response($result = null, $options = 0)
{
	if(isset($result))
	{
		$send = json_encode($result, $options);
		header('Content-type: application/json');
		header('Content-Length: ' . strlen($send));
		exit($send);
	}
	exit();
}
function responseWithError($error, $errorMessage = "", $cause = "")
{
	$result = array(
		"error" => $error,
		"errorMessage" => $errorMessage,
		"cause" => $cause);
	$send = json_encode($result, 0);
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-type: application/json');
	header('Content-Length: ' . strlen($send));
	exit($send);
}
function prepareForTextOutput()
{
	header('Content-Type: text/plain; charset=utf-8');
}
