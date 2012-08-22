<?php
/*---------------------------------------------------------------------
 * @project:	My Flicker Gallery
 * 
 * @Project		My Flicker Gallery Application
 * 
 * --------------------------------------------------------------------
 * Created by ashon on Aug 17, 2012
 * 
 * (c) 2010 - 2012 Copyright Ashon Associates Inc. Web Solutions 
 * 
 * This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * --------------------------------------------------------------------
 */

//Debugging functions
if (!function_exists('pr'))
{
	function pr($array)
	{
		echo '<pre>';
		echo print_r($array, true);
		echo '</pre>';
	}
}

if (!function_exists('vp'))
{
	function vp($variable)
	{
		echo '<pre>';
		echo $variable;
		echo '</pre>';
	}
}

/**
 *  Useful for creating HTML elements programmatically
 * 
 * @return string HTML tag 
 */
function wrapHtml($data = '', $tag, $attrs = null, $single_tag = false)
{
	$ret =  '<'.$tag;
	if (is_array($attrs) && !empty($attrs))
	{
		$ret .= ' ';
		foreach($attrs as $attr => $val)
		$ret .= $attr .'="'.$val .'" ';
	}
	
	if ($single_tag)
		$ret .= ' />';
	else
		$ret .= '>'. $data. '</'.$tag.'>';
	return $ret;
}

/**
 * Retrieve values from arrays in a safe manner and a default value 
 */
function getArrayVar($array, $key, $default = null)
{
	$array	= (array) $array;
	
//	if (!is_array($array))
//		return;
	if ( array_key_exists($key, $array) && $array[$key] != null)
		return $array[$key];
	else
		return $default;
}


// Helper function to check if file exists and we have reading permissions
function readable($file)
{
	return (file_exists($file) && is_readable($file));
}

// Helper function to check if file exists and we have WRITING permissions
function writable($file)
{
	return (file_exists($file) && is_writable($file));
}

// Helper function to convert string to params array
function string2params($params_string)
{

	if (is_string($params_string))
	{
		$params		= array();
		if (strpos($params_string , '/') !== false )
		{
			$raw_params = explode('/', $params_string);

			if (($c = count($raw_params)) % 2 == 0)
			{
				$i	= 0;
				foreach ($raw_params as $param )	// Step in twos
				{
					if ($i % 2 == 0)
						$params [$raw_params[$i]] = $raw_params[$i + 1];		// Do the magic
					$i++;
				}
			}
		}

		if (strpos($params_string, '=') !== false )
		{
			$params_q	= explode('&' , $params_string);
			
			foreach ($params_q as $this_q)
			{
				if (strpos($this_q, '='))
				{
					list($key, $value)	= explode('=', $this_q, 2);
					$params[$key]	= $value;
				}		
			}
//			// Join the two
//			$params		= array_merge($params, $params_q);
		}
		return $params;
	}
}

// Helper function to convert params to string
function params2string($params)
{
	if (is_array( $params ) && ! empty( $params ))
	{
		$ret	= null;
		foreach ($params as $key => $value)
			$ret .= '/' . $key . '/' . $value;
			
		return $ret;
	}
}


function cleanSlashes(&$text)
{
	if (is_string($text))
	{
		if (get_magic_quotes_gpc())
		{ 
			$text = stripcslashes($text);
			$text = stripslashes($text);
		}
	}
	
	return $text;
}

/**
 *  sanitization
 */ 
function sanitizeVariables(&$item, $cleanText = true) 
{ 
	if (!is_array($item)) 
	{ 
		// undoing 'magic_quotes_gpc = On' directive
		if (get_magic_quotes_gpc()) 
			$item = stripcslashes($item); 
		if ($cleanText)
			$item = sanitizeText($item); 
	} 
} 

// does the actual 'html' and 'sql' sanitization. customize if you want. 
function sanitizeText($text, $trim = false) 
{ 
	$text = str_replace("<", "&lt;", $text); 
	$text = str_replace(">", "&gt;", $text); 
	$text = str_replace("\"", "&quot;", $text); 
	$text = str_replace("'", "&#039;", $text); 
	
	// it is recommended to replace 'addslashes' with 'mysql_real_escape_string' or whatever db specific fucntion used for escaping. 
	// However 'mysql_real_escape_string' is slower because it has to connect to mysql.
	
	$text = addslashes($text); 
	
	if ($trim)
		$text = trim($text, '\'');
	return $text; 
}


// Helper function to remove trailing slash
function remove_trailing_slash($path)
{
	if (substr($path, -1) == DS )
			$path = substr($path, 0, strlen($path) - 1);
	return $path;
}

defined ( 'HELPERS_LOADED' )
	|| define ('HELPERS_LOADED', 0xF3 );