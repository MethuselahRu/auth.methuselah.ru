<?php
define('METHUSELAH_INCLUDE_CHECK', true);
require_once "yggdrasil.php";

$result = array(
	"Status"                  => "OK",
	"Runtime-Mode"            => "productionMode",
	"Application-Name"        => "Methuselah Authentication Server",
	"Application-Description" => "Methuselah Authentication Server",
	"Application-Owner"       => "methuselah.ru",
	"Application-Author"      => "methuselah.ru");
	/*
	  // MOJANG TEXT:
	  "Status" => "OK",
	  "Runtime-Mode" => "productionMode",
	  "Application-Description" => "Mojang Authentication Server.",
	  "Specification-Version" => "1.18.4",
	  "Implementation-Version" => "1.18.4_build196",
	  "Application-Name" => "yggdrasil.auth.restlet.server",
	  "Application-Owner" => "Mojang",
	  "Application-Author" => "Mojang Web Force",
	 */

response($result);
