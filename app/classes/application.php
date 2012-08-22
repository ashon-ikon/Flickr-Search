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
 * 
 * This is the main Flickr Gallery Application
 * 
 * It handles essential things such as routes etc
 */

class FlickrApp extends Base_Class
{
	
	/**
	 * @var Object Controller _front 
	 */
	protected	$_controller_front	= null;
	
	/**
	 * @var string request method
	 */
	protected	$_method	= 'get';
	

	private static $instance	= null;
	/**
	 * Signleton main constructor to FlickrApp
	 * 
	 */
	public function __construct()	
	{
		$this->_init();
	}
	
	public static function getInstance()
	{
		if (null === self::$instance)
		{
			self::$instance	= new self;
		}
		return self::$instance;
	}
	
	/**
	 * This is the main setup function for this application
	 * @throws	Exception if front controller class is missing
	 */
	protected function _init()
	{
		/* Check that the main front controller exists*/	
		if (! class_exists('Controller_Front'))
			throw new Exception( 'Failed to find front_controller' );
		
		/**
		 * @var array controller options 
		 */
		$controller_options		= array(
						'controllers_path' 		=> APP_PATH . 'controllers' . DS,
						'models_path' 			=> APP_PATH . 'models' . DS,
						'views_path' 			=> APP_PATH . 'views' . DS,
						'starting_controller' 	=> 'home',
						);
		
		$this->_controller_front	= $this->getControllerFront($controller_options); 
	}
	
	/**
	 * This tries to retrieve the front controller
	 * 
	 */
	public function getControllerFront($options = null)
	{
		if (null === $this->_controller_front)
		{
			$this->_controller_front	= new Controller_Front($options);
		}
		return $this->_controller_front;
	}
	
	/**
	 * This function shall be responsible for directing all user requests
	 */
	public function run()
	{
		$this->getControllerFront()->getRoute();			// Establish the route
		
		$this->getControllerFront()->dispatchRoute();		// Dispatch  route
		
	}
	
	
}