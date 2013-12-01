<?php

// Notes:
// This is so dangerous if the doc doesn't fit into memory...
// I need to find a safer way to parse/inject the contents into the XML.

require_once("include/ewiki.inc.php");
require_once("include/gentools.inc.php");
require_once("config.php");

$wm = new WikiManager();
$body  = Tools::getArray($_POST, "document", NULL); 
$title = Tools::getArray($_POST, "i", "index");

$file = $DATA_LOCATION . "/" . $title;

if ($body) {
	file_put_contents($file . ".wiki", $body);
	$wm->renderText($body, $title, $file . ".xml");
	header( 'Location: /ewiki/' . $file . ".xml");
} else {
	$body = Tools::safeRead($file . ".wiki");
	$doc = $wm->editPage($body); 

	header("Content-type: text/xml; charset=utf-8");
	$doc->save("php://output");
}

?>
