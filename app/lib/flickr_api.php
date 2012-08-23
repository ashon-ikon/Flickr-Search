<?php
/*---------------------------------------------------------------------
 * @project:	My Flicker Gallery
 * 
 * @Project		My Flicker Gallery Application
 * 
 * --------------------------------------------------------------------
 * Created by ashon on Aug 17, 2012
 * 
 * ashon.ikon @gmail.com
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
 */

/**
 * @internal This script relies on Base_Class
 * @internal This script relies on helper_function.php
 * 
 * This is the my api class for communication with Yahoo's Flickr (TM)
 * =====================================================================
 * Singleton class that can be invoked at any instance
 * 
 */
if (! class_exists('Base_Class'))
	throw new FlickrException('Base_Class not loaded yet. I need it to work', null, array('class' => 'Base_Class'));

if (! defined('HELPERS_LOADED'))
	throw new FlickrException('Helper classes needed not found. I need it to work');
	
class Flickr_API extends Base_Class
{
	
	/*
	 * -----------------------------------------------
	 * Error Codes	Integers
	 */
	const AE_UNKNOWN_USER			= 2;			// A user_id was passed which did not match a valid flickr user.
	const AE_PARAMETERLESS_SEARCH	= 3;			// To perform a search with no parameters (to get the latest public photos, please use flickr.photos.getRecent instead).
	const AE_NO_VIEWING_ACCESS		= 4;			// The logged in user (if any) does not have permission to view the pool for this group.
	
	const AE_FLICKR_NOT_AVAILABLE	= 10;			// The Flickr API search databases are temporarily unavailable.
	const AE_INVALID_MACHINE_TAGS	= 11;			// The query styntax for the machine_tags argument did not validate.
	const AE_MAXIMUM_MACHINE_TAGS	= 12;			// The maximum number of machine tags in a single query was exceeded.
	
	const AE_YOU_CAN_ONLY_SEARCH_YOURS	= 17;		// The call tried to use the contacts parameter with no user ID or a user ID other than that of the authenticated user.
	const AE_ILLOGICAL_ARGUMENTS	= 18;			// The request contained contradictory arguments.
	
	const AE_INVALID_SIGNATURE		= 96;			// The passed signature was invalid.
	const AE_MISSING_SIGNATURE		= 97;			// The call required signing but no signature was sent.
	const AE_LOGIN_FAILED			= 98;			// The login details or auth token passed were invalid.
	const AE_USER_NOT_LOGGED_IN		= 99;			//The method requires user authentication but the user was not logged in, or the authenticated method call did not have the required permissions.
	const AE_INVALID_API_KEY		= 100;			// The API key passed was not valid or has expired.
	
	const AE_SERVICE_NOT_AVAILABLE	= 105;			// The requested service is temporarily unavailable.
	const AE_FORMAT_NOT_FOUND		= 111;			// The requested response format was not found.
	const AE_METHOD_NOT_FOUND		= 112;			// The requested method was not found.
	const AE_INVALID_SOAP_ENVELOPE	= 114;			// The SOAP envelope send in the request could not be parsed.
	const AE_INVALID_XML_RPC_METHOD	= 115;			// The XML-RPC request document could not be parsed. 
	const AE_BAD_URL_FOUND			= 116;			// One or more arguments contained a URL that has been used for abuse on Flickr.
	
	/**
	 * @var array error code array
	 * 
	 * Messages defining the error codes
	 */
	protected	$_error_codes		= array(
		self::AE_UNKNOWN_USER				=> 	'A user_id was passed which did not match a valid flickr user.',
		self::AE_PARAMETERLESS_SEARCH		=> 	'To perform a search with no parameters (to get the latest public photos, please use flickr.photos.getRecent instead).',
		self::AE_NO_VIEWING_ACCESS			=> 	'The logged in user (if any) does not have permission to view the pool for this group.',
		self::AE_FLICKR_NOT_AVAILABLE		=>	'The Flickr API search databases are temporarily unavailable.',
		self::AE_INVALID_MACHINE_TAGS		=>	'The query styntax for the machine_tags argument did not validate.',
		self::AE_MAXIMUM_MACHINE_TAGS		=>	'The maximum number of machine tags in a single query was exceeded.',
		self::AE_YOU_CAN_ONLY_SEARCH_YOURS	=>	'The call tried to use the contacts parameter with no user ID or a user ID other than that of the authenticated user.',
		self::AE_ILLOGICAL_ARGUMENTS		=> 'The request contained contradictory arguments.',
		self::AE_INVALID_SIGNATURE			=> 'The passed signature was invalid.',
		self::AE_MISSING_SIGNATURE			=> 'The call required signing but no signature was sent.',
		self::AE_LOGIN_FAILED				=> 'The login details or auth token passed were invalid.',
		self::AE_USER_NOT_LOGGED_IN			=> 'The method requires user authentication but the user was not logged in, or the authenticated method call did not have the required permissions.',
		self::AE_INVALID_API_KEY			=> 'The API key passed was not valid or has expired.',
		self::AE_SERVICE_NOT_AVAILABLE		=> 'The requested service is temporarily unavailable.',
		self::AE_FORMAT_NOT_FOUND			=> 'The requested response format was not found.',
		self::AE_METHOD_NOT_FOUND			=> 'The requested method was not found.',
		self::AE_INVALID_SOAP_ENVELOPE		=> 'The SOAP envelope send in the request could not be parsed.',
		self::AE_INVALID_XML_RPC_METHOD		=> 'The XML-RPC request document could not be parsed.',
		self::AE_BAD_URL_FOUND				=> 'One or more arguments contained a URL that has been used for abuse on Flickr.',
	);
	
	/**
	 * @var	string	api user_id
	 */
	protected	$_user_id				= 	null;

	/**
	 * @var	string	api key
	 */
	protected	$_api_key				= 	null;
		
	/**
	 * @var	string	api service url
	 */
	protected	$_service_endpoint		= 	'http://api.flickr.com/services/rest/';
	
	/**
	 * @var resource cURL handle
	 */
	protected	$_curl_handle			= null;
	
	/**
	 * @var resource cURL handle2 for processing dual requests
	 */
	protected	$_curl_handle2			= null;
	
	/**
	 * @var array	response options
	 */
	protected	$_reponse_options		= array(
		'assoc'		=> true
	);
	
	/**
	 * @var resource cURL options
	 */
	protected	$_curl_options			= array(
//		'CURLOPT_FOLLOWLOCATION'	=> true,
		'CURLOPT_RETURNTRANSFER'	=> true,
//		'CURLOPT_HEADER'			=> false,
//		'CURLOPT_ENCODING'			=> 'gzip, deflate',
		'CURLOPT_USERAGENT'			=> 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:13.0) Gecko/20100101 Firefox/13.0'
	);
	
	/**
	 * @var array default settings
	 */
	protected	$_data_options			= array(
		'format'		=> 'json'
	);
	
	/**
	 * @var static isntance to object
	 */
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
	 * This function sets up the user's account
	 */
	public function setupUser( $user_params = array() )
	{
		if ( !empty( $user_params ))
		{
			// Load recursively
			$this->_loadOptions($user_params);
		}
	}
	
	
	/**
	 * This function attempts to make a post request to the Flickr server
	 */
	public function post( array $data )
	{
		if ( ! empty( $data ) )
		{
			return $this->request( $data , 'post');
		}
	}
	
	/**
	 * This function attempts to make a post request to the Flickr server
	 */
	public function get( array $data )
	{
		if ( ! empty( $data ) )
		{
			return $this->request( $data , 'get');
		}
	}
	
	/**
	 * This makes the request using cURL
	 */
	public function request( $data , $method = 'post' )
	{
		if ( is_array( $data ) && ! empty( $data ) )
		{
			// Only parse if data is valid
			
			$data	= array_merge($this->_data_options, $data );

			$data	= array_merge(
				array(
					'user_id'	=> $this->_user_id,
					'api_key'	=> $this->_api_key,
					'method'	=> 'flickr.test.echo'		// This should be overriden !!
			), $data );
			
			// Sanitize data
			foreach ($data as $key => $value)
				if ($value == null ) unset ($data[$key]);
			ksort($data);
			
			// execute the request
			$response		= null;
			$params		= '';
			foreach ( $data as $key => $value )
				$params	.= $key . '='. urlencode($value) . '&';
			
			$params	= substr($params, 0, -1);
			$url	= $this->_service_endpoint . '?' . $params;
			
			if( extension_loaded('curl'))
			{
				// Run using curl
				$ch		= $this->_getCurlHandle();
				
				curl_setopt($ch, CURLOPT_URL		, $url);
				curl_setopt($ch, CURLOPT_POSTFIELDS	, $data);
				curl_setopt($ch, CURLOPT_POST		, $method == 'post' ? true : false );
				
				$response	= curl_exec($ch);
//				curl_close($ch);
				
			}// End cURL attempt
			else
			{
				// Use fopen
				
//				
//				pr($url);
//				
				if ( $f	= @fopen( $url, 'r') )
				{
					while ( $line = @fgets ($f, 1024 ) )
					{
						$response	.= $line; 
					}
					fclose($f);
				}
				else
					throw new FlickrException ( 'I failed reaching Flickr Server.' );
				
			}// End fopen

			/**
			 * Handle JSON type
			 */
			$format		= getArrayVar($data, 'format');
			if ($format	== 'json')
			{
				if ( strpos( $response, 'jsonFlickrApi(', 0) !== false )
				{
					// We got a true Flickr json response
					$response	= str_replace('jsonFlickrApi(', '', $response);
					$response	= substr($response, 0, -1);							// Remove the trailing ')'
					$response	= json_decode($response, $this->_reponse_options['assoc']);
				}
			}
			return $response;
				
		}
	}
	
	/**
	 * This function gets the cURL handle
	 * 
	 * @var string initialized cURL hanlde
	 */
	protected function _getCurlHandle()
	{
		if ( null === $this->_curl_handle)
		{
			if ( '' == $this->_service_endpoint )
				$this->_throwError ( 'Service Endpoint undefined');
				
			$this->_curl_handle	= curl_init();
			
			foreach ($this->_curl_options as $key => $value)
				curl_setopt($this->_curl_handle, constant($key), $value );
		}
		return $this->_curl_handle;
	}
		
	/**
	 * This method helps to interprete the error message
	 * 
	 * @return mixed message | array (message , code)
	 */
	public function interpreteResponse($code = 0, $return_array = false )
	{
		$message		=	getArrayVar($this->_error_codes, $code); 
		
		if ( null == $message )
			$message	= 'Unknown error occurred!<br />';
		
		if (! $return_array)
			return $message;
		else
			return array(
					'message' 	=> $message,
					'code'		=> $code
			);
	}
	
	/**
	 * This function throws the exception
	 */
	protected function _throwError ($message = '', $code = 0, $options = array())
	{
		$message	.= $this->interpreteResponse($code);
			
		throw new FlickrException($message , $code, $options);
	}
	
	/*===================================API====FUNCTIONS===================
	 * 
	 * 
	 *		FLICKr 		API FUNCTIONS 
	 *  
	 * 
	 *======================================================================*/
	
	
	/**
	 * This function gets the url of an image
	 */
	public function getImageInfoById ( $photo_id, $image_params = array(), $sizes_alone = false, $format = 'json' )
	{
		$method			= 'flickr.photos.getSizes';
		
		$data			= array_merge(array(
			'method'	=> $method,
			'photo_id'	=> $photo_id,
			
		), $this->_data_options, $image_params, array('format'	=> $format));
		
		$image_sizes = $this->get( $data );
		
		$image_info	 = array();
		if (! $sizes_alone )
		{
			$data['method']	= 'flickr.photos.getInfo';
			$image_info		= $this->get( $data ); 	
		}

		return array_merge($image_sizes, $image_info);
	}
	 
	/**
	 * This function gets the detail of an image
	 */
	public function getImageUrlFromParams ( $image_params = array() )
	{
		if (! empty($image_params) )
		{
			$url		= null;
			return;
		}
	}
	 
	/**
	 * This function gets the Gallery info
	 */
	public function getGalleryInfo ( $user_id = null, $format = 'json' )
	{
		$method			= 'flickr.galleries.getInfo';
		
		$data			= array_merge(array(
			'method'	=> $method,
			'user_id'	=> $user_id,
			'format'	=> $format
		), $this->_data_options);
			
	}
	 
	/**
	 * This function gets the list of Galleries
	 */
	public function getGalleriesList ( $user_id = null , $gallery_params = array(), $format = 'json' )
	{
		$method			= 'flickr.galleries.getList';
		
		$data			= array_merge(array(
			'method'	=> $method,
			'format'	=> $format
		), $this->_data_options, $gallery_params);
		
			
	}
	 
	/**
	 * This function gets the list of recent images
	 */
	public function getRecentImages ( $images_params = array(), $format = 'json')
	{
		$method			= 'flickr.photos.getRecent';
		
		$data			= array_merge(array(
			'method'	=> $method,
			'format'	=> $format
		), $this->_data_options, $images_params);
		
		return $this->get( $data );	
	}
	 
	/**
	 * This function searches for images that match a particular param
	 */
	public function searchImages ( $search_text = null , $search_params = array(), $format = 'json' )
	{
//		if (trim($search_text) == '' )
//			$this->_throwError( 'Parameterless searches have been disabled. Please use flickr.photos.getRecent instead.', self::AE_PARAMETERLESS_SEARCH );
		$method			= 'flickr.photos.search';	

		$agressive_mode		= getArrayVar($search_params, 'agressive', true );
		
		// If we are to do agressive search
		if ( $agressive_mode && $search_text != '')
		{
//			$words		= preg_split('/ +/', $search_text);
//			$words		= array_filter($words);		
			$search_text	= '"' . $search_text . '"';
		}


		
		$data			= array_merge(array(
			'method'	=> $method,
			'text'		=> $search_text,
			'format'	=> $format
		), $this->_data_options, $search_params);
		
		
		if (array_key_exists('agressive', $data))
		{
			unset($data['agressive']);
		}
		
		
		return $this->get( $data );
	}
	
	/**
	 * Destroy safely
	 */
	public function __destruct(){
		$ch		= $this->_getCurlHandle();
		
		curl_close($ch);
	}
	 
}