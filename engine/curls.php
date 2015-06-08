<?php
if(!defined('METHUSELAH_INCLUDE_CHECK'))
{
	die("Access denied!");
}

function curlPostRequest($address, $payload)
{
	$ch = curl_init();
	$jsonPayload = json_encode($payload);
	curl_setopt($ch, CURLOPT_URL, $address);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	// curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json; charset=utf-8',
		'Content-Length: ' . strlen($jsonPayload))
	);
	$jsonResponce = curl_exec($ch);
	$curlErrNo = curl_errno($ch);
	if($curlErrNo == 0)
	{
		curl_close($ch);
		if(strlen($jsonResponce) > 0)
		{
			$object = json_decode($jsonResponce, true);
			return is_array($object) ? $object : $jsonResponce;
		}
		return array("OK" => "OK");
	}
	return array("error" => "CURL Error #$curlErrNo");
}
