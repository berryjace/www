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

// ------------------------------------------------------------------------
//	HybridAuth End Point
// ------------------------------------------------------------------------

	/**
	 * here we implement some needed stuff for OpenID
	 * - policy
	 * - Relying Party Discovery for OpenID
	 *
	 *	# http://blog.nerdbank.net/2008/06/why-yahoo-says-your-openid-site.html
	 *	   You must advertise your XRDS document from your Realm URL
	 *	   Add the following line inside the HEAD tags of your Realm page:
	 *	   <meta http-equiv="X-XRDS-Location" content="http://yourdomain.com/hybridauth_path/or_somthing_like_that/?openid_xrds"/> 
	 *
	 *  # http://developer.yahoo.com/openid/faq.html
	 *	   Yahoo! displays the above warning for Relying Parties which fail to implement Section 13: Discovering OpenID Relying Parties of the 
	 *	   OpenID 2.0 Protocol. Implementing Relying Party Discovery enables Yahoo to verify your site's OpenID Realm when 
	 *	   servicing Authentication Requests from your site. 
	 * 
	 * @author     Zachy <hybridauth@gmail.com>
	 * @see        Section 13: Discovering OpenID Relying Parties of the OpenID 2.0 Protocol
	 * @link       http://wiki.openid.net
	 * @link       http://developer.yahoo.com/openid/faq.html
	 * @link       http://blog.nerdbank.net/2008/06/why-yahoo-says-your-openid-site.html
	 */

// ------------------------------------------------------------------------

	// shall not display any errors here, if something happen just die and RIP
	error_reporting( 0 );
        //die(realpath(dirname(__FILE__).'../../../../').'---');
        ini_set('session.save_path', realpath(dirname(__FILE__).'/../../../').'/tmp');
 	// start a new session (required for Hybridauth)
	session_start();
	// check session
	if( !isset( $_SESSION["HA::CONFIG"] ) ):
		die( "Sorry, this page cannot be accessed directly! Please return to the login page and try again. If you think this is a server error, please contact the webmaster." );
	endif;

	require_once( "Hybrid/Auth.php" );

	# init Hybrid_Auth
	try{
		Hybrid_Auth::initialize( unserialize( $_SESSION["HA::CONFIG"] ) ); 
	}
	catch( Exception $e )
	{ 
		Hybrid_Logger::error( "Endpoint: Error while trying to init Hybrid_Auth" ); 

		die();
	}

	Hybrid_Logger::info( "Enter Hybrid_Auth Endpoint." ); 

	# if openid_policy get argument defined, we return our policy document  
	if( isset( $_REQUEST["hauth_start"] ) || isset( $_REQUEST["hauth_done"] ) )
	{
		Hybrid_Logger::info( "Enter Endpoint" ); 

		# define:endpoint step 3.
		# yeah, why not a switch!
		if( isset( $_REQUEST["hauth_start"] ) && $_REQUEST["hauth_start"] )
		{
			$provider_id = trim( strip_tags( $_REQUEST["hauth_start"] ) );

			# check if page accessed directly
			if( ! Hybrid_Auth::storage()->get( "hauth_session.$provider_id.hauth_endpoint" ) )
			{
				Hybrid_Logger::error( "Endpoint: hauth_endpoint parameter is not defined on hauth_start, halt login process!" );

				die( "Sorry, this page cannot be accessed directly! Please return to the login page and try again. If you think this is a server error, please contact the webmaster." );
			}

			# define:hybrid.endpoint.php step 2.
			$hauth = Hybrid_Auth::setup( $provider_id );

			# if REQUESTed hauth_idprovider is wrong, session not created, or shit happen, etc. 
			if( ! $hauth )
			{
				Hybrid_Logger::error( "Endpoint: Invalide parameter on hauth_start!" );

				Hybrid_Error::setError( "Endpoint: Invalide parameter on hauth_start!" );
				
				die( "Invalide parameter! Please return to the login page and try again. If you think this is a server error, please contact the webmaster." );
			}

			try
			{ 
				Hybrid_Logger::info( "Endpoint: call adapter [{$provider_id}] loginBegin()" );
				
				$hauth->adapter->loginBegin();
			}
			catch( Exception $e )
			{
				Hybrid_Logger::error( "Exception:" . $e->getMessage(), $e );

				Hybrid_Error::setError( $e->getMessage(), $e->getCode(), $e->getTraceAsString(), $e );

				$hauth->returnToCallbackUrl();
			}

			die();
		}

		# define:endpoint step 3.1 and 3.2
		if( isset( $_REQUEST["hauth_done"] ) && $_REQUEST["hauth_done"] ) 
		{
			$provider_id = trim( strip_tags( $_REQUEST["hauth_done"] ) );

			$hauth = Hybrid_Auth::setup( $provider_id );
			
			if( ! $hauth )
			{
				Hybrid_Logger::error( "Endpoint: Invalide parameter on hauth_done!" );

				Hybrid_Error::setError( "Endpoint: Invalide parameter on hauth_done!" );

				$hauth->adapter->setUserUnconnected(); 

				die( "Invalide parameter! Please return to the login page and try again. If you think this is a server error, please contact the webmaster." );
			}

			try
			{
				Hybrid_Logger::info( "Endpoint: call adapter [{$provider_id}] loginFinish() " );
				
				$hauth->adapter->loginFinish(); 
			}
			catch( Exception $e )
			{
				Hybrid_Logger::error( "Exception:" . $e->getMessage(), $e );

				Hybrid_Error::setError( $e->getMessage(), $e->getCode(), $e->getTraceAsString(), $e );

				$hauth->adapter->setUserUnconnected(); 
			}

			unset( $_SESSION["HA::CONFIG"] );
			
			Hybrid_Logger::info( "Endpoint: job done. retrun to callback url." );

			$hauth->returnToCallbackUrl();

			die();
		} 

		die();
	}

	# if windows_live_channel get argument defined, we return our windows_live WRAP_CHANNEL_URL
	if( isset( $_REQUEST["get"] ) && $_REQUEST["get"] == "windows_live_channel" )
	{
		echo file_get_contents( Hybrid_Auth::$config["path_resources"] . "windows_live_channel.html" ); 

		exit( 0 );
	}

	# if openid_policy get argument defined, we return our policy document  
	if( isset( $_REQUEST["get"] ) && $_REQUEST["get"] == "openid_policy")
	{
		echo file_get_contents( Hybrid_Auth::$config["path_resources"] . "openid_policy.html" ); 

		exit( 0 );
	}

	# if openid_xrds get argument defined, we return our XRDS document  
	# for sure you can change "openid_xrds" by any name, just sync it with the X-XRDS-Location tag
	if( isset( $_REQUEST["get"] ) && $_REQUEST["get"] == "openid_xrds" )
	{
		header("Content-Type: application/xrds+xml");

		echo str_replace
			(
				"{RETURN_TO_URL}",
				Hybrid_Auth::$config["base_url"],
				file_get_contents( Hybrid_Auth::$config["path_resources"] . "openid_xrds.xml" )
			); 

		exit( 0 );
	}

	# Else, 
	# We advertise our XRDS document, something supposed to be done from the Realm URL page
	# The Realm URL is $GLOBAL_HYBRID_AUTH_URL_BASE in configuration file
	echo str_replace
		(
			"{X_XRDS_LOCATION}",
			Hybrid_Auth::$config["base_url"] . "?get=openid_xrds",
			file_get_contents( Hybrid_Auth::$config["path_resources"] . "openid_realm.html" )
		);
