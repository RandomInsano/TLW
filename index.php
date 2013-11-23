<?php

// Notes:
// This is so dangerous if the doc doesn't fit into memory...
// I need to find a safer way to parse/inject the contents into the XML.

require_once("wiky.inc.php");

$THEME = "default";
$THEME_LOCATION = "/ewiki/themes/" . $THEME;
$IN_FILE = "input.wiki";
$OUT_FILE = "data/input.xml";

// If cached copy is out of date, freshen it. Otherwise, direct to semi-static page
if (!file_exists($OUT_FILE) ||  filemtime($IN_FILE) > filemtime($OUT_FILE)) {
	$wikiFormatter = new wiky();

	$wikiDoc = parseWikiDoc($IN_FILE);
	$doc = createPage("TLW", $wikiDoc, "2011-01-01"); 
	$doc->save($OUT_FILE);

	header("Content-type: text/xml; charset=utf-8");
	$doc->save("php://output");
}
else
{
	header('Location: ' . $OUT_FILE, true, 307); // Faster than streaming the file via PHP
}

function parseWikiDoc($file)
{
	global $wikiFormatter;

	$wikiText = file_get_contents($file);
	$wikiText = htmlspecialchars($wikiText);
	return $wikiFormatter->parse($wikiText);
}

function createPage($title, $body, $date, $themeLocation = null) {
	$doc = new DOMDocument('1.0', 'UTF-8');

	global $THEME_LOCATION;
	if ($themeLocation == null)
		$themeLocation = $THEME_LOCATION;

	if ($body == "")
		$body = "No body text given to createPage on line " . __LINE__;

	// Turn body HTML into a real document
	$bodyContentNode = DOMDocument::loadXML("<body>" . $body . "</body>");
	$bodyContentNode = $bodyContentNode->getElementsByTagName("body")->item(0);
	$bodyContentNode = $doc->importNode($bodyContentNode, true);

	// Create nodes
	$xlsNode     = new DOMProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="' . $themeLocation . '/main.xsl"');
	$docNode     = new DOMElement("document");
	$titleNode   = new DOMElement("title", $title); 
	$bodyNode    = new DOMElement("body");
	$metaNode    = new DOMElement("meta");
	$dateNode    = new DOMElement("date", $date);
	$authNode    = new DOMElement("author", "Edwin Amsler");
	$styleNode   = new DOMElement("style");
	$fileNode    = new DOMElement("file", $themeLocation . "/main.css");
	
	// Wire them up
	$doc->appendChild($xlsNode);
	$doc->appendChild($docNode);
	$docNode->appendChild($titleNode);
	$docNode->appendChild($bodyNode);
	$bodyNode->appendChild($bodyContentNode);
	$docNode->appendChild($metaNode);
	$metaNode->appendChild($dateNode);
	$metaNode->appendChild($authNode);
	$metaNode->appendChild($styleNode);
	$styleNode->appendChild($fileNode);

	return $doc;
}


?>
