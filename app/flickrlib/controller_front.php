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
 * This is the very small front controller
 *
 **/
require('controller_abstract.php');
require('controller_request.php');
require('controller_view.php');
require('controller_layout.php');

//Use session for page interlinking

class Controller_Front extends Controller_Abstract
{
	/**
	 * @var string $default Controller
	 */	
	protected $_starting_controller	= 'default';
	
	/**
	 * @var string $error Controller
	 */	
	protected $_error_controller	= 'error';
	
	/**
	 * @var string current controller name
	 */
	protected $_controller_name		= null;
	
	/**
	 * @var string current action name
	 */
	protected $_action_name			= null;
	
	/**
	 * @var Object Layout object
	 */
	protected $_layout_object		= null;
	
	/**
	 * @var String Layout name
	 */
	protected $_layout_name		= null;
	
	/**
	 * @var String Layouts path
	 */
	protected $_layouts_path		= null;
	
	/**
	 * @var boolean setting done
	 */
	protected $_done_setting_up		= false;

	/**
	 * @var object	Active class
	 */
	protected $_active_class		= null;

	/**
	 * Main construct
	 */	
	public function __construct($options = null )
	{
		
		$this->init($options);
		
		parent::__construct(); 	// Just in case there's  something to be done
	}
	
	/**
	 * Set up default controller or die if not found
	 */
	public function init($options	= array())
	{
		
		/* Load the default controller */
		$this->_loadOptions($options);
		
		/* Ensure all MVC folders are readable */
		if (!readable($this->_controllers_path))
			throw new FlickrException('Controllers\' path is not accessible. Please check before I continue.');
		
		if (!readable($this->_models_path))
			throw new FlickrException('models\' path is not accessible. Please check before I continue.');
		
		if (!readable($this->_views_path))
			throw new FlickrException('views\' path is not accessible. Please check before I continue.');
		
		// Setup layout
		$this->_setupLayout();
		
		return $this;
	}
	
	/**
	 * Set default controller name
	 */
	public function setDefaultController($controller_name)
	{
		if (is_string($controller_name))
			$this->_starting_controller	= $controller_name;
		else
			throw new Exception('Invalid Contorller name specified');
		return $this;
	}
	
	/**
	 * Gets the main controller name
	 * 
	 * @return string Default Controller name
	 */
	public function getDefaultController()
	{
		if (null !== $this->_starting_controller)
			return $this->_starting_controller;
	}
	
	/**
	 * Set current controller name
	 */
	public function setCurrentController($controller_name)
	{
		if (is_string($controller_name))
			$this->_controller_name	= $controller_name;
		else
			throw new Exception('Couldn\'t set current Contorller name.');
		return $this;
	}
	
	/**
	 * Gets the current controller name
	 * 
	 * @return string current Controller name
	 */
	public function getCurrentController()
	{
		if (null !== $this->_controller_name)
			return $this->_controller_name;
	}
	
	/**
	 * Set current action name
	 */
	public function setCurrentAction($action_name)
	{
		if (is_string($action_name))
			$this->_action_name	= $action_name;
		else
			throw new Exception('Couldn\'t set current Contorller name.');
		return $this;
	}
	
	/**
	 * Gets the current action name
	 * 
	 * @return string Current Action name
	 */
	public function getCurrentAction()
	{
		if (null !== $this->_action_name)
			return $this->_action_name;
	}
	
	
	/**
	 * This sets up the layout object
	 */
	protected function _setupLayout()
	{
		if ( null === $this->_layout_object)
		{
			if ( null == $this->_layouts_path)
			{
				// Assume layouts path to be:
				// APP_PATH / layouts / 
				$this->_layouts_path	= APP_PATH . 'layouts' . DS;
			}

			if ( null == $this->_layouts_name)
			{
				// Assume layouts name to be:
				// ashon / 
				$this->_layouts_name	= 'ashon';
			}
			
			$new_layout_object			= Controller_Layout::getInstance();
			$new_layout_object->setLayoutPath($this->_layouts_path)
							  ->setLayoutName($this->_layouts_name)
							  ->setup();
							  
			$this->_layout_object	= $new_layout_object;
		}
		
		return $this;
	}
	
	/**
	 * This gets the layout object
	 */
	public function getLayout()
	{
		if ( null === $this->_layout_object)
		{
			$this->_setupLayout();
		}
		
		return $this->_layout_object;
	}
	
	/**
	 * This function sets the active controller class
	 * 
	 * @param Controller_Abstract The currently active class	
	 */
	public function setActiveController( Controller_Abstract $controller )
	{
		$this->_active_class	= $controller;
		
		return $this;
	}
	
	/**
	 * This function gets the active controller class
	 */
	public function getActiveController ()
	{
		if ( null !== $this->_active_class )
		
			return $this->_active_class;
	}
	
		
	/**
	 * @param	string formatted url
	 * 			assumption:
	 * 			controller / action / params
	 * 			controller / action
	 * 			controller /
	 * 			/
	 * @return array array ( controller, action, params )
	 */
	protected function getRoutesFromString( $url )
	{
		//remove leading '/'
		if (strpos($url, '/') == 0)
			$url	= substr($url, 1);
		
		$uri_parts	= explode('/', $url, 3);
		
		/* Clean up array */
		$uri_parts	= array_values(array_filter($uri_parts));
		$controller_name	= $action_name	= $request_params = null;
		
		
		switch (count($uri_parts))
		{
			case 0:
			{
				// index of default/starting page assumed
				$controller_name	= $this->starting_controller;
				$action_name		= 'index';
				
			} break;
			
			case 1:
			{
				// index of supplied controller intended
				$controller_name	= $uri_parts[0];
				$action_name		= 'index';
				
				// Remove '?' strings if present
				if (false !== strpos($controller_name, '?'))
				{
					$controller_name_parts	= explode('?', $controller_name, 2);
					$controller_name		= $controller_name_parts[0];
					$request_params			= $controller_name_parts[1];
					
					if (strlen($controller_name) == 0)
						$controller_name	= $this->starting_controller;
				} 				
				
				
			} break;
			
			case 2:
			{
				// controller / action intended
				$controller_name	= $uri_parts[0];
				$action_name		= $uri_parts[1];
				
				// Remove '?' strings if present
				if (false !== strpos($action_name, '?'))
				{
					$action_name_parts	= explode('?', $action_name, 2);
					$action_name		= $action_name_parts[0];
					$request_params		= $action_name_parts[1];
				} 				
				
			} break;
			
			case 3:
			{
				// controller / action intended and some params supplied
//				var_dump($this->_checkController( $controller_name ))
				$controller_name	= $uri_parts[0];
				if ( Controller_Abstract::CE_NO_ERROR != $this->_checkController( $controller_name ))
				{
					// Controller not found
					$controller_name	= $this->getActiveController();
					if (! $controller_name )
						$controller_name	= $this->getDefaultController();
					
					$action_name			= 'index';		// Assume index
					 
					// Handle the other parts
					// Join uri_parts to other params
						$uri_parts[2]		= implode('/', $uri_parts);
				}
				else
				{
					$controller_name	= $uri_parts[0];	
					// Check if class has $action name
					if ( $this->controllerHasAction($controller_name, $uri_parts[1]))
						$action_name		= $uri_parts[1];
					else
					{
						$action_name		= 'index';
						// Join uri_part[1] to other params
						$uri_parts[2]		= $uri_parts[1] . '/' . $uri_parts[2];
					}	
				}
				
//				pr(array($controller_name, $action_name, $uri_parts[2]));exit;
					
				$request_params		= $uri_parts[2];
				
			} break;
		}
		
		//====================================
		// Validate controller / action
		//
		if (self::CE_NO_ERROR !== $return_code = $this->_checkControllerAction($controller_name, $action_name))
		{
			$controller_class_path	=	$this->_getControllerFullpath($controller_name);
			$options		= array(
				'controller_filename'	=> $controller_class_path,
				'controller'			=> $controller_name,
				'action'				=> $action_name,
			);
			throw new FlickrException($this->error_codes[$return_code], $return_code, $options);
		}
		
		// Clean up params
		$request_params		= string2params($request_params);
		
		$ret_data	= array(
			'controller'	=>	$controller_name,
			'action'		=>	$action_name,
			'params'		=>	$request_params
		);
		return $ret_data;
	}
	
	/**
	 * This function tries to get the route and returns the relevant controller/action
	 * 
	 * Assume a primitive MVC style
	 *
	 * @return array array of controller/action/params 
	 */
	public function getRoute()
	{
		// @var $ret array
		$ret		= array('controller' => '',
							'action'	 => ''
						);
		// Get Request URI
		$site_offset	= dirname(getArrayVar($_SERVER, 'PHP_SELF'));
		$request_uri	= substr(getArrayVar($_SERVER, 'REQUEST_URI'), strlen($site_offset));
		$request_uri	= strtolower($request_uri);
		
		$front_controller_request	= $this->getRequest();
		$front_controller_request->site_offset	= $site_offset;
		/*
		 * Assumptions
		 * 
		 *	/controller/action/param1/val1/param2/val2...
		 *	/controller/action/
		 *	/controller
		 *	/controller/param1/var1/param2/val2
		 *	/
		 * 
		 */
		$routes_data		  = $this->getRoutesFromString( $request_uri );
		$controller		= getArrayVar($routes_data, 'controller');
		$action			= getArrayVar($routes_data, 'action');
		$request_params	= getArrayVar($routes_data, 'params');

		$this->setCurrentController	($controller);
		$this->setCurrentAction		($action);
		
		
		$front_controller_request->setParams($request_params);
		$front_controller_request->controller_name	= $controller;
		$front_controller_request->action_name		= $action;
 
		
		/* Determine which request method it is*/
		$method		= getArrayVar($_SERVER, 'REQUEST_METHOD', 'GET');
		$front_controller_request->setMethod($method);
		
		// We are done witht he setting up APP is ready!!!
		$this->_done_setting_up	= true;
		
	}
	
	/**
	 * This is the main function that dispatches the discovered route
	 */
	public function dispatchRoute()
	{
		/**
		 * since we passed the controller validation test invoke the controller
		 */
		$front_controller_request	= $this->getRequest();
		$this->invokeControllerAction(	$front_controller_request );
		
		$dispatchable_class			= $this->getActiveController();
//		pr($dispatchable_class);
		return $this->finishDispatch( $dispatchable_class );
	}
	
	// This handles re-directed controllers
	public function finishDispatch( $controller_class )
	{
		if ( ! $controller_class instanceof Controller_Abstract )
			throw new FlickrException ( 'Invalid Controller class given!' );
			
		$request			= $this->getRequest();
		$action_name		= $request->action_name;
		
		// Render the view
		$content			=	null;

		if (! $controller_class->noRender() )
			$content 		= $controller_class->view->render($request);
		
		
		// Invoke post Action
		$controller_class->postAction();
		
		// Invoke route shutdown
		$controller_class->routeShutdown();
		
		$layout_content		= array( 'content' => $content ); 
		$return 	= $this->getLayout()->renderLayout($layout_content);
		
		echo $return;
	}
	
	/**
	 * This helps to check if the controller has the action
	 */
	public function controllerHasAction($controller,  $action)
	{
		$controller_class_name	= $controller. 'Controller';
		$action_name			= $action. 'Action';
		
		if ( ! class_exists($controller_class_name ))
			$this->loadController($controller);
		
		return (in_array($action_name , get_class_methods($controller_class_name)));
	}
	
	
}
 