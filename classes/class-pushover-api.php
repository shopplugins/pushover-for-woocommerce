<?php
/**
 * Pushover_Api class.
 *
 */
/* 
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pushover_Api {

	private $endpoint   = 'https://api.pushover.net/1/messages.json';
	private $site_api   = '';
	private $user_api   = '';
	private $device     = '';
	private $priority   = '';
	private $title      = '';
	private $message    = '';
	private $url        = '';

   /**
    * Constructor
    *
    * @access public
    */
	public function __construct() {
		
	}
	
   /*
	* Sets the Site API for Pushover
	*
    * @param string $site_api
    *
	*/
	public function setSiteApi( $site_api ) {
		$this->site_api = $site_api;
	}

   /*
	*  Getter for Site API
    *
	* @return string $site_api
	*/
	public function getSiteApi() {
		return $this->site_api;
	}

   /*
	*  setUserApi()
	*
	*/
	public function setUserApi( $user_api ) {
		$this->user_api = $user_api;
	}

   /*
	* getUserApi() 
	*
    * @return string @site_api
	*/
	public function getUserApi() {
		return $this->user_api;
	}

   /*
	*  Set Device ID to send to Pushover (optional)
	*
	*/
	public function setDevice( $device ) {
		$this->device = $device;
	}

   /*
	* Get Device ID to send to Pushover
	*
    * @return string device
	*/
	public function getDevice() {
		return $this->device;
	}

   /*
	*  Set Priority to send to Pushover (optional)
	*
	*/
	public function setPriority( $priority ) {
		$this->priority = $priority;
	}

   /*
	*  Get Priority to send to Pushover (optional)
	*
    * @return string $priority
	*/
	public function getPriority() {
		return $this->priority;
	}

   /*
	*  setTitle()
	*
	*/
	public function setTitle( $title ) {
		$this->title = $title;
	}

   /*
	*  getTitle()
	*
	*/
	public function getTitle() {
		return $this->title;
	}

   /*
	*  setMessage()
	*
	*/
	public function setMessage( $message ) {
		$this->message = $message;
	}

   /*
	*  getMessage()
	*
	*/
	public function getMessage() {
		return $this->message;
	}

   /*
	*  setUrl()
	*
	*/
	public function setUrl( $url ) {
		$this->url = $url;
	}

	/*
	 *  getUrl()
	 *
	 */
	public function getUrl() {
		return $this->url;
	}

	/*
	 *  send()
	 *
	 *  Use WP_remote_post to send message to Pushover API
	 *
	 * @throws exception - on any error in sending, or missing settings
	 * @return none
	 */
	function send() {
			
		if ( $this->site_api == '' )	
			throw new Exception('Missing Site API'); 
		if ( $this->user_api == '' )	
			throw new Exception('Missing User API'); 
		if ( $this->title == '' )	
			throw new Exception('Missing Title'); 
		if ( $this->message == '' )	
			throw new Exception('Missing Message'); 
		if ( $this->url == '' )	
			throw new Exception('Missing URL'); 

		$request = array(
						'token' 	=> $this->site_api,
						'user'  	=> $this->user_api,
						'title'     => $this->title,
						'message'	=> $this->message,
						'url' 		=> $this->url,
					);

		$response = wp_remote_post( 
				$this->endpoint,
				array(
					'timeout'   => 70,
					'sslverify' => 0,
					'body'      => $request
			    )
		);
	
		return $response; 
			
	}


}
