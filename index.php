<?php

$THEME = "default";
$THEME_LOCATION = "themes/" . $THEME;

$doc = createPage("TLW", "Hello world", $THEME_LOCATION);

header("Content-type: text/xml; charset=utf-8");
$doc->save("php://output");

function createPage($title, $body, $themeLocation) {
	$doc = new DOMDocument('1.0', 'UTF-8');

	// Create nodes
	$xlsNode     = new DOMProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="' . $themeLocation . '/main.xsl"');
	$docNode     = new DOMElement("document");
	$titleNode   = new DOMElement("title", $title); 
	$bodyNode    = new DOMElement("body", $body);
	$metaNode    = new DOMElement("meta");
	$dateNode    = new DOMElement("date", "2013-11-16");
	$authNode    = new DOMElement("author", "Edwin Amsler");
	$styleNode   = new DOMElement("style");
	$fileNode    = new DOMElement("file", $themeLocation . "/main.css");
	
	// Wire them up
	$doc->appendChild($xlsNode);
	$doc->appendChild($docNode);
	$docNode->appendChild($titleNode);
	$docNode->appendChild($bodyNode);
	$docNode->appendChild($metaNode);
	$metaNode->appendChild($dateNode);
	$metaNode->appendChild($authNode);
	$metaNode->appendChild($styleNode);
	$styleNode->appendChild($fileNode);

	return $doc;
}


?>
