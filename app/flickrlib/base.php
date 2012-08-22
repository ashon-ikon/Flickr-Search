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
require_once('helper_functions.php');

class Base_Class
{
	/**
	 * @var array variable to hold values
	 */
	protected $_vars	= array();
	
	/**
	 * Magic function 
	 * 
	 * value setter
	 */
	public function __set($key, $value)
	{
		// Check if member exists
		$class_vars		= get_class_vars(get_class($this));
		
		$protect_key	= '_' . $key;
		
		if (array_key_exists($protect_key, $class_vars))
			$this->$protect_key = $value;		
		else
		{
			// Store it inside the $this->_vars
			$this->_vars[$key]	= $value;
		}
		
		return $this; // Allow for chaining
	}
	
	/**
	 * value getter
	 */
	public function __get($key)
	{
		// Check if member exists else try retieving from $this->_vars
		$class_vars		= get_class_vars(get_class($this));
		
		$protect_key	= '_' . $key;
		
		if (array_key_exists($protect_key, $class_vars))
			return 	$this->$protect_key;
			
		else if (array_key_exists($key, $this->_vars))
			return $this->_vars[$key];
			
		else
			return;			// We had nothing to return :(
	}
	
	
	protected function _loadOptions($options)
	{
		if (is_array($options))
		{
			foreach ($options as $key => $value)
			{
				if (is_array ($value) && !empty($this->$key))
					$this->$key	= array($this->$key, $value);
				else
					$this->$key = $value;
			}
		}
		return $this;
	}
	
}