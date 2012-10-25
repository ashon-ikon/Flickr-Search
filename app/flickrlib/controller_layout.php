<?php
/*---------------------------------------------------------------------
 * @project:	My Flicker Gallery
 * 
 * @Project		My Flicker Gallery Application
 * 
 * --------------------------------------------------------------------
 * Created by ashon on Aug 18, 2012
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
class Controller_Layout extends Controller_View_Base
{
	/**
	 * @var string Layout Script
	 */
	protected	$_script		= null;

	/**
	 * @var string Layout path
	 */
	protected	$_layout_path	= '';

	/**
	 * @var string Layout name
	 */
	protected	$_layout_name	= '';
	
	/**
	 * @var string Page Title
	 */
	protected	$_page_title	= '';
	
	/**
	 * @var array CSS scripts
	 */
	protected	$_css_scripts	= array();
	
	/**
	 * @var array Javascript scripts
	 */
	protected	$_scripts	= array();
	
	/**
	 * @var String view scripts extension
	 */
	protected $_script_extension	= '.phtml';
	
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
	 * @internal This should be called after all the default settings are set
	 * 
	 * This method sets up the layout
	 */	
	public function setup()
	{
		// Ensure script is readable
		if ( ! readable( $script = $this->_getLayoutFullname() ) )
		{
			throw new FlickrException( 'Cannot find layout script!', Controller_Abstract::CE_PATH_NOT_FOUND, array('path' => $script) );
		}
		
		$this->_script	= $script;
		
		// Setup page title just in case
		$this->setPageTitle();
		
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
	
	public function renderLayout( $layout_content =  null )
	{
		if ( is_array($layout_content))
		{
			foreach ($layout_content as $key => $value)
			{
				$this->$key = $value;
			}
		}
		else if ( is_string ( $layout_content ))
		{
			// Not good practice but :(
			$this->$$layout_content	= $layout_content;
		}
		
		return $this->render();
	}
	
	/**
	 * This sets the title
	 */
	public function setPageTitle( $title = null , $escape = true)
	{
		if ( null === $title )
		{
			$title	= APP_NAME;
			$this->_page_title	= $title;
		}
		else 
		{
			if ( $escape )
			{
				$title	= cleanSlashes($title);
				$title	= sanitizeText($title);
			}
			
			$this->_page_title	= $title ;	
		}
		
		return $this;
	}
	
	/**
	 * This gets the page title
	 */
	public function getPageTitle( )
	{
		if ( null === $this->_page_title )
		{
			$this->_setPageTitle();
		}
		
		return $this->_page_title;		
	}
	
	/**
	 * This sets the layout script's path
	 */
	public function setLayoutPath( $layout_path )
	{
		if ( is_string( $layout_path ) )
		{
			if (! readable( $layout_path))
				throw new FlickrException( 'Invalid layout script!', Controller_Abstract::CE_PATH_NOT_FOUND, array('path' => $layout_path) );
				
			$this->_layout_path	= $layout_path;
		}
		
		return $this;
	}

	/**
	 * This gets the layout path
	 * 
	 * @return string 	Path to current layout path
	 */
	public function getLayoutPath( )
	{
		if ( null != $this->_script  )
			return $this->_script;	
	}
	
	/**
	 * This sets the current / active layout
	 */
	public function setLayoutName( $layout_name )
	{
		if ( is_string( $layout_name ) )
			$this->_layout_name	= $layout_name;
			
		return $this;	
	}

	/**
	 * This gets the current / active layout
	 * 
	 * @return string Name of current layout
	 */
	public function getLayoutName( )
	{
		if ( null != $this->_layout_name  )
			return $this->_layout_name;	
	}
	
	/**
	 * This function helps to construct the layout script path based on it's name
	 */
	protected function _getLayoutFullname($layout_name = null)
	{
		if ( null === $layout_name )
			$layout_name	= $this->_layout_name;
			
		return $this->layout_path . $layout_name . $this->getScriptsExtension();	
	}
	
	/**
	 * This function adds a script
	 */
	public function addScript( $script_url, $attribs = array(), $position = 'prepend' )
	{
		$request			= Request_HTTP::getInstance();
		$baseUrl			= $request->getBaseUrl();
		$prepared_script	= null;
		
		if ( false === strpos($script_url, '<script>', 0))
		{
			if ( false === strpos($script_url, 'http', 0) )
				$script_url	= $baseUrl . $script_url;
		
		
			$basic_attribs	= array('type' 	=> 'text/javascript' );
			$attribs		= array_merge($basic_attribs, $attribs);
		
			$prepared_script	= array('src' => $script_url, 'attribs' => $attribs);
		}
		else
		{
			$prepared_script	= array('script' => $script_url);
		}						
		
		// Add the script
		if ( $position == 'prepend')
		{
			// Add to the top of array
			array_unshift($this->_scripts, $prepared_script);
		}
		else
		{
			// Add to the bottom of array
			array_push($this->_scripts, $prepared_script);
		}
		
		return $this;
	}
	 
	/**
	 * This function gets all scripts
	 */
	public function getScripts ( $asArray = false )
	{
		if (! empty ( $this->_scripts ) )
		{
			// Return entire array
			if ( $asArray )
				return $this->_scripts;
			
			
			// Return html formatted
			$scripts	= null;
			foreach ($this->_scripts as $script)
			{
				if (array_key_exists('script', $script))
					$scripts	.= '//<![CDATA['. "\n" .$script['script'] . "\n//]]>";		// Already formatted script
				else
					$scripts	.= wrapHtml('', 'script', array_merge(array('src' => $script['src']),$script['attribs'])) . "\n";	
			}
			return $scripts;
		}	
	}
	
	/**
	 * This function adds a CSS script
	 */
	public function addCSS( $css_file, $attribs = array(), $position = 'prepend' )
	{
		$request			= Request_HTTP::getInstance();
		$baseUrl			= $request->getBaseUrl();
		$prepared_script	= null;
		
		if ( false === strpos($css_file, '<style>', 0))
		{
			if ( false === strpos($css_file, 'http', 0) )
				$css_file	= $baseUrl . $css_file;
		
		
			$basic_attribs	= array('type' 	=> 'text/css',
									'rel'	=> 'stylesheet');
			$attribs		= array_merge($basic_attribs, $attribs);
		
			$prepared_script	= array('link' => $css_file, 'attribs' => $attribs);
		}
		else
		{
			$prepared_script	= array('style' => $css_file);
		}						
		
		// Add the script
		if ( $position == 'prepend')
		{
			// Add to the top of array
			array_unshift($this->_css_scripts, $prepared_script);
		}
		else
		{
			// Add to the bottom of array
			array_push($this->_css_scripts, $prepared_script);
		}
	}
	 
	/**
	 * This function gets all CSS scripts
	 */
	public function getCSS ( $asArray = false )
	{
		if (! empty ( $this->_css_scripts ) )
		{
			// Return entire array
			if ( $asArray )
				return $this->_css_scripts;
			
			
			// Return html formatted
			$scripts	= null;
			foreach ($this->_css_scripts as $script)
			{
				if (array_key_exists('style', $script))
					$scripts	.= $script['style'];		// Already formatted style
				else
					$scripts	.= wrapHtml('', 'link', array_merge(array('href' => $script['link']),$script['attribs']), true) . "\n";	
			}
			return $scripts;
		}	
	}
}