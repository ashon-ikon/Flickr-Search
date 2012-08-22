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
class FlickrException extends Exception
{
	/**
	 * @var array vars
	 */
	protected	$_vars	= array();
	
	public function __construct($message = null, $code = 0, $options = array())
	{
		// Call parent
		parent::__construct($message, $code);
		
		$this->_vars	= array_merge($this->_vars, $options);	
	}
	
	public function __toString()
	{
		$error_message 	= null;
		
		switch ($this->getCode())
		{
			case Controller_Front::CE_NO_ERROR:
			{
				$error_message = $this->getMessage();
			}break;
			
			case Controller_Front::CE_CONTROLLER_FILE_NOT_FOUND:{
				$controller_file	= getArrayVar($this->_vars, 'controller_filename', 'unknown file');
				$error_message		= '<p><em>\''.$controller_file.'\'</em></p> ' . $this->getMessage();
			}break;
			
			case Controller_Front::CE_CONTROLLER_NOT_FOUND:{
				$controller			= getArrayVar($this->_vars, 'controller', 'unknown controller');
				$error_message		= '<p><em>\''.$controller.'Controller\'</em> ' . $this->getMessage() . '</p>';
			}break;
			
			case Controller_Front::CE_INVALID_CONTROLLER_NAME:{
				$controller			= getArrayVar($this->_vars, 'controller', 'unknown controller');
				$error_message		= '<p><em>\''.$controller.'\'</em> ' . $this->getMessage() . '</p>';
			}break;
			
			case Controller_Front::CE_ACTION_NOT_FOUND:{
				$action				= getArrayVar($this->_vars, 'action', 'unknown action');
				$error_message		= '<p><em>\''.$action.'\'</em> ' . $this->getMessage() . '</p>';
			}break;
			
			case Controller_Front::CE_PATH_NOT_FOUND:{
				$path				= getArrayVar($this->_vars, 'path', 'unknown path');
				$error_message		= '<p><em>\''.$path.'\'</em><br />' . $this->getMessage() . '</p>';
			}break;
			
			case Controller_Front::CE_VIEW_FOLDER_NOT_FOUND:{
				$path				= getArrayVar($this->_vars, 'path', 'unknown path');
				$error_message		= '<p><em>\''.$path.'\'</em><br />' . $this->getMessage() . '</p>';
			}break;
			
			case Controller_Front::CE_VIEW_FILE_NOT_FOUND:{
				$scipt				= getArrayVar($this->_vars, 'script', 'missing script');
				$error_message		= '<p><em>\''.$scipt.'\'</em> ' . $this->getMessage() . '</p>';
			}break;
			
			default:
			{
				$error_message	= $this->getMessage();	
			}break;	
		}
		
		$ret = null;
		$ret .= '<pre>';
		$ret .=	'<strong>Message: </strong><em><big>'. $error_message. '</big></em>' ."\n\n\n";
		$ret .= '<strong>Code:</strong>'. $this->getCode() . "\n\n";
		$ret .= 'Error on line '. $this->getLine();
		$ret .=	'<p>-------------------------------------------------</p>';
//		foreach($this->getTrace() as $error)
		$ret .= $this->getTraceAsString();
		$ret .= '<p><small>Ashon Associates Inc: Flickr App</small></p>';
		$ret .= '</pre>';
		return $ret;
	}
}