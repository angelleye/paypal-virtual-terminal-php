<?php
/**
 * Timezone Setting
 */
date_default_timezone_set('America/Chicago');

/** 
 * Sandbox Mode - TRUE/FALSE
 * Be sure to set $sandbox to FALSE when you go live!
 *
 * The $domain variable here is used throughout the app, so make sure you have
 * both your test server (sandbox) and live server values setup here.
 */
$sandbox = TRUE;
$domain = $sandbox ? 'http://sandbox.domain.com/paypal-pos/' : 'http://www.domain.com/paypal-pos/';

/**
 * API Credentials
 * 
 * You need to make sure and fill in your PayPal API credentials here
 * for both the sandbox and the live servers.
 * 
 * Your sandbox credentials are available within your PayPal developer account (http://developer.paypal.com)
 * Your live credentials are available in your PayPal account profile, or you can use this tool:  
 * https://www.paypal.com/us/cgi-bin/webscr?cmd=_get-api-signature&generic-flow=true
 */
$api_version = '85.0';
$api_username = $sandbox ? 'SANDBOX_API_USERNAME' : 'LIVE_API_USERNAME';
$api_password = $sandbox ? 'SANDBOX_API_PASSWORD' : 'LIVE_API_PASSWORD';
$api_signature = $sandbox ? 'SANDBOX_API_SIGNATURE' : 'LIVE_API_SIGNATURE';

/**
  * Enable Sessions
  */
if(!session_id()) session_start();

/**
 * Enable error reporting if running in sandbox mode.
 */
if($sandbox)
{
	error_reporting(E_ALL);
	ini_set('display_errors', '1');	
}
?>