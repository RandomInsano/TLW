<?php

// Notes:
// This is so dangerous if the doc doesn't fit into memory...
// I need to find a safer way to parse/inject the contents into the XML.

require_once("wiky.inc.php");

class WikiManager
{
	private $wikiFormatter;

	function __construct()
	{
		$this->wikiFormatter = new wiky();
	}

	public function renderFile($title, $inputFile, $outputFile = NULL)
	{
		if (!file_exists($file))
		{
			die("No file named " . $file);
		}
        
        list($headers, $file) = MessageParser::read($file);
        
        $text  = $file;
        $title = $headers["Title"];
        
		$this->renderText($text, $title, $outputFile);
        
        fclose($file);
	}

	public function getAuthor()
	{
		if ($_SERVER["PHP_AUTH_USER"])
		{
			return $_SERVER["PHP_AUTH_USER"];
		}
		else
		{
			return $_SERVER["REMOTE_ADDR"];
		}
	}

	public function renderText($text, $title, $outputFile = NULL)
	{
		// TODO: Handle date/author right
		$date = date("m.d.y");
		$author = $this->getAuthor();

        // Handle stream or text as input text
        if (get_resource_type($text) === 'stream')
            $text = stream_get_contents($text);
        
		$text = $this->wikiFormatter->parse($text);
		$doc = $this->createXMLPage($title, $text, $author, $date); 

		if ($outputFile != NULL)
			$doc->save($outputFile);
		else
		{
			header("Content-type: text/xml; charset=utf-8");
			$doc->save("php://output");
		}
	}
    
    public function editPage($file, $title)
    {
        // TODO: Allow new files to be created
        list($headers, $content) = MessageParser::read($file);
        
        $title = $headers["Title"];
        
        return $this->createEditPage(stream_get_contents($content), $title);
    }

	private function createXMLPage($title, $body, $author, $date, $themeLocation = null) 
	{
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
		$authNode    = new DOMElement("author", $author);
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

	public function createEditPage($body, $title, $themeLocation = null) {
		$doc = new DOMDocument('1.0', 'UTF-8');

		global $THEME_LOCATION;
		if ($themeLocation == null)
			$themeLocation = $THEME_LOCATION;

		if ($body == "")
			$body = "New page! Oh my gosh!";

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
}

?>
