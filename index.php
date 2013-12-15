<?php

require_once("config.php");
require_once("include/gentools.inc.php");
require_once("include/ewiki.inc.php");

$docname  = Tools::getArray($_GET, "i", "index");

$xmlFile = $CACHE_LOCATION . "/" . $docname . ".xml";
$wikiFile = $DATA_LOCATION . "/" . $docname . ".wiki";

if (file_exists($wikiFile))
{
	// TODO: Fix this whole mess

	if (file_exists($xmlFile))
	{
		header("location: " . $xmlFile);
	}
	else
	{
		header("location: edit.php?i=" . $docname);
		exit;

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
	header("location: edit.php?i=" . $docname);
}

?>
