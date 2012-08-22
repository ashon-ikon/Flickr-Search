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
 * 
 * @abstract	Controller Abstract class that all controllers inherit from
 */
abstract class Controller_Abstract extends Base_Class
{
	/**
	 * @var array variable to hold all controller values
	 */
	protected $_vars			= array();
	
	/**
	 * @var string Controllers' path
	 */
	protected $_controllers_path	= null;
	
	/**
	 * @var string models' path
	 */
	protected $_models_path			= null;
	
	/**
	 * @var string views' script path
	 */
	protected $_views_path			= null;
	
	
	/**
	 * @var Object view object
	 */
	protected $_view				= null;	
	
	/**
	 * @var Object layout object
	 */
	protected $_layout				= null;
	
	/**
	 * @var array view settings
	 */
	protected $_view_settings		= array();
	
	/**
	 * @var bool can we render views?
	 */
	protected $_can_render			= true;
	
	/**
	 * @var bool dispatched	to guard against loops
	 */
	protected $_dispatched			= false;
	
	/**
	 * @var String view scripts extension
	 */
	protected $_script_extension	= '.phtml';
	
	/**
	 * @var String view scriptname
	 */
	protected $_script_name			= null;
	
	/**
	 * @var object Controller's request
	 */
	protected $_controller_request	= null;
	
	
	const CE_NO_ERROR					=	0x000;			// No need to worry, all is fine
	const CE_CONTROLLER_FILE_NOT_FOUND	=	0xE01;			// Controller file not found
	const CE_CONTROLLER_NOT_FOUND		=	0xE02;			// Controller not found
	const CE_INVALID_CONTROLLER_NAME	=	0xE03;			// Invalid Controller Name
	const CE_ACTION_NOT_FOUND			=	0xE04;			// Controller found but action not found
	const CE_PATH_NOT_FOUND				=	0xE05;			// Path not found
	const CE_VIEW_FOLDER_NOT_FOUND		=	0xE06;			// Controller's view folder not found
	const CE_VIEW_FILE_NOT_FOUND		=	0xE07;			// Path not found
	
	/* Error messages */
	public	$error_codes				= array(
		self::CE_NO_ERROR 					=> 'No error',
		self::CE_CONTROLLER_FILE_NOT_FOUND 	=> 'Controller file could not be found',
		self::CE_CONTROLLER_NOT_FOUND 		=> 'Controller class does not exist',
		self::CE_INVALID_CONTROLLER_NAME	=> 'The Controller name specified is invalid',
		self::CE_ACTION_NOT_FOUND 			=> 'Action not found in controller',
		self::CE_PATH_NOT_FOUND 			=> 'Path supplied not found or readable',
		self::CE_VIEW_FOLDER_NOT_FOUND 		=> 'Cannot find the corresponding view folder for controller. Consider creating one',
		self::CE_VIEW_FILE_NOT_FOUND 		=> 'Cannot find view script in folder. Did you forget to create one?',
	);
	
	/**
	 * Left for child classes to implement
	 */
	public function __construct($options = array()){ 
		
		$controller_name		= get_class($this);

		if ($pos  = strpos($controller_name, 'Controller') !== false && strpos($controller_name, 'Controller') !== 0)
		{
			$controller_name	=	strtolower(substr($controller_name, 0, -10));
			
			// Set up controller view
			$views_path		= getArrayVar($options, 'views_path', APP_PATH . 'views'. DS );
			$this->setViewsPath($views_path);
			
			$request		= $this->getRequest();
			// Register the controller / action
			$this->controller_name	= $request->controller_name;
			$this->action_name		= $request->action_name;
			
		}
		$this->init();
		
		return $this;
		}
	
	//---------------------
	// Controller Hooks
	//-------------------
	
	/**
	 * This is called before routing starts
	 */
	public function routeStartup(){ }

	/**
	 * This is called before action is dispatched
	 */
	public function preAction(){	}
	
	/**
	 * This function is called after action is done with
	 */
	public function postAction() { }
	
	/**
	 * This is called after routing is done
	 */
	public function routeShutdown(){ }	
	
	/**
	 * Called for every Controller
	 */
	abstract function init();
	
	/**
	 * This function attempts to setup view for $this controller
	 * 
	 * Assumptions: APP_PATH / views / $controller / folder exits
	 */
	public function initView( $options = array() )
	{
		
		$request		= $this->getRequest();
		
		$controller		= getArrayVar($options, 'controller', $request->controller_name);
		$action			= getArrayVar($options, 'action', 	 $request->action_name);
		
		$active_controller_view_path	= $this->getViewsPath() . $controller . DS;
		if ( ! readable($active_controller_view_path ) )
		{
			$this->_throwError('', self::CE_VIEW_FOLDER_NOT_FOUND, array('path' => $active_controller_view_path));
		}
		
		$view_script_filename	= $active_controller_view_path . $action . $this->getScriptsExtension();
		
		if ( ! readable($view_script_filename ) )
		{
			$this->_throwError( '', self::CE_VIEW_FILE_NOT_FOUND, array( 'script' => $action  . $this->getScriptsExtension() ));
		}
		// Store the script for rendering purpose ;)
		$this->_script_name		= $view_script_filename;
		
		$this->_view		= $this->_getView();

		return $this;
	}
	
	
	/**
	 * Get Layout object
	 */
	protected function _getLayout()
	{
		if (null === $this->_layout)
		{
			// Get the layout object
			$this->_layout	= Controller_Layout::getInstance();
		}
		
		return $this->_layout;
	}
	
	/**
	 * This proxies to Controller::_setPageTitle()
	 */
	protected function _setPageTitle( $page_title, $escape = true )
	{
		$this->_getLayout()->setPageTitle( $page_title, $escape );
		return $this;
	}
	
	/**
	 * Get View object
	 */
	protected function _getView()
	{
		if (null === $this->_view)
		{
			// Attempt to create a new view object
			$this->_setView();
		}
		
		return $this->_view;
	}
	/**
	 * Set the view object
	 */
	protected function _setView( $options = null )
	{
		$this->_view_settings		= array(
			'script'		=> $this->_script_name,
			'controller'	=> $this->controller_name,
			'action'		=> $this->action_name,
		);
		
		if ( null !== $options )
		{
			$this->_view_settings	= array_merge($this->_view_settings, (array) $options);
		}
		
		// setup the view
		$this->_view		= new Controller_View( $this->_view_settings );
		
		return $this;
	}
	/**
	 * Set Controller Models' path
	 */
	public function setModelsPath( $path )
	{
		if (is_string( $path ))
		{
			if (! readable( $path ) )
				$this->_throwError( '<p>Error finding models path</p>', self::CE_PATH_NOT_FOUND, array('path' => $path ) );
				
			$this->_models_path	= $path;
		}
		
		return $this;
	}
	
	/**
	 * This retrieves the controller models' path
	 */
	public function getModelsPath( )
	{
		if ( null === $this->_models_path )
		{
			// Assume models path :
			// APP_PATH / models /
			$this->_models_path	= APP_PATH . 'models' . DS;
			
			$this->setModelsPath( $this->_models_path ); 
		}
		
		return $this->_models_path;
	}
	
	/**
	 * Set Controller Views' path
	 */
	public function setViewsPath( $path )
	{
		if (is_string( $path ))
		{
			if (! readable( $path ) )
				$this->_throwError( '<p>Error finding views path</p>', self::CE_PATH_NOT_FOUND, array('path' => $path ) );
				
			$this->_views_path	= $path;
		}
		
		return $this;
	}
	
	/**
	 * This retrieves the controller views' path
	 */
	public function getViewsPath( )
	{
		if ( null === $this->_views_path )
		{
			// Assume views path :
			// APP_PATH / views /
			$this->_views_path	= APP_PATH . 'views' . DS;
			
			$this->setViewsPath( $this->_views_path ); 
		}
		
		return $this->_views_path;
	}
	
	/**
	 * Set Views script's extension
	 */
	public function setScriptsExtension( $extension )
	{
		if (is_string( $extension ))
		{
			$this->_script_extension 	= $extension;
		}
		
		return $this;
	}
	
	/**
	 * This retrieves the scripts' extension
	 */
	public function getScriptsExtension( )
	{
		if ( null === $this->_script_extension )
		{
			// Assume scripts extension:  .phtml
			
			$this->_script_extension	= '.phtml';
		}
		
		return $this->_script_extension;
	}
	
	/**
	 * This function gets the request object
	 */
	public function getRequest()
	{
		if ( null === $this->_controller_request )
		{
			$this->_controller_request	= Request_HTTP::getInstance();
		}
		return $this->_controller_request;
	}
	
	/**
	 * Internal re-direction
	 * 
	 * Url stays the same but new action / controller is executed
	 */
	protected function _forward( $location, $params = array() )
	{
		$controller	=	$action	= $route_params	= null;
		if (is_array($location))
		{
			$controller	= getArrayVar($location, 'controller', $this->controller_name);
			$action		= getArrayVar($location, 'action', 'index');
		}
		else if (is_string($location))
		{
			// In format controller/action/params
			if ( strpos ($location, '/') === false )
			{
				// Internal redirection 
				
				$controller	= $this->controller_name;
				$action		= $location;
			}
			else
			{
				$front		= FlickrApp::getInstance()->getControllerFront();
				$route_data	= $front->getRoutesFromString($location);
				
				$controller		= getArray($route_data, 'controller');
				$action			= getArray($route_data, 'action');
				$route_params	= getArray($route_data, 'params');
			}
		}
		// Invoke the controller / action
//		pr(array($controller, $action));
		$this->invokeControllerAction($controller, $action, $route_params);
	} 

	/**
	 * This uses the Header to redirect to new location
	 */
	protected function _redirect( $location, $params = array() )
	{
		
	}
	
	/**
	 * This helper function checks if the controller /action  exist and emits 
	 * relevant errors
	 * @return ENUM one of the error highlighted
	 */
	protected function _checkControllerAction( $controller, $action = 'index' )
	{
		$controller_class_path	= $this->_getControllerFullpath( $controller );
		$controller_class		= $this->_getControllerClassName( $controller ) ;
		$controller_action		= $action . 'Action';
		
		// Check if controller file exists
		if (!readable( $controller_class_path ))
			return self::CE_CONTROLLER_FILE_NOT_FOUND;

		// Check if controller class exists
		if (! class_exists( $controller_class ) )
		{
			// Try loading the class
			$this->loadController( $controller );
		}
		
		
		// Check if action exists
		if (!in_array($controller_action, get_class_methods($controller_class)))
			return self::CE_ACTION_NOT_FOUND;
		
		return self::CE_NO_ERROR;		
	}
	
	/**
	 * This helper function checks if the controller and emits 
	 * relevant errors
	 * @return ENUM one of the error highlighted
	 */
	protected function _checkController( $controller )
	{
		$controller_class_path	= $this->_getControllerFullpath( $controller );
		$controller_class		= $this->_getControllerClassName( $controller ) ;
		
		// Check if controller file exists
		if (!readable( $controller_class_path ))
			return self::CE_CONTROLLER_FILE_NOT_FOUND;

		// Check if controller class exists
		if (! class_exists( $controller_class ) )
		{
			// Try loading the class
			if (! $this->loadController( $controller, false ) )
				return self::CE_CONTROLLER_NOT_FOUND;
		}
		
		return self::CE_NO_ERROR;		
	}
	
	/**
	 * This function attempts to load the controller
	 * 
	 * @throws	Throws exception on error if set
	 * @return true | false
	 */
	public function loadController($controller_name = null, $fail_on_error = true )
	{
		if (null === $controller_name)
		{
			$controller_name	= $this->controller_name;
			
			if (null === $controller_name)
				if ($fail_on_error)
					$this->_throwError('', self::CE_INVALID_CONTROLLER_NAME);
				else
					return false; 
		}
		
		$controller_class_path	= $this->_getControllerFullpath($controller_name);
		
		if (!readable($controller_class_path))
		{
			
			if ($fail_on_error)
				$this->_throwError( '',	self::CE_CONTROLLER_FILE_NOT_FOUND, array('controller_filename' => $controller_class_path)	);
			else
				return false;
		}								
		
		require_once ($controller_class_path);
		
		$controller_class	= $this->_getControllerClassName($controller_name) ;
 
		if (!class_exists($controller_class))
		{
			if ($fail_on_error)
				$this->_throwError( '',	self::CE_CONTROLLER_NOT_FOUND,	array('controller' => $controller_name)	);
			else
				return false;
			
		}	
		
		return $this;		
	}
	
	
	/**
	 * This calls the required controller / action
	 */
	public function invokeControllerAction ($controller, $action = 'index', $params	= array())
	{
		$controller_front	= FlickrApp::getInstance()->getControllerFront();
		
		if ($this->_dispatched)
		{
			return $controller_front->getActiveController();
		}
			
		if ($controller instanceof Request_HTTP )
		{
			$action			= $controller->action_name;
			$params			= $controller->getParams();
			$controller		= $controller->controller_name;
			
		}
		else if ( null == $controller )
		{
			// Assume current controller
			$controller	= Request_HTTP::getInstance()->controller_name;
			
		}
		
		if (self::CE_NO_ERROR !== $return_code = $this->_checkControllerAction($controller, $action))
		{
			// Attempt to autoload it
			$controller_class_path	=	$this->_getControllerFullpath($controller);

			$options		= array(
				'controller_filename'	=> $controller_class_path,
				'controller'			=> $controller,
				'action'				=> $action,
			);
			$this->_throwError('', $return_code, $options);
		}
		
		// Update request object
		$request	= 	Request_HTTP::getInstance();
		if ( $request->controller_name	!= $controller ) $request->controller_name = $controller;
		if ( $request->action_name	!= $action ) $request->action_name = $action;
		
		// No errors so dispatch Controller
		$controller_class	= $this->_getControllerClassName($controller) ;

		$active_controller_class		= new $controller_class;
		
		/* Invoke the route and action callbacks */
		
		// Invoke route starup
		$active_controller_class->routeStartup();
		
		// Invoke pre Action
		$active_controller_class->preAction();
		
		
		// Invoke requested action
		$opts	= array('controller' => $controller, 'action' => $action);

		$active_controller_class->initView($opts);
		
		// Action dispatched
		$this->_dispatched		= true;
		$controller_front->setActiveController($active_controller_class);
		
		$action_method			= $action . 'Action';
		$active_controller_class->$action_method();
		
		
		
		return $active_controller_class;
	}
	
	/**
	 * This function helps to construct the controller path based on it's name
	 * 
	 * It assumes that all controllers are saved as lower-case 'controller-name.php'
	 */
	protected function _getControllerFullpath($controller_name)
	{
		if (null == $this->controllers_path )
		{
			$front		= FlickrApp::getInstance()->getControllerFront();
			$this->controllers_path	= $front->controllers_path;
		}
		return $this->controllers_path . $controller_name . '.php';	
	}
	
	// For throwing errors
	protected function _throwError($message = '', $code = 0, $options = array())
	{
		$in_options		= array(
				'controller_filename'	=> $this->_getControllerFullpath($this->controller_name),
				'controller'			=> $this->controller_name,
				'action'				=> $this->action_name,
			);
		$options	= array_merge($in_options, $options);
		
		if ('' == $message)
			$message	= $this->error_codes[$code];
			
		throw new FlickrException($message , $code, $options);
	}		
	
	/**
	 * Helper function to get controller class name
	 */
	protected function _getControllerClassName($controller_name)
	{
		if (is_string($controller_name))
			return ucfirst($controller_name) . 'Controller';
	}
	
	/**
	 * This is to determine when to render
	 */
	public function noRender()
	{
		return ! $this->_can_render;
	}

}