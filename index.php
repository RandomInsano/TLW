<?php

require_once("config.php");
require_once("include/gentools.inc.php");

$file  = Tools::getArray($_GET, "i", "index");
$title = Tools::getArray($_GET, "t", "index");

$xmlFile = $DATA_LOCATION . "/" . $file . ".xml";

if (file_exists($xmlFile))
{
	header("location: " . $xmlFile);
}
else
{
	header("location: edit.php?i=" . $file . "&t=" . $title);
}

?>
