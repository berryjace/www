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
//	HybridAuth Config
// ------------------------------------------------------------------------

/**
 * - "base_url" is the url to HybridAuth EndPoint 'index.php'
 * - "providers" is the list of providers supported by HybridAuth
 * - "enabled" can be true or false; if you dont want to use a specific provider then set it to 'false'
 * - "keys" are your application credentials for this provider 
 * 		for example :
 *     		'id' is your facebook application id
 *     		'key' is your twitter application consumer key
 *     		'secret' is your twitter application consumer secret 
 * - To enable Logging, set debug_mode to true, then provide a path of a writable file on debug_file
 *  
 * Note: The HybridAuth Config file is not required, to know more please visit:
 *       http://hybridauth.sourceforge.net/userguide/Configuration.html
 */
return
        array(
            "base_url" => "http://" . $_SERVER['HTTP_HOST'] . "/dayplanit/library/ThirdParty/hybridauth/",
            "providers" => array(
                // google
                "Google" => array(
                    "enabled" => true
                ),
                // yahoo
                "Yahoo" => array(
                    "enabled" => true
                ),
                // facebook
                "Facebook" => array(// 'id' is your facebook application id
                    "enabled" => true,
                    "keys" => array("id" => "139701912741289", "secret" => "a26ef26dc10234e1ce6cc4d49c097e87")
                ),
                // myspace
                "MySpace" => array(// 'key' is your twitter application consumer key
                    "enabled" => false,
                    "keys" => array("key" => "", "secret" => "")
                ),
                // twitter
                "Twitter" => array(
                    "enabled" => true,
                    "keys" => array("key" => "Fr5qLg6epLJILGzhy0Gbw", "secret" => "gVHIbciv7cd5RrJFOqNma15uKu9vXbalv7EwcI")
                ),
                // windows live
                "Live" => array(
                    "enabled" => false,
                    "keys" => array("id" => "", "secret" => "")
                ),
                // linkedin
                "LinkedIn" => array(
                    "enabled" => false,
                    "keys" => array("key" => "", "secret" => "")
                ),
                // tumblr
                "Tumblr" => array(
                    "enabled" => false,
                    "keys" => array("key" => "", "secret" => "")
                ),
                // vimeo
                "Vimeo" => array(
                    "enabled" => false,
                    "keys" => array("key" => "", "secret" => "")
                ),
                // foursquare
                "Foursquare" => array(
                    "enabled" => false,
                    "keys" => array("key" => "", "secret" => "")
                ),
                // gowalla
                "Gowalla" => array(
                    "enabled" => false,
                    "keys" => array("key" => "", "secret" => "")
                ),
                // PayPal
                "PayPal" => array(
                    "enabled" => false,
                ),
            ),
            "debug_mode" => false,
            // if you want to enable logging, set 'debug_mode' to true  then provide here a writable file by the web server 
            "debug_file" => "",
);
