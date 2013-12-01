<?php

require_once("config.php");
require_once("include/gentools.inc.php");
require_once("include/ewiki.inc.php");

$file  = Tools::getArray($_GET, "i", "index");
$title = Tools::getArray($_GET, "t", "index");

$xmlFile = $DATA_LOCATION . "/" . $file . ".xml";
$wikiFile = $DATA_LOCATION . "/" . $file . ".wiki";

if (file_exists($wikiFile))
{
	if (file_exists($xmlFIle))
	{
		header("location: " . $xmlFile);
	}
	else
	{
		// TODO: Forward to a new script to keep this one leaner
		$wm = new WikiManager();	

		// Refresh the cache
		$body = Tools::safeRead($wikiFile);
		$wm->renderText($body, "FIXME: Need to store title with page", $xmlFile);
		
		header("location: " . $xmlFile);
	}
}
else
{
	header("location: edit.php?i=" . $file . "&t=" . $title);
}

?>
