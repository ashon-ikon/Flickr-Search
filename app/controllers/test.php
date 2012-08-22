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
class TestController extends Controller_Abstract
{
	public function indexAction()
	{
		echo 'someplace';
	}

	public function showAction()
	{
		$layout	= $this->_getLayout();
		
		$layout->title	= 'Test World';
		$this->view->test_string	= 'Hello World';
	}
	
	public function deprecatedAction()
	{

//		$query_data		= array(
//					'method'	=> 'flickr.photos.getInfo',
//					'user_id'	=> '',
//					'photo_id'	=> '7570132974',
//								);
//		
/**
 * array(
				'per_page' 	=> 20,
				'page' 		=> 4,
				'format' 	=> 'json',
				'method'	=> 'flickr.photos.search',
				'user_id'	=> '',
				'text'		=> 'andy o'
				)
 */
		
	}
	
}