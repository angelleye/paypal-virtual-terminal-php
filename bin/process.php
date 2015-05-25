<?php
/**
 * Process Payments Script
 */

/**
 * Include PayPal config file
 */
require_once('../includes/config.php');
require_once('../vendor/autoload.php');

if(isset($config['ApiSelection']) && (strtolower($config['ApiSelection']) == 'paypalpro-ddp'))
{
    /**
     * PayPalPro API
     */
    // PayPal object
    $paypal_config = array('Sandbox' => $config['Sandbox'], 'APIUsername' => $config['APIUsername'], 'APIPassword' => $config['APIPassword'], 'APISignature' => $config['APISignature']);
    $paypal = new \angelleye\PayPal\PayPal($paypal_config);

    ############[ SESSIONS ]################
    $_SESSION['invoice'] = isset($_POST['InvoiceID']) ? $_POST['InvoiceID'] : '';
    $_SESSION['notes'] = isset($_POST['Notes']) ? $_POST['Notes'] : '';
    $_SESSION['item_name'] = isset($_POST['ItemName']) ? $_POST['ItemName'] : '';
    $_SESSION['transaction_type'] = isset($_POST['TransactionType']) ? $_POST['TransactionType'] : 'Authorization';
    $_SESSION['cc_type'] = isset($_POST['CreditCardType']) ? $_POST['CreditCardType'] : '';
    $_SESSION['cc_number'] = isset($_POST['CreditCardNumber']) ? $_POST['CreditCardNumber'] : '';
    $_SESSION['cc_expdate'] = isset($_POST['CreditCardExpMo']) ?  $_POST['CreditCardExpMo'] . $_POST['CreditCardExpYear'] : '';
    $_SESSION['cvv2'] = isset($_POST['CreditCardSecurityCode']) ? $_POST['CreditCardSecurityCode'] : '';

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
    if (!isset($_POST['shippingDisabled']) && !isset($_POST['shippingSameAsBilling']))
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
        'paymentaction' => $_SESSION['transaction_type'],                        // How you want to obtain payment.  Authorization indidicates the payment is a basic auth subject to settlement with Auth & Capture.  Sale indicates that this is a final sale for which you are requesting payment.  Default is Sale.
        'ipaddress' => $_SERVER['REMOTE_ADDR'],        // Required.  IP address of the payer's browser.
        'returnfmfdetails' => '1'                        // Flag to determine whether you want the results returned by FMF.  1 or 0.  Default is 0.
    );

    $CCDetails = array(
        'creditcardtype' => $_SESSION['cc_type'],                        // Required. Type of credit card.  Visa, MasterCard, Discover, Amex, Maestro, Solo.  If Maestro or Solo, the currency code must be GBP.  In addition, either start date or issue number must be specified.
        'acct' => $_SESSION['cc_number'],                                // Required.  Credit card number.  No spaces or punctuation.
        'expdate' => $_SESSION['cc_expdate'],        // Required.  Credit card expiration date.  Format is MMYYYY
        'cvv2' => $_SESSION['cvv2']                                    // Requirements determined by your PayPal account settings.  Security digits for credit card.
    );

    $PayerInfo = array(
        'email' => $_SESSION['billing_email'],                // Email address of payer.
        'business' => ''                            // Payer's business name.
    );

    $PayerName = array(
        'firstname' => $_SESSION['billing_first_name'],        // Payer's first name.  25 char max.
        'lastname' => $_SESSION['billing_last_name']            // Payer's last name.  25 char max.
    );

    $BillingAddress = array(
        'street' => $_SESSION['billing_street1'],                        // Required.  First street address.
        'street2' => $_SESSION['billing_street2'],                    // Second street address.
        'city' => $_SESSION['billing_city'],                            // Required.  Name of City.
        'state' => $_SESSION['billing_state'],                        // Required. Name of State or Province.
        'countrycode' => $_SESSION['billing_country_code'],                // Required.  Country code.
        'zip' => $_SESSION['billing_postal_code'],                        // Required.  Postal code of payer.
        'phonenum' => $_SESSION['billing_phone']                        // Phone Number of payer.  20 char max.
    );

    if (isset($_POST['shippingDisabled']) && $_POST['shippingDisabled'] == true) {
        $ShippingAddress = array();
    } else {
        $ShippingAddress = array(
            'shiptoname' => $_SESSION['shipping_first_name'] . " " . $_SESSION['shipping_last_name'],                            // Required if shipping is included.  Person's name associated with this address.  32 char max.
            'shiptostreet' => $_SESSION['shipping_street1'],                    // Required if shipping is included.  First street address.  100 char max.
            'shiptostreet2' => $_SESSION['shipping_street2'],                    // Second street address.  100 char max.
            'shiptocity' => $_SESSION['shipping_city'],                            // Required if shipping is included.  Name of city.  40 char max.
            'shiptostate' => $_SESSION['shipping_state'],            // Required if shipping is included.  Name of state or province.  40 char max.
            'shiptozip' => $_SESSION['shipping_postal_code'],                // Required if shipping is included.  Postal code of shipping address.  20 char max.
            'shiptocountrycode' => $_SESSION['shipping_country_code'],        // Required if shipping is included.  Country code of shipping address.  2 char max.
            'shiptophonenum' => $_SESSION['shipping_phone']                                // Phone number for shipping address.  20 char max.
        );
    }

    $PaymentDetails = array(
        'amt' => $_SESSION['amount'],                            // Required. Total amount of the order, including shipping, handling, and tax.
        'currencycode' => $config['CurrencyCode'],                                // A three-character currency code.  Default is USD.
        'itemamt' => $_SESSION['subtotal'],        // Required if you specify itemized L_AMT fields. Sum of cost of all items in this order.
        'shippingamt' => $_SESSION['shipping_amount'],                // Total shipping costs for this order.  If you specify SHIPPINGAMT you mut also specify a value for ITEMAMT.
        'handlingamt' => $_SESSION['handling_amount'],                                    // Total handling costs for this order.  If you specify HANDLINGAMT you mut also specify a value for ITEMAMT.
        'taxamt' => $_SESSION['tax_amount'],                            // Required if you specify itemized L_TAXAMT fields.  Sum of all tax items in this order.
        'desc' => 'PayPal Payments Pro Virtual Terminal Sale',                        // Description of items on the order.  127 char max.
        'custom' => '',                                        // Free-form field for your own use.  256 char max.
        'invnum' => $_SESSION['invoice'],                                        // Your own invoice or tracking number.  127 char max.
        'buttonsource' => '',                                    // ID code for use by third-party apps to identify transactions in PayPal.
        'notifyurl' => ''                                        // URL for receiving Instant Payment Notifications
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

    $_SESSION['DPResult'] = $paypal->DoDirectPayment($DPData);

    // Write to transaction log
    if(isset($config['LogEnabled']) && $config['LogEnabled'])
    {
        logTransaction($_SESSION['DPResult'], $config['LogFilePath']);
    }

    if (strtoupper($_SESSION['DPResult']['ACK']) == 'FAILURE' || strtoupper($_SESSION['DPResult']['ACK']) == 'FAILUREWITHWARNING') {
        $_SESSION['paypal_errors'] = $_SESSION['DPResult']['ERRORS'];
        $PayPalErrors = $_SESSION['paypal_errors'];

        $result_data_html = '<ul>';
        foreach ($PayPalErrors as $error) {
            foreach ($error as $k => $v) {
                $result_data_html .= '<li><strong>' . $k . '</strong>&nbsp;' . $v . '</li>';
            }
        }
        $result_data_html .= '</ul>';

        echo json_encode(array('result' => 'error', 'result_data' => $result_data_html));
        exit;
    }

    $_SESSION['transaction_id'] = isset($_SESSION['DPResult']['TRANSACTIONID']) ? $_SESSION['DPResult']['TRANSACTIONID'] : '';
    $_SESSION['avscode'] = isset($_SESSION['DPResult']['AVSCODE']) ? $_SESSION['DPResult']['AVSCODE'] : '';
    $_SESSION['cvv2match'] = isset($_SESSION['DPResult']['CVV2MATCH']) ? $_SESSION['DPResult']['CVV2MATCH'] : '';
    $_SESSION['timestamp'] = isset($_SESSION['DPResult']['TIMESTAMP']) ? $_SESSION['DPResult']['TIMESTAMP'] : '';
    $_SESSION['currency_code'] = isset($_SESSION['DPResult']['CURRENCYCODE']) ? $_SESSION['DPResult']['CURRENCYCODE'] : '';

    $returnData = array();
    $returnData['payment_details'] = array(
        'Transaction_ID' => $_SESSION['transaction_id'],
        'Timestamp' => $_SESSION['timestamp'],
        'AVS_Code' => $_SESSION['avscode'],
        'CVV2_Match' => $_SESSION['cvv2match'],
        'Currency_Code' => $_SESSION['currency_code'],
        'amount' => $_SESSION['amount'],
        'shipping_amount' => $_SESSION['shipping_amount'],
        'handling_amount' => $_SESSION['handling_amount'],
        'tax_amount' => $_SESSION['tax_amount'],
        'transaction_type' => $_SESSION['transaction_type'],
        'card_type' => $_SESSION['cc_type'],
        'card_number' => substr($_SESSION['cc_number'], 0, 4) . str_repeat("X", strlen($_SESSION['cc_number']) - 8) . substr($_SESSION['cc_number'], -4),
        'card_expiration' => $_SESSION['cc_expdate'],
        'invoice' => $_SESSION['invoice'],
        'item_name' => $_SESSION['item_name'],
        'notes' => $_SESSION['notes']
    );
    $returnData['billing_info'] = array(
        'first_name' => $_SESSION['billing_first_name'],
        'last_name' => $_SESSION['billing_last_name'],
        'street_1' => $_SESSION['billing_street1'],
        'street_2' => $_SESSION['billing_street2'],
        'city' => $_SESSION['billing_city'],
        'state' => $_SESSION['billing_state'],
        'postal_code' => $_SESSION['billing_postal_code'],
        'country_code' => $_SESSION['billing_country_code'],
        'phone' => $_SESSION['billing_phone'],
        'email' => $_SESSION['billing_email'],
    );
    $returnData['shipping_info'] = array(
        'first_name' => $_SESSION['shipping_first_name'],
        'last_name' => $_SESSION['shipping_last_name'],
        'street_1' => $_SESSION['shipping_street1'],
        'street_2' => $_SESSION['shipping_street2'],
        'city' => $_SESSION['shipping_city'],
        'state' => $_SESSION['shipping_state'],
        'postal_code' => $_SESSION['shipping_postal_code'],
        'country_code' => $_SESSION['shipping_country_code'],
        'phone' => $_SESSION['shipping_phone'],
        'email' => $_SESSION['shipping_email'],
    );

    $returnHtml = '';
    $returnHtml .= '<div class="well">';
    $returnHtml .= '<div class="row" id="pos-panel-success-details">';
    $returnHtml .= '<div class="col-lg-4"><h4>Payment Details</h4>';
    foreach ($returnData['payment_details'] as $k => $v) {
        if(!empty($v))
        {
            $returnHtml .= '<li><strong>' . ucfirst(str_replace("_", " ", $k)) . '</strong>:&nbsp;' . str_replace("_", " ", $v) . '</li>';
        }
    }
    $returnHtml .= '</div>';
    $returnHtml .= '<div class="col-lg-4"><h4>Billing Info</h4>';
    foreach ($returnData['billing_info'] as $k => $v) {
        if(!empty($v))
        {
            $returnHtml .= '<li><strong>' . ucfirst(str_replace("_", " ", $k)) . '</strong>:&nbsp;' . str_replace("_", " ", $v) . '</li>';
        }
    }
    $returnHtml .= '</div>';
    if(!empty($returnData['shipping_info']['first_name']))
    {
        $returnHtml .= '<div class="col-lg-4"><h4>Shipping Info</h4>';
        foreach ($returnData['shipping_info'] as $k => $v) {
            if(!empty($v))
            {
                $returnHtml .= '<li><strong>' . ucfirst(str_replace("_", " ", $k)) . '</strong>:&nbsp;' . str_replace("_", " ", $v) . '</li>';
            }
        }
        $returnHtml .= '</div>';
    }
    $returnHtml .= '</div>';
    $returnHtml .= '</div>';

    // DebugMode output
    if(isset($config['DebugMode']) && $config['DebugMode'])
    {
        $returnHtml .= '<hr>';
        $returnHtml .= '<pre>'.print_r($_SESSION['DPResult'], TRUE).'</pre>';
    }

    echo json_encode(array('result' => 'success', 'result_data' => $returnData, 'result_html' => $returnHtml));
    exit;
}
elseif(isset($config['ApiSelection']) && (strtolower($config['ApiSelection']) == 'paypalpro-payflow'))
{
    /**
     * PayFlow API
     */
    // PayPal object
    $paypal_config = array(
        'Sandbox' => $config['Sandbox'],
        'APIUsername' => $config['PayFlowUsername'],
        'APIPassword' => $config['PayFlowPassword'],
        'APIVendor' => $config['PayFlowVendor'],
        'APIPartner' => $config['PayFlowPartner']
    );
    $paypal = new \angelleye\PayPal\PayFlow($paypal_config);

    ############[ SESSIONS ]################
    $_SESSION['invoice'] = isset($_POST['InvoiceID']) ? $_POST['InvoiceID'] : '';
    $_SESSION['notes'] = isset($_POST['Notes']) ? $_POST['Notes'] : '';
    $_SESSION['item_name'] = isset($_POST['ItemName']) ? $_POST['ItemName'] : '';
    $_SESSION['transaction_type'] = isset($_POST['TransactionType']) ? $_POST['TransactionType'] : 'Authorization';
    $_SESSION['cc_type'] = isset($_POST['CreditCardType']) ? $_POST['CreditCardType'] : '';
    $_SESSION['cc_number'] = isset($_POST['CreditCardNumber']) ? $_POST['CreditCardNumber'] : '';
    $_SESSION['cc_expdate'] = isset($_POST['CreditCardExpMo']) ?  $_POST['CreditCardExpMo']. substr($_POST['CreditCardExpYear'], 2, 2) : '';
    $_SESSION['cvv2'] = isset($_POST['CreditCardSecurityCode']) ? $_POST['CreditCardSecurityCode'] : '';

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
    if (!isset($_POST['shippingDisabled']) && !isset($_POST['shippingSameAsBilling']))
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

    ##########[ Process Transaction ]############
    // Create new PayPal object
    $PayPalRequestData = array(
        'tender'=>'C', 				                // Required.  The method of payment.  Values are: A = ACH, C = Credit Card, D = Pinless Debit, K = Telecheck, P = PayPal
        'trxtype'=> ($_SESSION['transaction_type'] == 'Sale') ? 'S' : 'A', 				            // Required.  Indicates the type of transaction to perform.  Values are:  A = Authorization, B = Balance Inquiry, C = Credit, D = Delayed Capture, F = Voice Authorization, I = Inquiry, L = Data Upload, N = Duplicate Transaction, S = Sale, V = Void
        'acct'=> $_SESSION['cc_number'], 				// Required for credit card transaction.  Credit card or purchase card number.
        'expdate'=> $_SESSION['cc_expdate'], 				            // Required for credit card transaction.  Expiration date of the credit card.  Format:  MMYY
        'amt'=> $_SESSION['amount'], 					        // Required.  Amount of the transaction.  Must have 2 decimal places.
        'dutyamt'=>'', 				                //
        'freightamt'=> $_SESSION['shipping_amount'], 			            //
        'taxamt'=> $_SESSION['tax_amount'], 				            //
        'taxexempt'=>'', 			                //
        'comment1'=> $_SESSION['notes'], 			    // Merchant-defined value for reporting and auditing purposes.  128 char max
        'comment2'=>'', 		// Merchant-defined value for reporting and auditing purposes.  128 char max
        'cvv2'=> $_SESSION['cvv2'], 				                // A code printed on the back of the card (or front for Amex)
        'recurring'=> '', 			                // Identifies the transaction as recurring.  One of the following values:  Y = transaction is recurring, N = transaction is not recurring.
        'swipe'=> '', 				                // Required for card-present transactions.  Used to pass either Track 1 or Track 2, but not both.
        'orderid'=> '', 				                // Checks for duplicate order.  If you pass orderid in a request and pass it again in the future the response returns DUPLICATE=2 along with the orderid
        'billtoemail'=> $_SESSION['billing_email'], 	// Account holder's email address.
        'billtophonenum'=> $_SESSION['billing_phone'], 		    // Account holder's phone number.
        'billtofirstname'=> $_SESSION['billing_first_name'], 		        // Account holder's first name.
        'billtomiddlename'=> '', 	                // Account holder's middle name.
        'billtolastname'=> $_SESSION['billing_last_name'], 		        // Account holder's last name.
        'billtostreet'=> $_SESSION['billing_street1']. ' ' .$_SESSION['billing_street2'], 		    // The cardholder's street address (number and street name).  150 char max
        'billtocity'=> $_SESSION['billing_city'], 			    // Bill to city.  45 char max
        'billtostate'=> $_SESSION['billing_state'], 			            // Bill to state.
        'billtozip'=> $_SESSION['billing_postal_code'], 			            // Account holder's 5 to 9 digit postal code.  9 char max.  No dashes, spaces, or non-numeric characters
        'billtocountry'=> $_SESSION['billing_country_code'], 		                // Bill to Country Code.
        'shiptofirstname'=> $_SESSION['shipping_first_name'], 		        // Ship to first name.  30 char max
        'shiptomiddlename'=> '', 	                // Ship to middle name. 30 char max
        'shiptolastname'=> $_SESSION['shipping_last_name'], 		        // Ship to last name.  30 char max
        'shiptostreet'=> $_SESSION['shipping_street1'], 		    // Ship to street address.  150 char max
        'shiptocity'=> $_SESSION['shipping_city'], 				// Ship to city.
        'shiptostate'=> $_SESSION['shipping_state'], 			            // Ship to state.
        'shiptozip'=> $_SESSION['shipping_postal_code'], 			            // Ship to postal code.  10 char max
        'shiptocountry'=> $_SESSION['shipping_country_code'], 		                // Ship to country code.
        'origid'=>'', 				                // Required by some transaction types.  ID of the original transaction referenced.  The PNREF parameter returns this ID, and it appears as the Transaction ID in PayPal Manager reports.
        'custref'=>'', 				                //
        'custcode'=>'', 			                //
        'custip'=>'', 				                //
        'invnum'=> '', 				                //
        'ponum'=>'', 				                //
        'starttime'=>'', 			                // For inquiry transaction when using CUSTREF to specify the transaction.
        'endtime'=>'', 				                // For inquiry transaction when using CUSTREF to specify the transaction.
        'securetoken'=>'', 			                // Required if using secure tokens.  A value the Payflow server created upon your request for storing transaction data.  32 char
        'partialauth'=>'', 			                // Required for partial authorizations.  Set to Y to submit a partial auth.
        'authcode'=>'' 			                    // Required for voice authorizations.  Returned only for approved voice authorization transactions.  AUTHCODE is the approval code received over the phone from the processing network.  6 char max
    );

    $_SESSION['PayPayResult'] = $paypal->ProcessTransaction($PayPalRequestData);

    // Write to transaction log
    if(isset($config['LogEnabled']) && $config['LogEnabled'])
    {
        logTransaction($_SESSION['PayPayResult'], $config['LogFilePath']);
    }

    if(isset($_SESSION['PayPayResult']['RESULT']) && ($_SESSION['PayPayResult']['RESULT'] != 0))
    {
        $_SESSION['paypal_errors'] = $_SESSION['PayPayResult']['RESPMSG'];
        $PayPalErrors = $_SESSION['paypal_errors'];

        $result_data_html = '<ul>';
        $result_data_html .= '<li><strong>ERROR</strong>&nbsp;' . $PayPalErrors . '</li>';
        $result_data_html .= '</ul>';

        echo json_encode(array('result' => 'error', 'result_data' => $result_data_html));
        exit;
    }

    $_SESSION['PNREF'] = isset($_SESSION['PayPayResult']['PNREF']) ? $_SESSION['PayPayResult']['PNREF'] : '';
    $_SESSION['RESPMSG'] = isset($_SESSION['PayPayResult']['RESPMSG']) ? $_SESSION['PayPayResult']['RESPMSG'] : '';
    $_SESSION['AUTHCODE'] = isset($_SESSION['PayPayResult']['AUTHCODE']) ? $_SESSION['PayPayResult']['AUTHCODE'] : '';
    $_SESSION['AVSADDR'] = isset($_SESSION['PayPayResult']['AVSADDR']) ? $_SESSION['PayPayResult']['AVSADDR'] : '';
    $_SESSION['AVSZIP'] = isset($_SESSION['PayPayResult']['AVSZIP']) ? $_SESSION['PayPayResult']['AVSZIP'] : '';
    $_SESSION['CVV2MATCH'] = isset($_SESSION['PayPayResult']['CVV2MATCH']) ? $_SESSION['PayPayResult']['CVV2MATCH'] : '';
    $_SESSION['PROCAVS'] = isset($_SESSION['PayPayResult']['PROCAVS']) ? $_SESSION['PayPayResult']['PROCAVS'] : '';
    $_SESSION['PROCCVV2'] = isset($_SESSION['PayPayResult']['PROCCVV2']) ? $_SESSION['PayPayResult']['PROCCVV2'] : '';
    $_SESSION['TRANSTIME'] = isset($_SESSION['PayPayResult']['TRANSTIME']) ? $_SESSION['PayPayResult']['TRANSTIME'] : '';
    $_SESSION['BILLTOFIRSTNAME'] = isset($_SESSION['PayPayResult']['BILLTOFIRSTNAME']) ? $_SESSION['PayPayResult']['BILLTOFIRSTNAME'] : '';
    $_SESSION['BILLTOLASTNAME'] = isset($_SESSION['PayPayResult']['BILLTOLASTNAME']) ? $_SESSION['PayPayResult']['BILLTOLASTNAME'] : '';
    $_SESSION['AMT'] = isset($_SESSION['PayPayResult']['AMT']) ? $_SESSION['PayPayResult']['AMT'] : '';
    $_SESSION['ACCT'] = isset($_SESSION['PayPayResult']['ACCT']) ? $_SESSION['PayPayResult']['ACCT'] : '';
    $_SESSION['EXPDATE'] = isset($_SESSION['PayPayResult']['EXPDATE']) ? $_SESSION['PayPayResult']['EXPDATE'] : '';
    $_SESSION['CARDTYPE'] = isset($_SESSION['PayPayResult']['CARDTYPE']) ? $_SESSION['PayPayResult']['CARDTYPE'] : '';
    $_SESSION['IAVS'] = isset($_SESSION['PayPayResult']['IAVS']) ? $_SESSION['PayPayResult']['IAVS'] : '';

    $returnData = array();
    $returnData['payment_details'] = array(
        'PNREF' => $_SESSION['PNREF'],
        'RESPMSG' => $_SESSION['RESPMSG'],
        'AUTHCODE' => $_SESSION['AUTHCODE'],
        'AVSADDR' => $_SESSION['AVSADDR'],
        'AVSZIP' => $_SESSION['AVSZIP'],
        'CVV2MATCH' => $_SESSION['CVV2MATCH'],
        'PROCAVS' => $_SESSION['PROCAVS'],
        'PROCCVV2' => $_SESSION['PROCCVV2'],
        'TRANSTIME' => $_SESSION['TRANSTIME'],
        'IAVS' => $_SESSION['IAVS'],
        'amount' => $_SESSION['amount'],
        'shipping_amount' => $_SESSION['shipping_amount'],
        'handling_amount' => $_SESSION['handling_amount'],
        'tax_amount' => $_SESSION['tax_amount'],
        'transaction_type' => $_SESSION['transaction_type'],
        'card_type' => $_SESSION['cc_type'],
        'card_number' => substr($_SESSION['cc_number'], 0, 4) . str_repeat("X", strlen($_SESSION['cc_number']) - 8) . substr($_SESSION['cc_number'], -4),
        'card_expiration' => $_SESSION['EXPDATE'],
        'invoice' => $_SESSION['invoice'],
        'item_name' => $_SESSION['item_name'],
        'notes' => $_SESSION['notes']
    );
    $returnData['billing_info'] = array(
        'first_name' => $_SESSION['billing_first_name'],
        'last_name' => $_SESSION['billing_last_name'],
        'street_1' => $_SESSION['billing_street1'],
        'street_2' => $_SESSION['billing_street2'],
        'city' => $_SESSION['billing_city'],
        'state' => $_SESSION['billing_state'],
        'postal_code' => $_SESSION['billing_postal_code'],
        'country_code' => $_SESSION['billing_country_code'],
        'phone' => $_SESSION['billing_phone'],
        'email' => $_SESSION['billing_email'],
    );
    $returnData['shipping_info'] = array(
        'first_name' => $_SESSION['shipping_first_name'],
        'last_name' => $_SESSION['shipping_last_name'],
        'street_1' => $_SESSION['shipping_street1'],
        'street_2' => $_SESSION['shipping_street2'],
        'city' => $_SESSION['shipping_city'],
        'state' => $_SESSION['shipping_state'],
        'postal_code' => $_SESSION['shipping_postal_code'],
        'country_code' => $_SESSION['shipping_country_code'],
        'phone' => $_SESSION['shipping_phone'],
        'email' => $_SESSION['shipping_email'],
    );

    $returnHtml = '';
    $returnHtml .= '<div class="well">';
    $returnHtml .= '<div class="row" id="pos-panel-success-details">';
    $returnHtml .= '<div class="col-lg-4"><h4>Payment Details</h4>';
    foreach ($returnData['payment_details'] as $k => $v) {
        if(!empty($v))
        {
            $returnHtml .= '<li><strong>' . ucfirst(str_replace("_", " ", $k)) . '</strong>:&nbsp;' . str_replace("_", " ", $v) . '</li>';
        }
    }
    $returnHtml .= '</div>';
    $returnHtml .= '<div class="col-lg-4"><h4>Billing Info</h4>';
    foreach ($returnData['billing_info'] as $k => $v) {
        if(!empty($v))
        {
            $returnHtml .= '<li><strong>' . ucfirst(str_replace("_", " ", $k)) . '</strong>:&nbsp;' . str_replace("_", " ", $v) . '</li>';
        }
    }
    $returnHtml .= '</div>';
    if(!empty($returnData['shipping_info']['first_name']))
    {
        $returnHtml .= '<div class="col-lg-4"><h4>Shipping Info</h4>';
        foreach ($returnData['shipping_info'] as $k => $v) {
            if(!empty($v))
            {
                $returnHtml .= '<li><strong>' . ucfirst(str_replace("_", " ", $k)) . '</strong>:&nbsp;' . str_replace("_", " ", $v) . '</li>';
            }
        }
        $returnHtml .= '</div>';
    }
    $returnHtml .= '</div>';
    $returnHtml .= '</div>';

    // DebugMode output
    if(isset($config['DebugMode']) && $config['DebugMode'])
    {
        $returnHtml .= '<hr>';
        $returnHtml .= '<pre>'.print_r($_SESSION['PayPayResult'], TRUE).'</pre>';
    }

    echo json_encode(array('result' => 'success', 'result_data' => $returnData, 'result_html' => $returnHtml));
    exit;
}
elseif(isset($config['ApiSelection']) && (strtolower($config['ApiSelection']) == 'paypal-rest'))
{
    /**
     * REST API
     */
    ############[ SESSIONS ]################
    $_SESSION['invoice'] = isset($_POST['InvoiceID']) ? $_POST['InvoiceID'] : '';
    $_SESSION['notes'] = isset($_POST['Notes']) ? $_POST['Notes'] : '';
    $_SESSION['item_name'] = isset($_POST['ItemName']) ? $_POST['ItemName'] : '';
    $_SESSION['transaction_type'] = isset($_POST['TransactionType']) ? $_POST['TransactionType'] : 'Authorization';
    $_SESSION['cc_type'] = isset($_POST['CreditCardType']) ? $_POST['CreditCardType'] : '';
    $_SESSION['cc_number'] = isset($_POST['CreditCardNumber']) ? $_POST['CreditCardNumber'] : '';
    $_SESSION['cc_exp_month'] = isset($_POST['CreditCardExpMo']) ?  $_POST['CreditCardExpMo'] : '';
    $_SESSION['cc_exp_year'] = isset($_POST['CreditCardExpYear']) ?  $_POST['CreditCardExpYear'] : '';
    $_SESSION['cvv2'] = isset($_POST['CreditCardSecurityCode']) ? $_POST['CreditCardSecurityCode'] : '';

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
        $_SESSION['billing_phone'] = '';
        $_SESSION['billing_email'] = '';
    }

    // If Shipping is different then billing
    if (!isset($_POST['shippingDisabled']) && !isset($_POST['shippingSameAsBilling']))
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
    }

    $_SESSION['amount'] = isset($_POST['GrandTotal']) ? str_replace(",","", $_POST['GrandTotal']) : '0.00';
    $_SESSION['subtotal'] = isset($_POST['NetAmount']) ? str_replace(",","", $_POST['NetAmount']) : '0.00';
    $_SESSION['shipping_amount'] = ( isset($_POST['ShippingAmount']) && $_POST['ShippingAmount'] != '') ? str_replace(",","", $_POST['ShippingAmount']) : '0.00';
    $_SESSION['handling_amount'] = ( isset($_POST['HandlingAmount']) && $_POST['HandlingAmount'] != '') ? str_replace(",","", $_POST['HandlingAmount']) : '0.00';
    $_SESSION['tax_amount'] = ( isset($_POST['TaxAmount']) && $_POST['TaxAmount'] != '') ? str_replace(",","", $_POST['TaxAmount']) : '0.00';
    $_SESSION['billingInfo'] = isset($_POST['billingInfo']) ? $_POST['billingInfo'] : array();
    $_SESSION['shippingInfo'] = isset($_POST['shippingInfo']) ? $_POST['shippingInfo'] : array();

    ##########[ Create Payment ]############
    // Create new PayPal Api Context
    $paypal_rest = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential(
        $config['RESTAPIClient_Id'],
        $config['RESTAPIClient_Secret']));

    // Add to header
    $paypal_rest->addRequestHeader('PayPal-Partner-Attribution-Id', 'AngellEYE_SP_POS_VT');

    $addr = new \PayPal\Api\Address();
    $addr->setLine1($_SESSION['billing_street1']);
    $addr->setLine2($_SESSION['billing_street2']);
    $addr->setCity($_SESSION['billing_city']);
    $addr->setCountryCode($_SESSION['billing_country_code']);
    $addr->setPostalCode($_SESSION['billing_postal_code']);
    $addr->setState($_SESSION['billing_state']);

    $card = new \PayPal\Api\CreditCard();
    $card->setNumber($_SESSION['cc_number']);
    $card->setType(strtolower($_SESSION['cc_type']));
    $card->setExpireMonth($_SESSION['cc_exp_month']);
    $card->setExpireYear($_SESSION['cc_exp_year']);
    $card->setCvv2($_SESSION['cvv2']);
    $card->setFirstName($_SESSION['billing_first_name']);
    $card->setLastName($_SESSION['billing_last_name']);
    $card->setBillingAddress($addr);

    $fi = new \PayPal\Api\FundingInstrument();
    $fi->setCreditCard($card);

    $payer = new \PayPal\Api\Payer();
    $payer->setPaymentMethod('credit_card');
    $payer->setFundingInstruments(array($fi));

    $amountDetails = new \PayPal\Api\Details();
    $amountDetails->setSubtotal(str_replace(",","", number_format($_SESSION['subtotal'],2)));
    $amountDetails->setTax(str_replace(",","", number_format($_SESSION['tax_amount'],2)) );
    $amountDetails->setShipping(str_replace(",","", number_format($_SESSION['shipping_amount'],2)));
    $amountDetails->setHandlingFee(str_replace(",","", number_format($_SESSION['handling_amount'],2)));

    $amount = new \PayPal\Api\Amount();
    $amount->setCurrency(isset($config['CurrencyCode']) ? $config['CurrencyCode'] : 'USD');
    $amount->setTotal(str_replace(",","", number_format($_SESSION['amount'],2)));
    $amount->setDetails($amountDetails);

    $transaction = new \PayPal\Api\Transaction();
    $transaction->setAmount($amount);
    $transaction->setDescription( (isset($_SESSION['item_name']) && $_SESSION['item_name'] != '') ? $_SESSION['item_name'] : 'PayPal Payments Pro Virtual Terminal Sale');
    $transaction->setInvoiceNumber($_SESSION['invoice']);
    $transaction->setCustom($_SESSION['notes']);

    $payment = new \PayPal\Api\Payment();
    $payment->setIntent(strtolower($_SESSION['transaction_type']));
    $payment->setPayer($payer);
    $payment->setTransactions(array($transaction));

    try {
        $payment->create($paypal_rest);

        if($payment->getState() == 'approved'){

            $transactions = $payment->getTransactions();
            foreach($transactions as $txn)
            {
                $related_resources = $txn->getRelatedResources();
                foreach($related_resources as $related)
                {
                    $related_sale = $related->getSale();
                    if ($related_sale)
                    {
                        $_SESSION['transaction_id'] = $related_sale->id;
                    }
                }
            }

            $_SESSION['payment_id'] = $payment->getId();
            $_SESSION['payment_created'] = $payment->getCreateTime();
            $_SESSION['payment_state'] = $payment->getState();
        }
        else
        {
            $_SESSION['payment_state'] = $payment->getState();
            $result_data_html = $_SESSION['payment_state'];
            echo json_encode(array('result' => 'error', 'result_data' => $result_data_html));
            exit;
        }
    } catch ( \PayPal\Exception\PayPalConnectionException $ex) {
        $PayPalErrors = json_decode($ex->getData());

        // Write to transaction log
        if(isset($config['LogEnabled']) && $config['LogEnabled'])
        {
            $log_array = (array) $PayPalErrors;
            logTransaction($log_array, $config['LogFilePath']);
        }

        $result_data_html = '<ul>';
        if(isset($PayPalErrors->name))
        {
            $result_data_html .= '<li><strong>Error: </strong>&nbsp;' . $PayPalErrors->name . '</li>';
        }
        if(isset($PayPalErrors->message))
        {
            $result_data_html .= '<li><strong>Message: </strong>&nbsp;' . $PayPalErrors->message . '</li>';
        }
        if(isset($PayPalErrors->details))
        {
            $result_data_html .= '<li><strong>Details: </strong><ul>';
            foreach($PayPalErrors->details as $error_details) {
                if(isset($error_details->field))
                {
                    $result_data_html .= '<li><strong>Field: </strong>&nbsp;' . $error_details->field . '</li>';
                }
                if(isset($error_details->issue))
                {
                    $result_data_html .= '<li><strong>Issue: </strong>&nbsp;' . $error_details->issue . '</li>';
                }
            }
            $result_data_html .= '</ul></li>';
        }
        if(isset($PayPalErrors->information_link))
        {
            $result_data_html .= '<li><strong>Information Link: </strong>&nbsp;' . $PayPalErrors->information_link . '</li>';
        }
        if(isset($PayPalErrors->debug_id))
        {
            $result_data_html .= '<li><strong>Debug Id: </strong>&nbsp;' . $PayPalErrors->debug_id . '</li>';
        }
        $result_data_html .= '</ul>';
        echo json_encode(array('result' => 'error', 'result_data' => $result_data_html));
        exit;
    }

    $returnData = array();
    $returnData['payment_details'] = array(
        'transaction_ID' => $_SESSION['transaction_id'],
        'payment_ID' => $_SESSION['payment_id'],
        'payment_created' => $_SESSION['payment_created'],
        'payment_state' => $_SESSION['payment_state'],
        'amount' => $_SESSION['amount'],
        'shipping_amount' => $_SESSION['shipping_amount'],
        'handling_amount' => $_SESSION['handling_amount'],
        'tax_amount' => $_SESSION['tax_amount'],
        'transaction_type' => $_SESSION['transaction_type'],
        'card_type' => $_SESSION['cc_type'],
        'card_number' => substr($_SESSION['cc_number'], 0, 4) . str_repeat("X", strlen($_SESSION['cc_number']) - 8) . substr($_SESSION['cc_number'], -4),
        'card_expiration' => $_SESSION['cc_expdate'],
        'invoice' => $_SESSION['invoice'],
        'item_name' => $_SESSION['item_name'],
        'notes' => $_SESSION['notes']
    );
    $returnData['billing_info'] = array(
        'first_name' => $_SESSION['billing_first_name'],
        'last_name' => $_SESSION['billing_last_name'],
        'street_1' => $_SESSION['billing_street1'],
        'street_2' => $_SESSION['billing_street2'],
        'city' => $_SESSION['billing_city'],
        'state' => $_SESSION['billing_state'],
        'postal_code' => $_SESSION['billing_postal_code'],
        'country_code' => $_SESSION['billing_country_code'],
        'phone' => $_SESSION['billing_phone'],
        'email' => $_SESSION['billing_email'],
    );
    $returnData['shipping_info'] = array(
        'first_name' => $_SESSION['shipping_first_name'],
        'last_name' => $_SESSION['shipping_last_name'],
        'street_1' => $_SESSION['shipping_street1'],
        'street_2' => $_SESSION['shipping_street2'],
        'city' => $_SESSION['shipping_city'],
        'state' => $_SESSION['shipping_state'],
        'postal_code' => $_SESSION['shipping_postal_code'],
        'country_code' => $_SESSION['shipping_country_code'],
        'phone' => $_SESSION['shipping_phone'],
        'email' => $_SESSION['shipping_email'],
    );

    $returnHtml = '';
    $returnHtml .= '<div class="well">';
    $returnHtml .= '<div class="row" id="pos-panel-success-details">';
    $returnHtml .= '<div class="col-lg-4"><h4>Payment Details</h4>';
    foreach ($returnData['payment_details'] as $k => $v) {
        if(!empty($v))
        {
            $returnHtml .= '<li><strong>' . ucfirst(str_replace("_", " ", $k)) . '</strong>:&nbsp;' . str_replace("_", " ", $v) . '</li>';
        }
    }
    $returnHtml .= '</div>';
    $returnHtml .= '<div class="col-lg-4"><h4>Billing Info</h4>';
    foreach ($returnData['billing_info'] as $k => $v) {
        if(!empty($v))
        {
            $returnHtml .= '<li><strong>' . ucfirst(str_replace("_", " ", $k)) . '</strong>:&nbsp;' . str_replace("_", " ", $v) . '</li>';
        }
    }
    $returnHtml .= '</div>';
    if(!empty($returnData['shipping_info']['first_name']))
    {
        $returnHtml .= '<div class="col-lg-4"><h4>Shipping Info</h4>';
        foreach ($returnData['shipping_info'] as $k => $v) {
            if(!empty($v))
            {
                $returnHtml .= '<li><strong>' . ucfirst(str_replace("_", " ", $k)) . '</strong>:&nbsp;' . str_replace("_", " ", $v) . '</li>';
            }
        }
        $returnHtml .= '</div>';
    }
    $returnHtml .= '</div>';
    $returnHtml .= '</div>';

    // DebugMode output
    if(isset($config['DebugMode']) && $config['DebugMode'])
    {
        $returnHtml .= '<hr>';
        $returnHtml .= '<pre>'.print_r($payment, TRUE).'</pre>';
    }

    // Write to transaction log
    if(isset($config['LogEnabled']) && $config['LogEnabled'])
    {
        logTransaction($payment, $config['LogFilePath']);
    }

    echo json_encode(array('result' => 'success', 'result_data' => $returnData, 'result_html' => $returnHtml));
    exit;
}
else
{
    // error - ApiSelection
    echo json_encode(array('result' => 'error', 'result_data' => 'Missing configuration; set your preferred API in config file.'));
    exit;
}

function logTransaction($data = array(), $file = ''){
    if(!empty($data) && $file != '')
    {
        // Append to log file
        file_put_contents($file, print_r($data, true) . "\n\n", FILE_APPEND);
    }
}

