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
class HomeController extends Controller_Abstract
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
		/*
		 * ==================================================
		 * This view shows a few recent images so the user can
		 * choose one and view.
		 */
		 
		$this->_setPageTitle('Recent images on Flickr | Ashon App', true);

		// Request obj
		$request		= $this->getRequest();
		
		$flicker_obj	= $this->_flickr_obj;
		
		// Get viewing options
		$per_page		= $request->getParam('perpage', 10);
		$start_page		= $request->getParam('p', 1);
		$extras			= $request->getParam('ex', 'description,license,owner_name,date_upload, date_taken,url_n,url_o');
		
		$data			= array(
			'page'		=> $start_page,
			'per_page'	=> $per_page,
			'extras'	=> $extras,
		);
		
		$response = $flicker_obj->getRecentImages( $data ); //searchImages( ' ', $query_data );
		$this->view->response	= $response;
	}
	
	public function aboutAction()
	{
		$this->_setPageTitle('About my sleepless app');	
	}
	
}