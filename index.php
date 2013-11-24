<?php

require_once("config.php");

if (array_key_exists("i",$_GET))
{
	$title = $_GET["i"];
}

if ($title == "")
	$title = "index";

$xmlFile = $DATA_LOCATION . "/" . $title . ".xml";

if (file_exists($xmlFile))
{
	header("location: " . $xmlFile);
}
else
{
	header("location: edit.php?i=" . $title);
}

?>
