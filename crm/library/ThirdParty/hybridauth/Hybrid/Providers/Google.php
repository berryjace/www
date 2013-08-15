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
 * Hybrid_Providers_Google class, wrapper for Google user accounts and hosted apps 
 */
class Hybrid_Providers_Google extends Hybrid_Provider_Model
{
	var $service           = "Users"; // can be "Users" (google user accounts) or "Apps" (hosted apps)
	var $googleIdentifiers = Array
							(
								"Users" => "https://www.google.com/accounts/o8/id", 
								"Apps" 	=> "https://www.google.com/accounts/o8/site-xrds?hd={hosted_domain_name}"
							);
	var $openidIdentifier  = NULL;
	var $hostedDomain      = NULL; // hosted_domain_name for Google "Apps" (hosted) accounts 

	// --------------------------------------------------------------------

   /**
	* IDp wrappers initializer 
	*/
	function initialize()
	{
		if( ! isset(  $this->params["google_service"] ) )
		{
			$this->params["google_service"] = "Users";
		}

		require_once Hybrid_Auth::$config["path_libraries"] . "OpenID/openid.php"; 

		$this->api = new LightOpenID( $_SERVER['SERVER_NAME'] ); 
	}
 
   /**
	* begin login step
	* 
	* google_service must be "User" for Google user accounts service or "Apps" for Google hosted Apps
	* if chosen google_service eq "Apps", google_hosted_domain parameter will be required
	*
	* build an normalized OpenID url to autentify the user with selected google_service openid identifier
	*/
	function loginBegin( )
	{
		# google_service must be "User" for Google user accounts service or "Apps" for Google hosted Apps
		if( $this->params["google_service"] == "Users" )
		{
			$this->openidIdentifier = $this->googleIdentifiers["Users"];
		}
		elseif( $this->params["google_service"] == "Apps" )
		{
			# if chosen google_service eq "Apps", google_hosted_domain parameter will be required
			if( isset( $this->params["google_hosted_domain"] ) && ! empty( $this->params["google_hosted_domain"] ) )
			{
				$this->hostedDomain = $this->params["google_hosted_domain"];
			}
			else
			{
				throw new Exception( "Authentification failed! Missing reuqired parameter [google_hosted_domain] for hosted Apps service!", 5 );
			}

			$this->openidIdentifier = str_replace( "{hosted_domain_name}", $this->hostedDomain, $this->googleIdentifiers["Apps"] );
		}
		else
		{
			throw new Exception( "Authentification failed! Only Google [Users] and [Apps] accounts services are supported!", 5 );
		} 

		$this->service = $this->params["google_service"];

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

		# redirect the user to the selected google openid identifier authentification page, and exit 
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
