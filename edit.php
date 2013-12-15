<?php

// Notes:
// This is so dangerous if the doc doesn't fit into memory...
// I need to find a safer way to parse/inject the contents into the XML.

require_once("include/ewiki.inc.php");
require_once("include/gentools.inc.php");
require_once("config.php");

$wm = new WikiManager();
$body  = Tools::getArray($_POST, "document", NULL); 
$title = Tools::getArray($_POST, "t", "Unnamed");
$docname  = Tools::getArray($_GET, "i", "index");

$wikiFilename = $DATA_LOCATION . "/" . $docname . ".wiki";
$xmlFilename = $CACHE_LOCATION . "/" . $docname . ".xml";

// TODO: Encap this in a class. People shouldn't touch headers
//       directly. I'm just trying to get something workable right now
$headers = array();
$headers["Title"] = $title;
$headers["Author"] = $wm->getAuthor();
$headers["Last-Modified"] = time();
$headers["Content-Location"] = $docname;
$headers["Content-Type"] = "text/wiki"; // Non-IANA MIME here people!

if ($body) 
{
    $wm->writePage($wikiFilename, $body, $headers);
    $wm->renderText($xmlFilename, $body, $headers);
	header( 'Location: /ewiki/?i=' . $docname);
}
else
{
	$doc = $wm->editPage($wikiFilename, $headers); 
	header("Content-type: text/xml; charset=utf-8");
	$doc->save("php://output");
}

?>
