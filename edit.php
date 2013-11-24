<?php

// Notes:
// This is so dangerous if the doc doesn't fit into memory...
// I need to find a safer way to parse/inject the contents into the XML.

require_once("ewiki.php");
require_once("config.php");

$wm = new WikiManager();


$body = $_POST["document"];
if (array_key_exists("i", $_GET))
{
	$title = $_GET["i"];
	if ($title == "")
		$title = "index";
}
$file = $DATA_LOCATION . "/" . $title;

if ($body) {
	file_put_contents($file . ".wiki", $body);
	$wm->renderText($body, $title, $file . ".xml");
	header( 'Location: /ewiki/' . $file . ".xml");
} else {
	$body = safeRead($file . ".wiki");
}

$doc = editPage($body); 

header("Content-type: text/xml; charset=utf-8");
$doc->save("php://output");

# Make sure the file we'll read is safe.
function testFilename($filename) {
	if (preg_match('/../', $filename)) return false;	// Has possible directory issues 
	if (preg_match('/^\//', $filename)) return false;	// Starts with a slash
	if (preg_match('/wiki$/', $filename)) return true;	// Must end in 'wiki'

	return false;
}

function safeRead($filename) {
	if (testFileName($filename))
		return "Filename is unsafe. I refuse to read it!";
	elseif (file_exists($filename))
		return file_get_contents($filename);
	else
		return "";
}

function editPage($body, $themeLocation = null) {
	$doc = new DOMDocument('1.0', 'UTF-8');

	global $THEME_LOCATION;
	if ($themeLocation == null)
		$themeLocation = $THEME_LOCATION;

	if ($body == "")
		$body = "New page! Oh my gosh!";

	$title = "Editing stuffs";

	// Create nodes
	$xlsNode     = new DOMProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="' . $themeLocation . '/main.xsl"');
	$docNode     = new DOMElement("document");
	$titleNode   = new DOMElement("title", $title); 
	$editNode    = new DOMElement("edit");
	$metaNode    = new DOMElement("meta");
	$styleNode   = new DOMElement("style");
	$fileNode    = new DOMElement("file", $themeLocation . "/main.css");
	$contentNode = $doc->createCDATASection($body);
	
	// Wire them up
	$doc->appendChild($xlsNode);
	$doc->appendChild($docNode);
	$docNode->appendChild($titleNode);
	$docNode->appendChild($editNode);
	$editNode->appendChild($contentNode);
	$docNode->appendChild($metaNode);
	$metaNode->appendChild($styleNode);
	$styleNode->appendChild($fileNode);

	return $doc;
}

?>
