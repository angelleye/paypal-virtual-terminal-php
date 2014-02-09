<?php
require_once('includes/config.php');
require_once('includes/paypal.class.php');

// PayPal object
$paypal_config = array('Sandbox' => $sandbox, 'APIUsername' => $api_username, 'APIPassword' => $api_password, 'APISignature' => $api_signature);
$paypal = new PayPal($paypal_config);

// Result
$result = $_GET['result'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? if ($result=='SUCCESS'): echo "Successful"; else: echo "Error"; endif; ?></title>
</head>
<body>
<div style="width: 600px; margin: auto;">
	<?php if ($result == "SUCCESS"){?>
    	<!-- SUCCESS -->
  <a href="index.php">New Transaction</a>
<h1 style="color: green;">Successful</h1>
        <div class="eC_PurchaserInfoWrapper">
         <h3 class="eC_InfoHeader" >Payment Information</h3>
         <div class="eC_InfoContainer" >
           <p class="eC_OrderInfo">
             <strong class="eC_OrderInfoLabel">Payment Method:</strong> <?php echo $_SESSION['cc_type']; ?><br />
             <strong class="eC_OrderInfoLabel">Card Number:</strong> xxxx xxxx xxxx <?php echo substr($_SESSION['cc_number'], -4, 4); ?></p>
         </div>
 <?php if ($_SESSION['billingInfo'] == 'true'){ ?>  
         <h3 class="eC_InfoHeader">Billing Information</h3>
         <div class="eC_InfoContainer">
           <p class="eC_OrderInfo"><?php echo $_SESSION['billing_first_name']; ?>&nbsp;<?php echo $_SESSION['billing_last_name']; ?><br />
             <?php echo $_SESSION['billing_street1'] . ' ' . $_SESSION['billing_street2']; ?><br />
             <?php echo $_SESSION['billing_city']; ?>,&nbsp;<?php echo $_SESSION['billing_state']; ?>&nbsp;<?php echo $_SESSION['billing_postal_code']; ?><br />
             <?php echo $_SESSION['billing_country_name']; ?><br />
             <?php echo $_SESSION['billing_phone']; ?><br />
           <?php echo $_SESSION['billing_email']; ?></p>
         </div>
         
         <h3 class="eC_InfoHeader">Shipping Information</h3>
         <div class="eC_InfoContainer">
           <p class="eC_OrderInfo"><?php echo $_SESSION['shipping_first_name']; ?>&nbsp;<?php echo $_SESSION['shipping_last_name']; ?><br />
             <?php echo $_SESSION['shipping_street1'] . ' ' . $_SESSION['shipping_street2']; ?><br />
             <?php echo $_SESSION['shipping_city']; ?>,&nbsp;<?php echo $_SESSION['shipping_state']; ?>&nbsp;<?php echo $_SESSION['shipping_postal_code']; ?><br />
             <?php echo $_SESSION['shipping_country_name']; ?><br />
             <?php echo $_SESSION['shipping_phone']; ?><br />
           <?php echo $_SESSION['shipping_email']; ?></p>
         </div>
 <?php } else { ?>
         <div class="eC_InfoContainer">
           <p class="eC_OrderInfo"><b>Bill To:</b> <?php echo $_SESSION['billing_first_name']; ?>&nbsp;<?php echo $_SESSION['billing_last_name']; ?><br />
           <b>Transaction:</b> $<?php echo number_format($_SESSION['amount'], 2);?>
         </div>
 <?php } ?>
     </div>
        <!-- /SUCCESS -->
    <?php } else {?>
    	<h1 style="color: red;">Error</h1>
    	<?php $paypal->DisplayErrors($_SESSION['paypal_errors']); ?>
        <p><a href="index.php">Try again</a></p>
    <?php } ?>
    
</div>
<?php
if($sandbox)
{
	echo '<div>';
	echo '<br /><br />';
	echo '<pre />';
	print_r($_SESSION['DPResult']);	
	echo '</div>';
}
?>
</body>
</html>
<?php session_destroy(); ?>