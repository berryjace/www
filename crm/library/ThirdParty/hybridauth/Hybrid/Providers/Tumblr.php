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
* Hybrid_Providers_Tumblr class, wrapper for Tumblr auth/api 
*/
class Hybrid_Providers_Tumblr extends Hybrid_Provider_Model
{
   /**
	* IDp wrappers initializer 
	*/
	function initialize() 
	{
		if ( ! $this->config["keys"]["key"] || ! $this->config["keys"]["secret"] )
		{
			throw new Exception( "Your application key and secret are required in order to connect to {$this->providerId}.", 4 );
		}

		require_once Hybrid_Auth::$config["path_libraries"] . "Tumblr/OAuth.php";
		require_once Hybrid_Auth::$config["path_libraries"] . "Tumblr/Tumblr.php";

		if( $this->token( "oauth_access_token" ) && $this->token( "oauth_access_token_secret" ) )
		{
			$this->api = new TumblrOAuth
							( 
								$this->config["keys"]["key"], $this->config["keys"]["secret"],
								$this->token( "oauth_access_token" ), $this->token( "oauth_access_token_secret" ) 
							);
		}
	}

   /**
	* begin login step 
	*/
	function loginBegin()
	{
 	    $this->api = new TumblrOAuth( $this->config["keys"]["key"], $this->config["keys"]["secret"] );

		$tokz = $this->api->getRequestToken( $this->endpoint ); 

		if ( $this->api->http_code != "200" )
		{
			throw new Exception( "Authentification failed! Could not connect to {$this->providerId}." );
		}

		/* store tokens for later */  
		$this->token( "oauth_request_token"       , $tokz["oauth_token"] ); 
		$this->token( "oauth_request_token_secret", $tokz["oauth_token_secret"] ); 

		# redirect user to Tumblr authorisation web page 
		Hybrid_Auth::redirect( $this->api->getAuthorizeURL( $tokz ) );
	}

   /**
	* finish login step
	* 
	* fetch returned parameters by The IDp client
	*/ 
	function loginFinish()
	{
		/* Create TumblrOAuth object with app key/secret and token key/secret from default phase */
		$this->api = new TumblrOAuth( 
							$this->config["keys"]["key"], $this->config["keys"]["secret"], 
							$this->token( "oauth_request_token" ), $this->token( "oauth_request_token_secret" ) 
						);

		$oauth_token    = @ $_REQUEST['oauth_token'];

		$oauth_verifier = @ $_REQUEST['oauth_verifier']; 

		if ( ! $oauth_token || ! $oauth_verifier )
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid OAuth Token and Verifier.", 5 );
		}

		$tokz = $this->api->getAccessToken( $oauth_verifier );

		if ( ! count( $tokz ) )
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Access Token.", 5 );
		}

		/* store the access tokens. Normally these would be saved in a database for future use. */
		$this->token( "oauth_access_token"        , $tokz['oauth_token'] );
		$this->token( "oauth_access_token_secret" , $tokz['oauth_token_secret'] ); 

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

		try{ 
			$response = $this->api->get( 'http://www.tumblr.com/api/authenticate' );

			$response = @ new SimpleXMLElement( $response ); 

			// the easy way (well 4 me at least)
			$xml2array = @ $this->xml2array( $response );
		}
		catch( Exception $e ){
			throw new Exception( "User profile request failed! {$this->providerId} returned an error while requesting the user profile.", 6 );
		}

/* ex:
<?xml version="1.0" encoding="UTF-8"?>
<tumblr version="1.0">
  <user default-post-format="html" can-upload-audio="1" can-upload-aiff="1" can-ask-question="1" can-upload-video="1" max-video-bytes-uploaded="26214400" liked-post-count="41"/>
  <tumblelog title="HybridAuth," is-admin="1" posts="10" twitter-enabled="1" draft-count="0" messages-count="0" queue-count="" name="hybridauth" url="http://hybridauth.tumblr.com/" type="public" followers="9" avatar-url="http://assets.tumblr.com/images/default_avatar_128.gif" is-primary="yes" backup-post-limit="30000"/>
</tumblr>
*/

		$this->user->profile->identifier    = @ (string) $xml2array["children"]["tumblelog"][0]["attr"]["url"]; 
		$this->user->profile->displayName  	= @ (string) $xml2array["children"]["tumblelog"][0]["attr"]["name"];
		$this->user->profile->profileURL 	= @ (string) $xml2array["children"]["tumblelog"][0]["attr"]["url"]; 
		$this->user->profile->webSiteURL 	= @ (string) $xml2array["children"]["tumblelog"][0]["attr"]["url"]; 
		$this->user->profile->photoURL   	= @ (string) $xml2array["children"]["tumblelog"][0]["attr"]["avatar-url"]; 

		return $this->user->profile;
 	}

	function xml2array($xml) { 
		$arXML=array(); 
		$arXML['name']=trim($xml->getName()); 
		$arXML['value']=trim((string)$xml); 
		$t=array(); 
		foreach($xml->attributes() as $name => $value){ 
			$t[$name]=trim($value); 
		} 
		$arXML['attr']=$t; 
		$t=array(); 
		foreach($xml->children() as $name => $xmlchild) { 
			$t[$name][]=$this->xml2array($xmlchild); //FIX : For multivalued node 
		} 
		$arXML['children']=$t; 
		return($arXML); 
	} 
}
