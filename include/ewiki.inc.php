<?php

// Notes:
// This is so dangerous if the doc doesn't fit into memory...
// I need to find a safer way to parse/inject the contents into the XML.

require_once("wiky.inc.php");
require_once("gentools.inc.php");

class WikiManager
{
	private $wikiFormatter;

	function __construct()
	{
		$this->wikiFormatter = new wiky();
	}

    /* FIXME
	public function renderFile($title, $inputFile)
	{
		if (!file_exists($file))
		{
			die("No file named " . $file);
		}
        
        list($headers, $file) = MessageParser::read($file);
        
        $text  = $file;
        $title = $headers["Title"];
        
		$this->renderText($text, $headers, $outputFile);
        
        fclose($file);
	}
    */

	public function getAuthor()
	{
		if (array_key_exists("PHP_AUTH_USER", $_SERVER))
		{
			return $_SERVER["PHP_AUTH_USER"];
		}
		else
		{
			return $_SERVER["REMOTE_ADDR"];
		}
	}

	public function renderText($outputFile, $text, $headers)
	{
		if (!Tools::testFilename($outputFile))
    		die("Bad filename specified (" . $outputFile . ")");
				
        // Handle stream or text as input text
        if (!is_string($text))
        	if (get_resource_type($text) === 'stream')
            	$text = stream_get_contents($text);
        
		$text = $this->wikiFormatter->parse($text);
		$doc = $this->createXMLPage($text, $headers); 

		Tools::makeFile($outputFile);
        $doc->save($outputFile);
	}
    
    public function writePage($filename, $body, $headers)
    {
    	if (!Tools::testFilename($filename))
    		die("Bad filename specified (" . $filename . ")");
    
    	Tools::makeFile($filename);
    	
        MessageParser::write($filename, $headers, $body);
    }
    
    public function editPage($file, $headers)
    {
    	if (!Tools::testFilename($file))
    		die("Bad filename specified (" . $file . ")");
    		    	
        if (file_exists($file))
        {
            list($headers, $content) = MessageParser::read($file);
            
            // TODO: I'd like this to be handled line by line far down the
            //       stack
            $content = stream_get_contents($content);
        }
        
        return $this->createEditPage($content, $headers);
    }

	private function createXMLPage($content, $metadata, $themeLocation = null) 
	{
		$doc = new DOMDocument('1.0', 'UTF-8');

		if ($content == "")
			$content = "No body text given to createPage on line " . __LINE__;

		// Turn body HTML into a real document
		$contentNode = DOMDocument::loadXML("<body>" . $content . "</body>");
		$contentNode = $contentNode->getElementsByTagName("body")->item(0);
		$contentNode = $doc->importNode($contentNode, true);

		return $this->createPage($doc, $contentNode, $metadata, $themeLocation);
	}
	
	public function createEditPage($content, $metadata, $themeLocation = null)
	{
		$doc = new DOMDocument('1.0', 'UTF-8');
	
		if ($content == "")
			$content = "New page! Oh my gosh!";
	
		$contentNode = $doc->createCDATASection($content);
		$content     = $doc->createElement("edit");
		$content->appendChild($contentNode);
	
		return $this->createPage($doc, $content, $metadata, $themeLocation);
	}
	
	/* Code common to rendering and editing the wiki pages. It's very specific
	 * to the two pages right now, but may be made somewhat simpler later. 
	 */
	private function createPage($doc, $content, $metadata, $themeLocation = null)
	{
		global $THEME_LOCATION;
		if ($themeLocation == null)
			$themeLocation = $THEME_LOCATION;
	
		// Relevant header goodness
		$title   = $metadata["Title"];
		$date    = $metadata["Last-Modified"];
		$author  = $metadata["Author"];
		$docname = $metadata["Content-Location"];
	
		// Create nodes
		$xlsNode     = new DOMProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="' . $themeLocation . '/main.xsl"');
		$docNode     = new DOMElement("document");
		$titleNode   = new DOMElement("title", $title);
		$metaNode    = new DOMElement("meta");
		$dateNode    = new DOMElement("date", $date);
		$authNode    = new DOMElement("author", $author);
		$nameNode    = new DOMElement("name", $docname);		
		$styleNode   = new DOMElement("style");
		$fileNode    = new DOMElement("file", $themeLocation . "/main.css");
	
		// Wire them up
		$doc->appendChild($xlsNode);
		$doc->appendChild($docNode);
		$docNode->appendChild($titleNode);
		$docNode->appendChild($content);
		$docNode->appendChild($metaNode);
		$metaNode->appendChild($nameNode);
		$metaNode->appendChild($dateNode);
		$metaNode->appendChild($authNode);
		$metaNode->appendChild($styleNode);
		$styleNode->appendChild($fileNode);
	
		return $doc;
	}	
}

?>
