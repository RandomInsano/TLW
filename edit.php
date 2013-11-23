<?php

// Notes:
// This is so dangerous if the doc doesn't fit into memory...
// I need to find a safer way to parse/inject the contents into the XML.

require_once("wiky.inc.php");

$THEME = "default";
$THEME_LOCATION = "/ewiki/themes/" . $THEME;
$IN_FILE = "input.wiki";

$doc = editPage($IN_FILE); 

header("Content-type: text/xml; charset=utf-8");
$doc->save("php://output");

# Make sure the file we'll read is safe.
function testFilename($filename) {
	if (strpos($file, '.') === TRUE) return false;
	if (preg_match('/wiki$/', $string)) return true;

	return false;
}

function editPage($file, $themeLocation = null) {
	$doc = new DOMDocument('1.0', 'UTF-8');

	global $THEME_LOCATION;
	if ($themeLocation == null)
		$themeLocation = $THEME_LOCATION;

	if (file_exists($file))
		# Be safe. Don't read outside our folder strucure
		if (testFileName($file))
			$body = "Filename is unsafe. I refusing to read it!";
		else
			$body = file_get_contents($file);
	else
		$body = "";

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
