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
/**
 * Singleton Request class that can be called within Controllers
 */
class Request_HTTP extends Base_Class
{
	/**
	 * @var array Request vars
	 */
	protected	$_vars			= array();
	
	/**
	 * @var array Request Params
	 */
	protected	$_params		= array();
	
	/**
	 * @var string request method
	 */
	protected	$_method		= 'get';
	
	/**
	 * @var string site offset
	 */
	protected	$_site_offset	= '';
	

	private static $instance	= null;
	
	private function __construct()
	{}
	
	public static function getInstance()
	{
		if (null === self::$instance)
		{
			self::$instance	= new self;
		}
		return self::$instance;
	}
	
	/**
	 * @internal Ideally this function should be called by front controller
	 */
	public function setParams($params_string, $include_global = true)
	{
		if (is_array($params_string) )
			$this->_params	= $params_string;
		else
			$this->_params	= string2params($params_string);
		
		if ($include_global)
			$this->_params	= array_merge( (array) $this->_params, $this->_getGlobalParams());
			
		return $this;
	}
	
	/**
	 * This sets the method of the request
	 */
	public function setMethod($request_method = null)
	{
		if (null === $request_method)
			$request_method 	= 'get';
			
		if (is_string($request_method))
			$this->_method	= strtolower($request_method);
		
		return $this;
	}
	
	/**
	 * This gets the method of the request
	 */
	public function getMethod()
	{
		if ($this->_method ==  null )
			$this->setMethod('get');
		
		return $this->method;
	}
	
	/**
	/**
	 * This function searches for a single paremeter within our params holder
	 */
	public function getParam( $key , $default = null)
	{
		return getArrayVar($this->_params, $key, $default);
	}
	
	/**
	 * This function checks if the request is post
	 */
	public function isPost()
	{
		return $this->getMethod() == 'post' ? true : false;
	}
	
	/**
	 * This function gets all paremeters in all of the 
	 * $_GET / $_POST / $_REQUEST or other superglobals
	 */
	protected function _getGlobalParams( $method = null)
	{
		if (null === $method)
			return $_REQUEST;
			
		else if (strtolower($method)	== 'get' )
			return $_GET;
			
		else if (strtolower($method)	== 'post' )
			return $_POST;
			
		else 
		{
			$supper_global 	= '_' . strtoupper($method);
			
			if (isset($$supper_global))
				return $$supper_global;
		}
		return array();
	}
	
	/**
	 * This function gets all paremeters in our params holder
	 */
	public function getParams( $method = null)
	{
		if (!empty($method) )
		{
			$supper_global	= '_' . strtoupper(trim($method));
			if ( isset($$supper_global))
				return $$supper_global;
		} 
		
			return $this->_params;
	}
	
	/**
	 * This helper function gets the site's base url
	 */
	public function getBaseUrl()
	{
//		pr ($_SERVER);
		
		$baseurl = "http" . ((isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'] ;
		$offset	 = $this->_site_offset;
		
		$baseurl = $baseurl . $offset . '/';
		
		return remove_trailing_slash($baseurl) . '/';		
	}
	
	/**
	 * This retrieves the user's ip address any time
	 */
	public static function getIp()
	{
		if ( isset($_SERVER["REMOTE_ADDR"]) ) 
			$uip=$_SERVER["REMOTE_ADDR"]; 
		else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ) 
			$uip=$_SERVER["HTTP_X_FORWARDED_FOR"]; 
		else if ( isset($_SERVER["HTTP_CLIENT_IP"]) )     
			$uip=$_SERVER["HTTP_CLIENT_IP"]; 
		
		return $uip; 
	}
	
}