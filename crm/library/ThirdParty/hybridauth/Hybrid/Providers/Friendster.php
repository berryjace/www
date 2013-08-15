<?php
/**
* HybridAuth
* 
* A Social-Sign-On PHP Library for authentication through identity providers like Facebook,
* Twitter, Google, Yahoo, LinkedIn, MySpace, Windows Live, Tumblr, Friendster, OpenID, PayPal,
* Vimeo, Foursquare, AOL, Gowalla, and others.
*
* Copyright (c) 2009-2011 (http://hybridauth.sourceforge.net) 
*/

/**
 * Hybrid_Providers_Friendster class, wrapper for Friendster auth/api 
 */
class Hybrid_Providers_Friendster extends Hybrid_Provider_Model
{
   /**
	* Friendster login url 
	*/
	var $authUrlBase = "http://www.friendster.com/widget_login.php?api_key=";

	// --------------------------------------------------------------------

   /**
	* IDp wrappers initializer
	*
	* The main job of wrappers initializer is to performs (depend on the IDp api client it self): 
	*     - include some libs nedded by this provider,
	*     - check IDp key and secret,
	*     - set some needed parameters (stored in $this->params) by this IDp api client
	*     - create and setup an instance of the IDp api client on $this->api 
	*/
	function initialize() 
	{
		if ( ! $this->config["keys"]["key"] || ! $this->config["keys"]["secret"] )
		{
			throw new Exception( "Your application key and secret are required in order to connect to {$this->providerId}.", 4 );
		} 

		require_once Hybrid_Auth::$config["path_libraries"] . "Friendster/FriendsterAPI.php";

		// If we have a session_key, set it
		if ( $this->token("session_key" ) )
		{
			$this->api = new FriendsterAPI( $this->config["keys"]["key"], $this->config["keys"]["secret"], $this->token("session_key" ) );
		}
	}

	// --------------------------------------------------------------------

   /**
	* begin login step
	* 
	* All what we need to know is on Access from an External Web Application section of developer guide
	* see http://www.friendster.com/developer
	* 
	*	// Access from an External Web Application
	*	// =======================================
	*	// External Web applications can access the Friendster APIs after authentication through the Login URL.
	*	// A login prompt lets the user enter his/her username and password and then calls the Callback URL.
	*	// The Login URLs for production are as follows:
	*	    // http://www.friendster.com/widget_login.php?api_key=<API_KEY>&next=<ENCODED_ARGS>
	*		// Example:
	*			// For instance the following application passes its own internal user ID to the login request:
	*			// http://www.friendster.com/widget_login.php?api_key=2e37638f335f0545c3719d34f4d20ed0 
	*			// Assuming the callback URL is http://mydomain/apps/1444, it would be called as follows:
	*			// http://mydomain/apps/1444?
	*			    // api_key=2e37638f335f0545c3719d34f4d20ed0&
	*			    // src=login&
	*			    // auth_token=846d79676186569.74429552& 
	*			    // lang=en-US&
	*			    // nonce=326233766.3425&
	*			    // sig=012345678901234567890123456789012
	*
	*/
	function loginBegin()
	{
		Hybrid_Auth::redirect( $this->authUrlBase . $this->config["keys"]["key"] );
	}

   /**
	* finish login step
	*/
	function loginFinish()
	{
		$this->api = new FriendsterAPI( $this->config["keys"]["key"], $this->config["keys"]["secret"] );

		// try to validate the request was really constructed and sent by Friendster 
		try{ 
			FriendsterAPI::validateSignature( $this->config["keys"]["secret"] );
		}
		catch( FriendsterAPIException $e )
		{
			throw new Exception( "Authentification failed! {$this->providerId} has returned an invalide response.", 5 ); 
		}

		/* use this if you created a class object with $session_key == null */
			// public function session($token = null) {
		# ..., okey, will make use of it, thanks

		$auth_token = @ $_REQUEST['auth_token'];

		if ( ! $auth_token )
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Authentication token.", 5 );
		}

		$session_key = $this->api->session( $auth_token );

		if ( ! $session_key )
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Session key.", 5 );
		}

		$this->token("auth_token" , $auth_token  ); 
		$this->token("session_key", $session_key );

		// set user as logged in
		$this->setUserConnected();
	}

	/**
	* load the user profile from the IDp api client
	*/
	function getUserProfile()
	{
		if ( ! $this->api || ! $this->isUserConnected() )
		{
			throw new Exception( "User profile request failed! User not connected to {$this->providerId}.", 6 );
		}

		// Access user info from friendster API
			// from http://www.friendster.com/developer
			// Get User Information
				// Resource URL: http://api.friendster.com/v1/user/<UID>
				// Resource Methods: GET (Retrieval of user information)
				// Resource Description: API to get user information on one or more users. If no user ID is specified, 
				// information about current logged in user will be returned.
				//                   =======================================

		# ..., cool, anyway we dont know the user id 
		$data = $this->api->users( NULL, NULL, FriendsterAPIResponseType::JSON ); 

		// if the provider identifier is not recived, we assume the auth has failed
		if ( ! isset( $data["user"] ) )
		{ 
			throw new Exception( "User profile request failed! {$this->providerId} api returned an invalid response.", 6 );
		} 

		$data = $data["user"]; 

		$this->user->profile->identifier    = $data["uid"];
		$this->user->profile->description  	= @ $data['about_me'];
		$this->user->profile->gender     	= @ strtolower( $data['gender'] );
		$this->user->profile->firstName     = @ $data['first_name'];
		$this->user->profile->lastName     	= @ $data['last_name'];
		$this->user->profile->displayName   = trim( $this->user->profile->firstName . " " . $this->user->profile->lastName );

		$this->user->profile->photoURL   	= @ $data['primary_photo_url'];
		$this->user->profile->profileURL 	= @ $data['url'];  

		$this->user->profile->birthDay 		= @ $data['birthday']['day'];  
		$this->user->profile->birthMonth 	= @ $data['birthday']['month'];  
		$this->user->profile->birthYear 	= @ $data['birthday']['year'];  

		$this->user->profile->country 		= @ $data['location']['country'];  
		$this->user->profile->region 		= @ $data['location']['state'];  
		$this->user->profile->city 			= @ $data['location']['city'];  
		$this->user->profile->zip 			= @ $data['location']['zip'];  

		return $this->user->profile;
	}
}
