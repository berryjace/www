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
 * Hybrid_Providers_Live class, wrapper for Windows Live 
 */
class Hybrid_Providers_Live extends Hybrid_Provider_Model
{
   /**
	* IDp wrappers initializer 
	*/
	function initialize() 
	{
		if ( ! $this->config["keys"]["id"] || ! $this->config["keys"]["secret"] )
		{
			throw new Exception( "Your application id and secret are required in order to connect to {$this->providerId}.", 4 );
		}

		// Application Specific Globals
		define( 'WRAP_CLIENT_ID'       , $this->config["keys"]["CONSUMER_ID"]);
		define( 'WRAP_CLIENT_SECRET'   , $this->config["keys"]["CONSUMER_SECRET"] ); 
		define( 'WRAP_CALLBACK'        , $this->endpoint );
		define( 'WRAP_CHANNEL_URL'     , Hybrid_Auth::$config["base_url"] . "?get=windows_live_channel" );

		// Live URLs required for making requests.
		define('WRAP_CONSENT_URL'      , 'https://consent.live.com/Connect.aspx');
		define('WRAP_ACCESS_URL'       , 'https://consent.live.com/AccessToken.aspx');
		define('WRAP_REFRESH_URL'      , 'https://consent.live.com/RefreshToken.aspx');

		require_once Hybrid_Auth::$config["path_libraries"] . "WindowsLive/OAuthWrapHandler.php";  

		$this->api = new OAuthWrapHandler();
	}

	// --------------------------------------------------------------------

   /**
	* begin login step 
	*/
	function loginBegin()
	{ 
		$this->api->ExpireCookies(); 
		
		Hybrid_Auth::redirect( WRAP_CONSENT_URL . "?wrap_client_id=" . WRAP_CLIENT_ID . "&wrap_callback=" . urlencode( WRAP_CALLBACK ) . "&wrap_scope=WL_Profiles.View" ); 
	}

   /**
	* finish login step 
	*/ 
	function loginFinish()
	{
		$response = $this->api->ProcessRequest();

		if ( ! isset( $response['c_uid'] ) || ! isset( $response['c_accessToken'] ) )
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Token.", 5 );
		}

		// set user as logged in
		$this->setUserConnected();

		# store access token
		$this->token( "access_token",  $response['c_accessToken'] ); 

		# store the user id. 
		$this->user->providerUID = (string) $response['c_uid']; 
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

		$access_token = $this->token( "access_token" ); 
		$info_url     = 'http://apis.live.net/V4.1/cid-'. $this->user->providerUID .'/Profiles/1-' . $this->user->providerUID ;

		$response    = $this->api->GET($info_url, false, $access_token );
		$response    = @ json_decode( $response );

		if ( ! is_object( $response ) )
		{
			throw new Exception( "User profile request failed! {$this->providerId} returned an invalide xml data.", 6 );
		}

		$this->user->profile->firstName   = @ (string) $response->FirstName; 
		$this->user->profile->lastName    = @ (string) $response->LastName; 
		$this->user->profile->profileURL  = @ (string) $response->UxLink; 
		$this->user->profile->gender      = @ (string) $response->Gender; 
		$this->user->profile->email       = @ (string) $response->Emails[0]->Address; 
		$this->user->profile->displayName = trim( $this->user->profile->firstName . " " . $this->user->profile->lastName );

		if( $this->user->profile->gender == 1 ){
			$this->user->profile->gender = "female";
		}
		elseif( $this->user->profile->gender == 2 ){
			$this->user->profile->gender = "male";
		}

		return $this->user->profile;
	} 
}
