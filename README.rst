###################
Introduction
###################

This is a bare-bones, but functional template to use as a web-based point-of-sale solution for PayPal Payments Pro.

Using this instead of the PayPal provided Virtual Terminal will save you money on transaction fees (PayPal charges a higher rate for VT than they do for Pro transactions) and it also provides a simple swipe functionality that works with USBSwiper credit card readers (www.usbswiper.com)

*******************
Server Requirements
*******************

-  PHP version 5.2.4 or newer.

************
Installation
************

Copy all solution files to a directory on your web server (eg. www.domain.com/pos/)

Open /includes/config-sample.php and adjust the following with your own values.  Then save-as config.php.

	- date_default_timezone_set()	
	- $sandbox
	- $domain
	- $api_username
	- $api_password
	- $api_signature

	- Sandbox API credentials can be obtained from your developer account
	  at http://developer.paypal.com

	- Live API credentials can be obtained from your live PayPal account profile, 
	  or you may also use this tool provided by PayPal:
 
	  https://www.paypal.com/us/cgi-bin/webscr?cmd=_get-api-signature&generic-flow=true


You may `contact me directly <http://www.angelleye.com/contact-us/>`_ if you need additional help getting started.