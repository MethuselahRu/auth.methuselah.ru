<?php
if(!defined('METHUSELAH_INCLUDE_CHECK'))
{
	die("Access denied!");
}
function setupEncryption($projectCode, $secretKeyword)
{
	$encryption = array();
	$encryption['projectCode'] = strtoupper($projectCode);
	$encryption['aes256key'] = hash(
		"SHA256",
		$encryption['projectCode'] . $secretKeyword,
		true);
	$encryption['iv'] = str_repeat("=", mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC));
	return $encryption;
}
function third_party_encrypt($plain2crypted, $encryption)
{
	return base64_encode(mcrypt_encrypt(
		MCRYPT_RIJNDAEL_256,
		$encryption['aes256key'], $plain2crypted,
		MCRYPT_MODE_CBC, $encryption['iv']));
}
function third_party_decrypt($crypted2plain, $encryption)
{
	return str_replace("\0", "", mcrypt_decrypt(
		MCRYPT_RIJNDAEL_256,
		$encryption['aes256key'], base64_decode($crypted2plain),
		MCRYPT_MODE_CBC, $encryption['iv']));
}
