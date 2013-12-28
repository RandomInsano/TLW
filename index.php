<?php

require_once("config.php");
require_once("include/gentools.inc.php");
require_once("include/ewiki.inc.php");

$docname  = Tools::getArray($_GET, "i", "index");

$wm = new WikiManager();
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
		$wm->renderFile($wikiFile, $xmlFile);
		header("location: " . $xmlFile);
	}
}
else
{
	header("location: edit.php?i=" . $docname);
}

?>
