<?php
require_once('includes/config.php');
require_once('includes/paypal.class.php');

// PayPal object
$paypal_config = array('Sandbox' => $sandbox, 'APIUsername' => $api_username, 'APIPassword' => $api_password, 'APISignature' => $api_signature);
$paypal = new PayPal($paypal_config);

############[ SESSIONS ]################
$_SESSION['invoice'] = isset($_POST['InvoiceID']) ? $_POST['InvoiceID'] : '';
$_SESSION['transaction_type'] = isset($_POST['TransactionType']) ? $_POST['TransactionType'] : 'Authorization';

$_SESSION['cc_type'] = $_POST['CreditCardType'];
$_SESSION['cc_number'] = $_POST['CreditCardNumber'];
$_SESSION['cc_expdate'] = $_POST['CreditCardExpMo'] . $_POST['CreditCardExpYear'];
$_SESSION['cvv2'] = $_POST['CreditCardSecurityCode'];

// Check to see if they manually enter in billing information
if (isset($_POST['billingInfo']) && $_POST['billingInfo'] == 'true'){
	$_SESSION['billing_first_name'] = $_POST['BillingFirstName'];
	$_SESSION['billing_last_name'] = $_POST['BillingLastName'];
	$_SESSION['billing_street1'] = $_POST['BillingStreet'];
	$_SESSION['billing_street2'] = $_POST['BillingStreet2'];
	$_SESSION['billing_city'] = $_POST['BillingCity'];
	$_SESSION['billing_state'] = $_POST['BillingState'];
	$_SESSION['billing_postal_code'] = $_POST['BillingPostalCode'];
	$_SESSION['billing_country_code'] = $_POST['BillingCountryCode'];
	$_SESSION['billing_country_name'] = $paypal -> GetCountryName($_POST['BillingCountryCode']);
	$_SESSION['billing_phone'] = $_POST['BillingPhoneNumber'];
	$_SESSION['billing_email'] = $_POST['BillingEmail'];
} else {
	$_SESSION['billing_first_name'] = $_POST['BillingFirstName'];
	$_SESSION['billing_last_name'] = $_POST['BillingLastName'];
	$_SESSION['billing_street1'] = '123 N/A';
	$_SESSION['billing_street2'] = '';
	$_SESSION['billing_city'] = 'NA';
	$_SESSION['billing_state'] = 'NA';
	$_SESSION['billing_postal_code'] = '00000';
	$_SESSION['billing_country_code'] = 'US';
	$_SESSION['billing_country_name'] = $paypal -> GetCountryName('US');
	$_SESSION['billing_phone'] = '';
	$_SESSION['billing_email'] = '';
}

// If Shipping is different then billing
if (isset($_POST['shippingInfo']) && $_POST['shippingInfo'] == "true")
{
	$_SESSION['shipping_first_name'] = isset($_POST['ShippingFirstName']) ? $_POST['ShippingFirstName'] : '';
	$_SESSION['shipping_last_name'] = isset($_POST['ShippingLastName']) ? $_POST['ShippingLastName'] : '';
	$_SESSION['shipping_phone'] = isset($_POST['ShippingPhoneNumber']) ? $_POST['ShippingPhoneNumber'] : '';
	$_SESSION['shipping_email'] = isset($_POST['ShippingEmail']) ? $_POST['ShippingEmail'] : '';
	$_SESSION['shipping_street1'] = isset($_POST['ShippingStreet']) ? $_POST['ShippingStreet'] : '';
	$_SESSION['shipping_street2'] = isset($_POST['ShippingStreet2']) ? $_POST['ShippingStreet2'] : '';
	$_SESSION['shipping_city'] = isset($_POST['ShippingCity']) ? $_POST['ShippingCity'] : '';
	$_SESSION['shipping_state'] = isset($_POST['ShippingState']) ? $_POST['ShippingState'] : '';
	$_SESSION['shipping_postal_code'] = isset($_POST['ShippingPostalCode']) ? $_POST['ShippingPostalCode'] : '';
	$_SESSION['shipping_country_code'] = isset($_POST['ShippingCountryCode']) ? $_POST['ShippingCountryCode'] : '';
	$_SESSION['shipping_country_name'] = $paypal -> GetCountryName($_POST['ShippingCountryCode']);
} 
else 
{
	$_SESSION['shipping_first_name'] = isset($_SESSION['billing_first_name']) ? $_SESSION['billing_first_name'] : '';
	$_SESSION['shipping_last_name'] = isset($_SESSION['billing_last_name']) ? $_SESSION['billing_last_name'] : '';
	$_SESSION['shipping_phone'] = isset($_SESSION['billing_phone']) ? $_SESSION['billing_phone'] : '';
	$_SESSION['shipping_email'] = isset($_SESSION['billing_email']) ? $_SESSION['billing_email'] : '';
	$_SESSION['shipping_street1'] = isset($_SESSION['billing_street1']) ? $_SESSION['billing_street1'] : '';
	$_SESSION['shipping_street2'] = isset($_SESSION['billing_street2']) ? $_SESSION['billing_street2'] : '';
	$_SESSION['shipping_city'] = isset($_SESSION['billing_city']) ? $_SESSION['billing_city'] : '';
	$_SESSION['shipping_state'] = isset($_SESSION['billing_state']) ? $_SESSION['billing_state'] : '';
	$_SESSION['shipping_postal_code'] = isset($_SESSION['billing_postal_code']) ? $_SESSION['billing_postal_code'] : '';
	$_SESSION['shipping_country_code'] = isset($_SESSION['billing_country_code']) ? $_SESSION['billing_country_code'] : '';
	$_SESSION['shipping_country_name'] = $paypal -> GetCountryName($_SESSION['billing_country_code']);
}

$_SESSION['amount'] = $_POST['GrandTotal'];
$_SESSION['subtotal'] = $_POST['NetAmount'];
$_SESSION['shipping_amount'] = $_POST['ShippingAmount'];
$_SESSION['handling_amount'] = $_POST['HandlingAmount'];
$_SESSION['tax_amount'] = $_POST['TaxAmount'];


$_SESSION['billingInfo'] = isset($_POST['billingInfo']) ? $_POST['billingInfo'] : array();
$_SESSION['shippingInfo'] = isset($_POST['shippingInfo']) ? $_POST['shippingInfo'] : array();
##########[ Direct Payment ]############

// Create new PayPal object
$DPFields = array(
					'paymentaction' => $_SESSION['transaction_type'], 						// How you want to obtain payment.  Authorization indidicates the payment is a basic auth subject to settlement with Auth & Capture.  Sale indicates that this is a final sale for which you are requesting payment.  Default is Sale.
					'ipaddress' => $_SERVER['REMOTE_ADDR'], 		// Required.  IP address of the payer's browser.
					'returnfmfdetails' => '1' 						// Flag to determine whether you want the results returned by FMF.  1 or 0.  Default is 0.
				);
				
$CCDetails = array(
					'creditcardtype' => $_SESSION['cc_type'], 						// Required. Type of credit card.  Visa, MasterCard, Discover, Amex, Maestro, Solo.  If Maestro or Solo, the currency code must be GBP.  In addition, either start date or issue number must be specified.
					'acct' => $_SESSION['cc_number'], 								// Required.  Credit card number.  No spaces or punctuation.  
					'expdate' => $_SESSION['cc_expdate'], 		// Required.  Credit card expiration date.  Format is MMYYYY
					'cvv2' => $_SESSION['cvv2'] 									// Requirements determined by your PayPal account settings.  Security digits for credit card.
				);
				
$PayerInfo = array(
					'email' => $_SESSION['billing_email'], 				// Email address of payer.
					'business' => '' 							// Payer's business name.
				);
				
$PayerName = array(
					'firstname' => $_SESSION['billing_first_name'], 		// Payer's first name.  25 char max.
					'lastname' => $_SESSION['billing_last_name']			// Payer's last name.  25 char max.
				);
				
$BillingAddress = array(
					'street' => $_SESSION['billing_street1'], 						// Required.  First street address.
					'street2' => $_SESSION['billing_street2'], 					// Second street address.
					'city' => $_SESSION['billing_city'], 							// Required.  Name of City.
					'state' => $_SESSION['billing_state'], 						// Required. Name of State or Province.
					'countrycode' => $_SESSION['billing_country_code'], 				// Required.  Country code.
					'zip' => $_SESSION['billing_postal_code'], 						// Required.  Postal code of payer.
					'phonenum' => $_SESSION['billing_phone'] 						// Phone Number of payer.  20 char max.
					);

if(isset($_POST['shippingDisabled']) && $_POST['shippingDisabled'] == true)
{
	$ShippingAddress = array();	
}
else
{
	$ShippingAddress = array(
					'shiptoname' => $_SESSION['shipping_first_name']." ".$_SESSION['shipping_last_name'], 							// Required if shipping is included.  Person's name associated with this address.  32 char max.
					'shiptostreet' => $_SESSION['shipping_street1'], 					// Required if shipping is included.  First street address.  100 char max.
					'shiptostreet2' => $_SESSION['shipping_street2'], 					// Second street address.  100 char max.
					'shiptocity' => $_SESSION['shipping_city'], 							// Required if shipping is included.  Name of city.  40 char max.
					'shiptostate' => $_SESSION['shipping_state'], 			// Required if shipping is included.  Name of state or province.  40 char max.
					'shiptozip' => $_SESSION['shipping_postal_code'], 				// Required if shipping is included.  Postal code of shipping address.  20 char max.
					'shiptocountrycode' => $_SESSION['shipping_country_code'], 		// Required if shipping is included.  Country code of shipping address.  2 char max.
					'shiptophonenum' => $_SESSION['shipping_phone']								// Phone number for shipping address.  20 char max.
						);	
}
						
$PaymentDetails = array(
					'amt' => $_SESSION['amount'], 							// Required. Total amount of the order, including shipping, handling, and tax.
					'currencycode' => 'USD', 								// A three-character currency code.  Default is USD.
					'itemamt' => $_SESSION['subtotal'], 		// Required if you specify itemized L_AMT fields. Sum of cost of all items in this order.  
					'shippingamt' => $_SESSION['shipping_amount'], 				// Total shipping costs for this order.  If you specify SHIPPINGAMT you mut also specify a value for ITEMAMT.
					'handlingamt' => $_SESSION['handling_amount'], 									// Total handling costs for this order.  If you specify HANDLINGAMT you mut also specify a value for ITEMAMT.
					'taxamt' => $_SESSION['tax_amount'], 							// Required if you specify itemized L_TAXAMT fields.  Sum of all tax items in this order. 
					'desc' => 'PayPal Payments Pro Virtual Terminal Sale', 						// Description of items on the order.  127 char max.
					'custom' => '', 										// Free-form field for your own use.  256 char max.
					'invnum' => $_SESSION['invoice'], 										// Your own invoice or tracking number.  127 char max.
					'buttonsource' => '', 									// ID code for use by third-party apps to identify transactions in PayPal. 
					'notifyurl' => '' 										// URL for receiving Instant Payment Notifications
					);

$DPData = array(
					'DPFields' => $DPFields, 
					'CCDetails' => $CCDetails, 
					'PayerInfo' => $PayerInfo, 
					'PayerName' => $PayerName, 
					'BillingAddress' => $BillingAddress, 
					'ShippingAddress' => $ShippingAddress, 
					'PaymentDetails' => $PaymentDetails 
				);

$_SESSION['DPResult'] = $paypal -> DoDirectPayment($DPData);
if(strtoupper($_SESSION['DPResult']['ACK']) == 'FAILURE' || strtoupper($_SESSION['DPResult']['ACK']) == 'FAILUREWITHWARNING')
{
	$_SESSION['paypal_errors'] = $_SESSION['DPResult']['ERRORS'];
	header('Location: results.php?result=ERROR');
	exit();
}

$_SESSION['transaction_id'] = isset($_SESSION['DPResult']['TRANSACTIONID']) ? $_SESSION['DPResult']['TRANSACTIONID'] : '';
$_SESSION['avscode'] = isset($_SESSION['DPResult']['AVSCODE']) ? $_SESSION['DPResult']['AVSCODE'] : '';
$_SESSION['cvv2match'] = isset($_SESSION['DPResult']['CVV2MATCH']) ? $_SESSION['DPResult']['CVV2MATCH'] : '';

header('Location: results.php?result=SUCCESS');
?>