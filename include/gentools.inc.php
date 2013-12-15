<?php

$MAX_FILE_SIZE = 262144; // 256 * 1024

class Tools
{

	static function getArray($array, $key, $default)
	{
		if (!array_key_exists($key, $array))
			return $default;
		
		$value = $array[$key];

		if ($value === NULL)
			return $default;

		if ($value == "")
			return $default;

		return $value;
	}

	# Make sure the file we'll read is safe.
	private function testFilename($filename) {
		if (preg_match('/../', $filename)) return false;        // Has possible directory issues
		if (preg_match('/^\//', $filename)) return false;       // Starts with a slash
		if (preg_match('/wiki$/', $filename)) return true;      // Must end in 'wiki'

		return false;
	}

	static function safeRead($filename) {
		global $MAX_FILE_SIZE;

		if (Tools::testFileName($filename))
			return "Filename is unsafe. I refuse to read it!";
		elseif (file_exists($filename))
			if (filesize($filename) > $MAX_FILE_SIZE)
				return "Filesize is too big. I refuse to read it!";
			else
				return file_get_contents($filename);
		else
			return NULL; 
	}
}

# Really lax version of http://tools.ietf.org/html/rfc561
# TODO: This is really messy. Clean up implementation, error handling
class MessageParser
{
	private static function assignHeader($line, &$array)
	{
		list($key, $val) = preg_split("/:\s*/", $line);
		
		if ($val)
		{
			print "Assigning " . $key . " with [" . $val . "]\n";
			$array[$key] = $val;
		}
	}
	
	// Read each header line and pass it to an optional
	// processing function. Lets us either skip over the
	// headers or actually do sometihn with them
	private static function processHeaders($file, &$headers)
	{
		while (($line = fgets($file, 1024)) !== false)
		{
			// We're done if we find an empty line
			if (preg_match("/^\s*$/",$line))
			{
				break;
			}
			
			// Remove trailing newline
			$line = trim($line);
			
            if ($headers)
            {
                MessageParser::assignHeader($line, $headers);
            }
		}
	}
	
	static function read($filename)
	{
		// The fact that I can pass this out is weird...
		$headers = array();
	
		$file = fopen($filename, 'r');
		MessageParser::processHeaders($file, $headers);
		
		return array($headers, $file);
	}
	
	static function write($filename, &$headers, $content)
	{
		$file = fopen($filename, "w");
	
		foreach ($headers as $key => $value)
		{
            // I'm just that crazy about things lining up
			fprintf($file, "%-20s%s\n", $key . ":", $value);
		}
        fprintf($file, "\n");
    
        // If we got a stream, copy it into the output
        if (get_resource_type($content) === 'stream')
        {
            while (($line = fgets($content, 1024)) !== false)
            {
                fprintf($file, "%s", $line);
            }
        }
        else
        {
            fprintf($file, $content);
        }
            
		fclose($file);
	}	
}

?>
