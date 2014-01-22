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
 * Hybrid_Providers_Yahoo class, wrapper for Yahoo user accounts and hosted apps 
 */
class Hybrid_Providers_Yahoo extends Hybrid_Provider_Model
{
	var $openidIdentifier = "https://www.yahoo.com/"; 

	// --------------------------------------------------------------------

   /**
	* IDp wrappers initializer 
	*/
	function initialize()
	{ 
		require_once Hybrid_Auth::$config["path_libraries"] . "OpenID/openid.php"; 

		$this->api = new LightOpenID( $_SERVER['SERVER_NAME'] ); 
	}
 
   /**
	* begin login step
	* 
	* Yahoo_service must be "User" for Yahoo user accounts service or "Apps" for Yahoo hosted Apps
	* if chosen Yahoo_service eq "Apps", Yahoo_hosted_domain parameter will be required
	*
	* build an normalized OpenID url to autentify the user with selected Yahoo_service openid identifier
	*/
	function loginBegin( )
	{ 
		$this->api->identity  = $this->openidIdentifier;
		$this->api->returnUrl = $this->endpoint;
		$this->api->required  = ARRAY( 
									'namePerson/first'		 ,
									'namePerson/last'		 ,
									'contact/email'          ,
									'namePerson'             ,
									'birthDate'              , 
									'person/gender'          ,
									'person/guid'            ,
									'contact/postalCode/home',
									'contact/city/home'      ,
									'contact/country/home'   ,
									'contact/phone/default'  ,
									'pref/language'          ,
									'pref/timezone'          ,
									'media/biography'        ,
									'media/image/default'    ,
								);

		# redirect the user to the selected Yahoo openid identifier authentification page, and exit 
		Hybrid_Auth::redirect( $this->api->authUrl() );
	}
	
   /**
	* finish login step 
	*/
	function loginFinish( )
	{
		# if user don't garant acess of their data to your site, halt with an Exception
		if( $this->api->mode == 'cancel')
		{
			throw new Exception( "Authentification failed! User has canceled authentication!", 5 );
		} 

		# if something goes wrong
		if( ! $this->api->validate() )
		{
			throw new Exception( "Authentification failed. Invalid request recived!", 5 );
		}
 
		$response = $this->api->getAttributes();

		# if no data recived
		if( ! $response )
		{
			throw new Exception( "Authentification failed. Invalid data recived!", 5 );
		}

		# fetch recived user data
		$this->user->profile->identifier  = $this->api->identity;

		$this->user->profile->firstName   = @ $response["namePerson/first"];
		$this->user->profile->lastName    = @ $response["namePerson/last"];
		$this->user->profile->displayName = @ $response["namePerson"];
		$this->user->profile->email       = @ $response["contact/email"];
		$this->user->profile->language    = @ $response["pref/language"];
		$this->user->profile->country     = @ $response["pref/country"]; 
		$this->user->profile->gender      = @ strtolower( $response["person/gender"] ); 
		$this->user->profile->photoURL    = @ $response["media/image/default"] ; 

		if( ! $this->user->profile->displayName ){
			$this->user->profile->displayName = trim( $this->user->profile->firstName . " " . $this->user->profile->lastName );
		}

		if( $this->user->profile->gender == "f" ){
			$this->user->profile->gender = "female";
		}

		if( $this->user->profile->gender == "m" ){
			$this->user->profile->gender = "male";
		} 

		// set user as logged in
		$this->setUserConnected();

		// then store it
		Hybrid_Auth::storage()->set( "hauth_session.{$this->providerId}.user", $this->user );
	}

   /**
	* load the user profile from the IDp api client
	*/
	function getUserProfile()
	{
		$this->user = Hybrid_Auth::storage()->get( "hauth_session.{$this->providerId}.user" ) ;

		if ( ! is_object( $this->user ) )
		{
			throw new Exception( "User profile request failed! User is not connected to {$this->providerId} or his session has expired.", 6 );
		} 

		return $this->user->profile;
	}
}
