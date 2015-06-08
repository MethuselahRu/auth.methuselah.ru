<?php
/*
 * IMPLEMENTATION OF: http://session.minecraft.net/game/joinserver.jsp
 */
define('METHUSELAH_INCLUDE_CHECK', true);
include "legacy.php";

/* СУПЕР СТАРЫЙ КОД -- ОЛОЛО
$username = mysql_real_escape_string(filter_input(INPUT_GET, 'user',      FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
$session  = mysql_real_escape_string(filter_input(INPUT_GET, 'sessionId', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
$serverId = mysql_real_escape_string(filter_input(INPUT_GET, 'serverId',  FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

$query = "SELECT `$db_columnUser` FROM `$db_table` WHERE `$db_columnSesId` = '$session' AND $db_columnUser = '$username' AND `$db_columnServer` = '$serverid'";
$result = mysql_query($query)
	or die("Запрос к базе данных завершился ошибкой.");

if(mysql_num_rows($result) != 1)
{
	$query = "UPDATE `$db_table` SET `$db_columnServer` = '$serverid' WHERE `$db_columnSesId` = '$session' AND `$db_columnUser` = '$username'";
	$result = mysql_query($query)
		or die("Запрос к базе данных завершился ошибкой.");
	if(mysql_affected_rows() != 1)
	{
		die("Bad login");
	}
}
exit("OK");
*/
die();
