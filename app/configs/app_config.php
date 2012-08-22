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
 * This handles the basic application configuration
 */

/*
 * Setup error reporting and management for effect application managment
 * 
 * Assuming using .htaccess with Apache server 
 */
defined('APP_ENV')
	|| define ('APP_ENV', getenv('APPLICATION_ENV') ?  getenv('APPLICATION_ENV') : 'production');


// The name of this flickr gallery app
defined('APP_NAME')
	|| define ( 'APP_NAME', 'My Flickr Demo App &trade;');

// Enable error reporting if not in production mode
if (APP_ENV != 'production')
{
	ini_set('display_errors', 1);
	error_reporting(E_ALL | E_STRICT); // Show all basic errors and non-strict compliance errors and warnings too
}
else
{
	ini_set('display_errors', 0);
}

// Set up locale  http://php.net
date_default_timezone_set('Australia/Sydney'); 	

// Define the my library path
defined ('APP_LIB')
	|| define ( 'APP_LIB', APP_PATH . 'flickrlib' . DS ) ;

//=======================================================
//
// Flickr Account details
//=======================================================
$flicker_user	=	array(
	'user_id'	=> '85143678@N07',
	'api_key'	=> 'f1e09c524cdda2a4613f8888b678b53b',
);