<?php
/*
 * IMPLEMENTATION OF: http://session.minecraft.net/game/checkserver.jsp
 */
define('METHUSELAH_INCLUDE_CHECK', true);
include "legacy.php";

/* СУПЕР СТАРЫЙ КОД -- ОЛОЛО
$username = mysql_real_escape_string(filter_input(INPUT_GET, 'user',      FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
$serverId = mysql_real_escape_string(filter_input(INPUT_GET, 'serverId',  FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

$query = "SELECT `$db_columnUser` FROM `$db_table` WHERE `$db_columnUser` = '$username' AND `$db_columnServer` = '$serverId'";
$result = mysql_query($query)
	or die("Запрос к базе завершился ошибкой.");

exit((mysql_num_rows($result) == 1) ? "YES" : "NO");
*/
die();
