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
 
class Controller_View_Base extends Base_Class
{
	/**
	 * @var string Controller Name 
	 */
	protected	$_controller	= null;
	
	/**
	 * @var string Action Name 
	 */
	protected	$_action		= null;
	
	/**
	 * @var string Script path 
	 */
	protected	$_script		= null;
	

	public function loadOptions( $options = array() )
	{
		$this->_loadOptions($options);
	}
	
	public function render ( $script = null )
	{
		
		if ($script instanceof Controller_View)
		{
			// Retrieve the infomation
			$this->_script		= $script->_script;
			$this->_controller	= $script->_controller;
			$this->_action		= $script->_action;
		}
		
		if ( is_string( $script ) )
		{
			if (! readable( $script ) )
				throw new FlickrException( '\'' . $script . '\' <br /> is unreadable!' );
			$this->_script		= $script;
		}
		
		if (! readable( $this->_script ) )
			throw new FlickrException( 'There is no valid script to render.' );

		// Now do the rendering
		$content	= null;
		ob_start();

		include ($this->_script);
		
		$content	= ob_get_clean();
		
		return $content;
	}
	
	/**
	 * This is used to make urls
	 */
	public function fullUrl($link_name, $params = array(), $attribs = array(), $reset_params = true )
	{
		$request		= Request_HTTP::getInstance();
		$controller		= $action	= null;
		
		if ( null != $action	= getArrayVar( $params, 'action'))
			$controller			= getArrayVar( $params, 'controller', $request->controller_name );
		else
		{
			// No action was specfied
			if (null != $controller	= getArrayVar( $params, 'controller'))
			{
				$action				= getArrayVar( $params, 'action', 	'index' );
			}	
		}
		
		
		// Remove the used params
		if ( isset( $params['controller']) ) unset ($params['controller']);
		if ( isset( $params['action']) ) unset ($params['action']);
		
		$params_string	= params2string($params);
		
		if (! $reset_params)
		{
			$params			= array_merge($request->getParams(), $params);
		}
		$params_string		= params2string($params);
		
		$href				= $request->getBaseUrl() . $controller;
		if ( null != $action)
			$href .=  '/' . $action;
		$href		= remove_trailing_slash($href);
		if ( null != $params_string)
			$href .= $params_string;
			
		$link_attribs		= array_merge(array(
			'href' 	=>	$href
		), $attribs);
		
		return wrapHtml($link_name, 'a' , $link_attribs);
	} 
	
	/**
	 * This is used to make urls link alone
	 */
	public function fullUrlLink( $params = array(), $reset_params = true )
	{
		$request		= Request_HTTP::getInstance();
		$controller		= $action	= null;
		
		if ( null != $action	= getArrayVar( $params, 'action'))
			$controller			= getArrayVar( $params, 'controller', $request->controller_name );
		else
		{
			// No action was specfied
			if (null != $controller	= getArrayVar( $params, 'controller'))
			{
				$action				= getArrayVar( $params, 'action', 	'index' );
			}	
		}
		
		
		// Remove the used params
		if ( isset( $params['controller']) ) unset ($params['controller']);
		if ( isset( $params['action']) ) unset ($params['action']);
		
		$params_string	= params2string($params);
		
		if (! $reset_params)
		{
			$params			= array_merge($request->getParams(), $params);
		}
		$params_string		= params2string($params);
		
		$href				= $request->getBaseUrl() . $controller;
		if ( null != $action)
			$href .=  '/' . $action;
		if ( null != $params_string)
			$href .=  $params_string;
			
		return $href;
	} 
	
	public function getBaseUrl()
	{
		$request	= Request_HTTP::getInstance();
		
		return	$request->getBaseUrl();
	}
}