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
			return "";
	}
}

?>
