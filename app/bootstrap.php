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
 * This loads all the relevant files for the gallery to function
 * 
 */

/* Load application config */
require_once(APP_PATH . 'configs' . DS . 'app_config.php');

/* Load application classes */
require_once(APP_LIB. 'base.php');
/* Exception handlers */
require_once(APP_LIB . 'flickr_exceptions.php');

/* Load the small front controllers */
require_once(APP_LIB . 'controller_front.php');

require_once(APP_PATH . 'classes' . DS . 'application.php');


/* Setup Flickr API Class */
require_once(APP_PATH . 'lib' . DS . 'flickr_api.php' );
 
Flickr_API::getInstance()->setupUser($flicker_user);

/* Run application */
$gallery	= FlickrApp::getInstance();

$gallery->run();