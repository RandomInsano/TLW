<?php
/* Wiky.php - A tiny PHP "library" to convert Wiki Markup language to HTML
 * Author: Toni Lähdekorpi <toni@lygon.net>
 *
 * Code usage under any of these licenses:
 * Apache License 2.0, http://www.apache.org/licenses/LICENSE-2.0
 * Mozilla Public License 1.1, http://www.mozilla.org/MPL/1.1/
 * GNU Lesser General Public License 3.0, http://www.gnu.org/licenses/lgpl-3.0.html
 * GNU General Public License 2.0, http://www.gnu.org/licenses/gpl-2.0.html
 * Creative Commons Attribution 3.0 Unported License, http://creativecommons.org/licenses/by/3.0/
 */

class wiky {
	private $patterns, $replacements;

	public function __construct($analyze=false) {
		$this->patterns=array(
			// Headings
			"/^====\s*(.+?)\s*====\s*$/m",					// Subsubheading
			"/^===\s*(.+?)\s*===\s*$/m",					// Subheading
			"/^==\s*(.+?)\s*==\s*$/m",					    // Heading
	
			// Formatting
			"/\'\'\'\'\'(.+?)\'\'\'\'\'/s",					// Bold-italic
			"/\'\'\'(.+?)\'\'\'/s",						// Bold
			"/\'\'(.+?)\'\'/s",						// Italic
	
			// Special
			"/^----+(\s*)$/m",						// Horizontal line
			"/\[\[(file|img):((ht|f)tp(s?):\/\/(.+?))( (.+))*\]\]/i",	// (File|img):(http|https|ftp) aka image
			"/\[((news|(ht|f)tp(s?)|irc):\/\/(.+?))( (.+))\]/i",		// Other urls with text
			"/\[((news|(ht|f)tp(s?)|irc):\/\/(.+?))\]/i",			// Other urls without text
			"/\[(.*?) (.*)\]/i",						// Internal refs
	
			// Indentations
			"/[\n\r]: *.+([\n\r]:+.+)*/",					// Indentation first pass
			"/^:(?!:) *(.+)$/m",						// Indentation second pass
			"/([\n\r]:: *.+)+/",						// Subindentation first pass
			"/^:: *(.+)$/m",						// Subindentation second pass
	
			// Ordered list
			"/[\n\r]?#.+([\n|\r]#.+)+/",					// First pass, finding all blocks
			"/[\n\r]#(?!#) *(.+)(([\n\r]#{2,}.+)+)/",			// List item with sub items of 2 or more
			"/[\n\r]#{2}(?!#) *(.+)(([\n\r]#{3,}.+)+)/",			// List item with sub items of 3 or more
			"/[\n\r]#{3}(?!#) *(.+)(([\n\r]#{4,}.+)+)/",			// List item with sub items of 4 or more
	
			// Unordered list
			"/[\n\r]?\*.+([\n|\r]\*.+)+/",					// First pass, finding all blocks
			"/[\n\r]\*(?!\*) *(.+)(([\n\r]\*{2,}.+)+)/",			// List item with sub items of 2 or more
			"/[\n\r]\*{2}(?!\*) *(.+)(([\n\r]\*{3,}.+)+)/",			// List item with sub items of 3 or more
			"/[\n\r]\*{3}(?!\*) *(.+)(([\n\r]\*{4,}.+)+)/",			// List item with sub items of 4 or more
	
			// List items
			"/^[#\*]+ *(.+)$/m",						// Wraps all list items to <li/>
	
			// Newlines (TODO: make it smarter and so that it groupd paragraphs)
			"/^(?!<li|dd).+(?=(<a|strong|em|img)).+$/mi"			// Ones with breakable elements (TODO: Fix this crap, the li|dd comparison here is just stupid)
		);
		$this->replacements=array(
			// Headings
			"<h4>$1</h4>",
			"<h3>$1</h3>",
			"<h2>$1</h2>",
	
			//Formatting
			"<strong><em>$1</em></strong>",
			"<strong>$1</strong>",
			"<em>$1</em>",
	
			// Special
			"<hr/>",
			"<img src=\"$2\" alt=\"$6\"/>",
			"<a href=\"$1\">$7</a>",
			"<a href=\"$1\">$1</a>",
			"<a href=\"/ewiki/index.php?i=$1\">$2</a>",
	
			// Indentations
			"\n<dl>$0\n</dl>", // Newline is here to make the second pass easier
			"<dd>$1</dd>",
			"\n<dd><dl>$0\n</dl></dd>",
			"<dd>$1</dd>",
	
			// Ordered list
			"\n<ol>$0\n</ol>",
			"\n<li>$1\n<ol>$2\n</ol>\n</li>",
			"\n<li>$1\n<ol>$2\n</ol>\n</li>",
			"\n<li>$1\n<ol>$2\n</ol>\n</li>",
	
			// Unordered list
			"\n<ul>$0\n</ul>",
			"\n<li>$1\n<ul>$2\n</ul>\n</li>",
			"\n<li>$1\n<ul>$2\n</ul>\n</li>",
			"\n<li>$1\n<ul>$2\n</ul>\n</li>",
	
			// List items
			"<li>$1</li>",
	
			// Newlines
			"$0<br />"
		);
		if($analyze) {
			foreach($this->patterns as $k=>$v) {
				$this->patterns[$k].="S";
			}
		}
	}
	
	private function convertParagraphs($input) {
		// Put some padding on the end of the file
		$input .= "\n\n";
	
		$output = preg_replace("/(.*?)(\r?\n){2,}/s", "<p>\n$1\n</p>\n", $input);
	
		$output = preg_replace("/\n(=+[^=]+?=+)\s.*?\n/s", "</p>\n$1\n<p>\n", $output);	

		return $output;
	}
	
	public function parse($input) {
		if(!empty($input)) {
			$output=$this->convertParagraphs($input);
			$output=preg_replace($this->patterns,$this->replacements,$output);
			
			return $output;
		}
		else
			return false;
	}
}
