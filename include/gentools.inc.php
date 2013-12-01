<?php

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
	}
}

?>
