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
class PhotosController extends Controller_Abstract
{
	/**
	 * @var object Flickr Object
	 */
	protected	$_flickr_obj	= null;
	
	public function init()
	{
		$this->_flickr_obj		= Flickr_API::getInstance();		
	}
	
	public function indexAction()
	{
		// redirect 
		$this->_forward( 'detail' );
//		$this->invokeControllerAction( null, 'detail', null, true);
	}
	
	public function detailAction()
	{
		/*
		 * ==================================================
		 * This view shows a details about an image.
		 */
		 
		// Request obj
		$request		= $this->getRequest();
		
		$flicker_obj	= $this->_flickr_obj;
		
		// Get viewing options
		$image_id		= $request->getParam('id');
		$data			= array(
			
		);
		
		$response = $flicker_obj->getImageInfoById( $image_id, $data, false, 'json' );
		$this->view->response	= $response;
		
		// Set title
		$iamge_tile	= getArrayVar($response, 'title');
		
		$page_title	=	'Photo';
		$this->_setPageTitle('Recent images on Flickr | Ashon App', true);
		
	}
	
	/**
	 * This action does the search
	 */
	public function searchAction()
	{
		// Request obj
		$request		= $this->getRequest();
		
		$this->_setPageTitle( 'Search');
		if (($request->isPost() && $request->getParam('ashon')) || $request->getParam('q') != '')
		{
			$flicker_obj	= $this->_flickr_obj;
			
			
			// Get viewing options
			$query			= $request->getParam('q');
			$per_page		= $request->getParam('perpage', 10);
			$start_page		= $request->getParam('p', 1);
			$extras			= $request->getParam('ex', 'owner_name,url_n,url_o');
			
			$this->_setPageTitle( 'Search for ' . urldecode($query));
			
			$data			= array(
				'page'		=> $start_page,
				'per_page'	=> $per_page,
				'extras'	=> $extras,
				'user_id'	=> ''
			);
			
			$response		= $flicker_obj->searchImages( $query, $data );
			
			$this->view->query		= $query;
			$this->view->response	= $response;
			
			
		}
	}
	
}