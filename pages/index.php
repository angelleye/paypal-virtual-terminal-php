<?php
/**
 * Include PayPal config file
 */
require_once('../includes/config.php');

?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>PayPal PHP Virtual Terminal POS</title>

    <!-- Bootstrap Core CSS -->
    <link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Bootstrap Switch Plugin CSS -->
    <link href="../css/bootstrap-switch.min.css" rel="stylesheet">

    <!-- FormValidation.io CSS -->
    <link href="../css/formValidation.min.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="../css/ae-paypal-php-pos.css" rel="stylesheet" type="text/css">

    <!-- Custom Fonts -->
    <link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link rel="shortcut icon" type="image/x-icon" href="../images/favicon.ico">

</head>

<body>

    <!-- Modal HTML -->
    <div id="posInstructionsModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Content will be loaded here from file -->
            </div>
        </div>
    </div>

    <!-- Modal HTML -->
    <div id="posResetConfirmModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    Are you sure you want to start over?
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-primary" id="resetPos">Reset Form</button>
                    <button type="button" data-dismiss="modal" class="btn">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">PayPal PHP Virtual Terminal POS</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-bars fa-fw text-secondary"></i>  <i class="fa fa-caret-down text-secondary"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="instructions-modal.html?v=1" data-toggle="modal" data-target="#posInstructionsModal"><i class="fa fa-info-circle fa-fw"></i> Help/About</a>
                        </li>
                        <li>
                            <a href="http://angelleye.com" target="_blank"><i class="fa fa-gift fa-fw"></i> Donate</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <?php if(isset($config['ShowNavMenuLeft']) && $config['ShowNavMenuLeft']) { ?>
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="index.php"><i class="fa fa-paypal fa-fw"></i> Virtual Terminal</a>
                        </li>
                        <li>
                            <a href="intro.html"><i class="fa fa-info-circle fa-fw"></i> Instructions</a>
                        </li>
                        <li>
                            <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PPHP4UKDUTZYS" target="_blank"><i class="fa fa-gift fa-fw"></i> Donate</a>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
            <?php } ?>
        </nav>

        <!-- Page Content -->
        <div id="page-wrapper" class="<?php echo (isset($config['ShowNavMenuLeft']) && !$config['ShowNavMenuLeft']) ? 'no-left-nav-page' : ''; ?>">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">Virtual Terminal</h1>
                        <div class="alert alert-info">
                            <p>To submit a new transaction enter the payment details below, then click the process payment button.</p>
                        </div>

                        <!-- POS form -->
                        <form class="form-horizontal" id="ae-paypal-pos-form" name="ae-paypal-pos-form" data-currency-sign="<?php echo (isset($config['CurrencySign'])) ? $config['CurrencySign'] : '$'; ?>" role="form" method="POST" action="../bin/process.php" autocomplete="off">

                            <div class="row">
                                <div class="col-lg-12">

                                    <!-- Swipe Card -->
                                    <div class="panel panel-default" id="pos-panel-swipe">
                                        <div class="panel-heading text-uppercase">Swipe Card</div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="swiper">Click to Swipe</label>
                                                <div class="col-lg-6 col-sm-7">
                                                    <input type="password" class="form-control" name="swiper" id="swiper">
                                                    <p class="help-block"><em>Note: A <a target="_blank" href="https://www.usbswiper.com/usbswiper-usb-magnetic-stripe-credit-card-reader.html?utm_source=angelleye&utm_medium=paypal-pos&utm_campaign=usbswiper">USB credit card reader</a> is required for swipe functionality.</em></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Info -->
                                    <div class="panel panel-default" id="pos-panel-card-info">
                                        <div class="panel-heading text-uppercase text-primary">Credit/Debit Card Info</div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="BillingFirstName">First Name</label>
                                                <div class="col-lg-3 col-sm-5">
                                                    <input type="text" class="form-control" name="BillingFirstName" id="BillingFirstName" maxlength="35" required="required" />
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="BillingLastName">Last Name</label>
                                                <div class="col-lg-3 col-sm-5">
                                                    <input type="text" class="form-control" name="BillingLastName" id="BillingLastName" maxlength="35" required="required" />
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="CreditCardType">Card Type</label>
                                                <div class="col-lg-2 col-sm-5">
                                                    <select class="form-control" name="CreditCardType" id="CreditCardType" required="required">
                                                        <option value="">- Select</option>
                                                        <option value="Visa">Visa</option>
                                                        <option value="MasterCard">Master Card</option>
                                                        <option value="Discover">Discover</option>
                                                        <option value="Amex">American Express</option>
                                                        <option value="Switch">Switch</option>
                                                        <option value="Solo">Solo</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="CreditCardNumber">Card Number</label>
                                                <div class="col-lg-4 col-sm-7">
                                                    <input type="text" class="form-control" name="CreditCardNumber" id="CreditCardNumber" maxlength="35" required="required" />
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="CreditCardExpMo">Exp Mo.</label>
                                                <div class="col-lg-2 col-sm-5">
                                                    <select class="form-control" name="CreditCardExpMo" id="CreditCardExpMo" required="required">
                                                        <option value="">- Select</option>
                                                        <option value="01">01</option>
                                                        <option value="02">02</option>
                                                        <option value="03">03</option>
                                                        <option value="04">04</option>
                                                        <option value="05">05</option>
                                                        <option value="06">06</option>
                                                        <option value="07">07</option>
                                                        <option value="08">08</option>
                                                        <option value="09">09</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12">12</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="CreditCardExpYear">Exp. Year</label>
                                                <div class="col-lg-2 col-sm-5">
                                                    <select class="form-control" name="CreditCardExpYear" id="CreditCardExpYear" required="required">
                                                        <option value="">- Select</option>
                                                        <option value="2015">2015</option>
                                                        <option value="2016">2016</option>
                                                        <option value="2017">2017</option>
                                                        <option value="2018">2018</option>
                                                        <option value="2019">2019</option>
                                                        <option value="2020">2020</option>
                                                        <option value="2021">2021</option>
                                                        <option value="2022">2022</option>
                                                        <option value="2023">2023</option>
                                                        <option value="2024">2024</option>
                                                        <option value="2025">2025</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group" id="DivCreditCardIssueNumber">
                                                <label class="col-lg-3 col-sm-4 control-label" for="CreditCardIssueNumber">Issue Number</label>
                                                <div class="col-lg-2 col-sm-3">
                                                    <input type="text" class="form-control" name="CreditCardIssueNumber" id="CreditCardIssueNumber" maxlength="2" />
                                                </div>
                                            </div>

                                            <div class="form-group" id="DivCreditCardSecurityCode">
                                                <label class="col-lg-3 col-sm-4 control-label" for="CreditCardSecurityCode">Security Code</label>
                                                <div class="col-lg-2 col-sm-3">
                                                    <input type="text" class="form-control" name="CreditCardSecurityCode" id="CreditCardSecurityCode" maxlength="5" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payment Info -->
                                    <div class="panel panel-default" id="pos-panel-payment-info">
                                        <div class="panel-heading text-uppercase">Payment Info</div>
                                        <div class="panel-body">

                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="TransactionType">Transaction Type</label>
                                                <div class="col-lg-3 col-sm-5">
                                                    <select class="form-control" name="TransactionType" id="TransactionType" required="required">
                                                        <option value="Authorization">Auth</option>
                                                        <option value="Sale" selected="selected">Sale</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="NetAmount">Net Order Amount</label>
                                                <div class="col-lg-3 col-sm-5">
                                                    <div class="input-group">
                                                        <div class="input-group-addon"><?php echo (isset($config['CurrencySign'])) ? $config['CurrencySign'] : '$'; ?></div>
                                                        <input type="text" class="form-control" name="NetAmount" id="NetAmount" required="required" pattern="([0-9]|\$|,|.)+" data-a-sign="<?php echo (isset($config['CurrencySign'])) ? $config['CurrencySign'] : '$'; ?>" data-m-dec="2" data-w-empty="" data-l-zero="keep" data-a-form="false" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="ShippingAmount">Shipping Amount</label>
                                                <div class="col-lg-3 col-sm-5">
                                                    <div class="input-group">
                                                        <div class="input-group-addon"><?php echo (isset($config['CurrencySign'])) ? $config['CurrencySign'] : '$'; ?></div>
                                                        <input type="text" class="form-control" name="ShippingAmount" id="ShippingAmount" pattern="([0-9]|\$|,|.)+" data-a-sign="<?php echo (isset($config['CurrencySign'])) ? $config['CurrencySign'] : '$'; ?>" data-m-dec="2" data-w-empty="" data-l-zero="keep" data-a-form="false" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="HandlingAmount">Handling Amount</label>
                                                <div class="col-lg-3 col-sm-5">
                                                    <div class="input-group">
                                                        <div class="input-group-addon"><?php echo (isset($config['CurrencySign'])) ? $config['CurrencySign'] : '$'; ?></div>
                                                        <input type="text" class="form-control" name="HandlingAmount" id="HandlingAmount" pattern="([0-9]|\$|,|.)+" data-a-sign="<?php echo (isset($config['CurrencySign'])) ? $config['CurrencySign'] : '$'; ?>" data-m-dec="2" data-w-empty="" data-l-zero="keep" data-a-form="false"/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group" id="DivTaxRate">
                                                <label class="col-lg-3 col-sm-4 control-label" for="TaxRate">Tax Rate</label>
                                                <div class="col-lg-3 col-sm-5">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="TaxRate" id="TaxRate" maxlength="4" />
                                                        <div class="input-group-addon">%</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="TaxAmount">Tax Amount</label>
                                                <div class="col-lg-3 col-sm-5">
                                                    <div id="TaxAmountDisplay" class="form-control-static"><strong>0.00</strong></div>
                                                    <input type="hidden" name="TaxAmount" id="TaxAmount" value="0" />
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="GrandTotal">Grand Total</label>
                                                <div class="col-lg-3 col-sm-5">
                                                    <div id="GrandTotalDisplay" class="form-control-static"><strong>0.00</strong></div>
                                                    <input type="hidden" name="GrandTotal" id="GrandTotal" value="0" />
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="InvoiceID">Invoice Number</label>
                                                <div class="col-lg-3 col-sm-5">
                                                    <input type="text" class="form-control" name="InvoiceID" id="InvoiceID" maxlength="35" />
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="ItemName">ItemName</label>
                                                <div class="col-lg-3 col-sm-8">
                                                    <input type="text" class="form-control" name="ItemName" id="ItemName" maxlength="70" />
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="Notes">Notes</label>
                                                <div class="col-lg-6 col-sm-8">
                                                    <textarea class="form-control" name="Notes" id="Notes"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Billing Address -->
                                    <div class="panel panel-default" id="pos-panel-billing">
                                        <div class="panel-heading text-uppercase">Billing Address</div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="billingInfo">Enter Billing Address</label>
                                                <div class="col-lg-4 col-sm-8">
                                                    <input type="checkbox" class="checkbox" checked="checked" name="billingInfo" id="billingInfo" value="true" />
                                                </div>
                                            </div>

                                            <!-- Billing Address Fields -->
                                            <div id="FormBillingAddress">

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="BillingStreet">Street</label>
                                                    <div class="col-lg-4 col-sm-8">
                                                        <input type="text" class="form-control" name="BillingStreet" maxlength="25" id="BillingStreet" required="required" />
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="BillingStreet2">Street 2</label>
                                                    <div class="col-lg-4 col-sm-8">
                                                        <input type="text" class="form-control" name="BillingStreet2" maxlength="25" id="BillingStreet2" />
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="BillingCity">City</label>
                                                    <div class="col-lg-4 col-sm-8">
                                                        <input type="text" class="form-control" name="BillingCity" id="BillingCity" maxlength="25" required="required" />
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="BillingState">State</label>
                                                    <div class="col-lg-4 col-sm-8">
                                                        <select class="form-control" name="BillingState" id="BillingState" required="required" >
                                                            <option value="" selected="selected">Choose a state</option>
                                                            <option value="AL">Alabama</option>
                                                            <option value="AK">Alaska</option>
                                                            <option value="AS">American Samoa</option>
                                                            <option value="AZ">Arizona</option>
                                                            <option value="AR">Arkansas</option>
                                                            <option value="CA">California</option>
                                                            <option value="CO">Colorado</option>
                                                            <option value="CT">Connecticut</option>
                                                            <option value="DE">Delaware</option>
                                                            <option value="DC">District Of Columbia</option>
                                                            <option value="FM">Federated States Of Micronesia</option>
                                                            <option value="FL">Florida</option>
                                                            <option value="GA">Georgia</option>
                                                            <option value="GU">Guam</option>
                                                            <option value="HI">Hawaii</option>
                                                            <option value="ID">Idaho</option>
                                                            <option value="IL">Illinois</option>
                                                            <option value="IN">Indiana</option>
                                                            <option value="IA">Iowa</option>
                                                            <option value="KS">Kansas</option>
                                                            <option value="KY">Kentucky</option>
                                                            <option value="LA">Louisiana</option>
                                                            <option value="ME">Maine</option>
                                                            <option value="MH">Marshall Islands</option>
                                                            <option value="MD">Maryland</option>
                                                            <option value="MA">Massachusetts</option>
                                                            <option value="MI">Michigan</option>
                                                            <option value="MN">Minnesota</option>
                                                            <option value="MS">Mississippi</option>
                                                            <option value="MO">Missouri</option>
                                                            <option value="MT">Montana</option>
                                                            <option value="NE">Nebraska</option>
                                                            <option value="NV">Nevada</option>
                                                            <option value="NH">New Hampshire</option>
                                                            <option value="NJ">New Jersey</option>
                                                            <option value="NM">New Mexico</option>
                                                            <option value="NY">New York</option>
                                                            <option value="NC">North Carolina</option>
                                                            <option value="ND">North Dakota</option>
                                                            <option value="MP">Northern Mariana Islands</option>
                                                            <option value="OH">Ohio</option>
                                                            <option value="OK">Oklahoma</option>
                                                            <option value="OR">Oregon</option>
                                                            <option value="PW">Palau</option>
                                                            <option value="PA">Pennsylvania</option>
                                                            <option value="PR">Puerto Rico</option>
                                                            <option value="RI">Rhode Island</option>
                                                            <option value="SC">South Carolina</option>
                                                            <option value="SD">South Dakota</option>
                                                            <option value="TN">Tennessee</option>
                                                            <option value="TX">Texas</option>
                                                            <option value="UT">Utah</option>
                                                            <option value="VT">Vermont</option>
                                                            <option value="VI">Virgin Islands</option>
                                                            <option value="VA">Virginia</option>
                                                            <option value="WA">Washington</option>
                                                            <option value="WV">West Virginia</option>
                                                            <option value="WI">Wisconsin</option>
                                                            <option value="WY">Wyoming</option>
                                                            <option value="AA">Armed Forces Americas</option>
                                                            <option value="AE">Armed Forces</option>
                                                            <option value="AP">Armed Forces Pacific</option>
                                                            <option value="AB">Alberta</option>
                                                            <option value="BC">British Columbia</option>
                                                            <option value="MB">Manitoba</option>
                                                            <option value="NB">New Brunswick</option>
                                                            <option value="NF">Newfoundland and Labrador</option>
                                                            <option value="NT">Northwest Territories</option>
                                                            <option value="NS">Nova Scotia</option>
                                                            <option value="NU">Nunavut</option>
                                                            <option value="ON">Ontario</option>
                                                            <option value="PE">Prince Edward Island</option>
                                                            <option value="QC">Quebec</option>
                                                            <option value="SK">Saskatchewan</option>
                                                            <option value="YK">Yukon</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="BillingPostalCode">Postal Code</label>
                                                    <div class="col-lg-2 col-sm-4">
                                                        <input type="text" class="form-control" name="BillingPostalCode" id="BillingPostalCode" maxlength="25" required="required" />
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="BillingCountryCode">Country</label>
                                                    <div class="col-lg-4 col-sm-8">
                                                        <select class="form-control" name="BillingCountryCode" id="BillingCountryCode" required="required" >
                                                            <option value="" selected="selected">Choose a country</option>
                                                            <option value="US">United States</option>
                                                            <option value="GB">United Kingdom</option>
                                                            <option value="AF">Afghanistan</option>
                                                            <option value="AX">Aland Islands</option>
                                                            <option value="AL">Albania</option>
                                                            <option value="DZ">Algeria</option>
                                                            <option value="AS">American Samoa</option>
                                                            <option value="AD">Andorra</option>
                                                            <option value="AO">Angola</option>
                                                            <option value="AI">Anguilla</option>
                                                            <option value="AQ">Antarctica</option>
                                                            <option value="AG">Antigua &amp; Barbuda</option>
                                                            <option value="AR">Argentina</option>
                                                            <option value="AM">Armenia</option>
                                                            <option value="AW">Aruba</option>
                                                            <option value="AU">Australia</option>
                                                            <option value="AT">Austria</option>
                                                            <option value="AZ">Azerbaijan</option>
                                                            <option value="BS">Bahamas</option>
                                                            <option value="BH">Bahrain</option>
                                                            <option value="BD">Bangladesh</option>
                                                            <option value="BB">Barbados</option>
                                                            <option value="BY">Belarus</option>
                                                            <option value="BE">Belgium</option>
                                                            <option value="BZ">Belize</option>
                                                            <option value="BJ">Benin</option>
                                                            <option value="BM">Bermuda</option>
                                                            <option value="BT">Bhutan</option>
                                                            <option value="BO">Bolivia</option>
                                                            <option value="BA">Bosnia &amp; Herzegovina</option>
                                                            <option value="BW">Botswana</option>
                                                            <option value="BV">Bouvet Island</option>
                                                            <option value="BR">Brazil</option>
                                                            <option value="IO">British Indian Ocean Territory</option>
                                                            <option value="BN">Brunei Darussalam</option>
                                                            <option value="BG">Bulgaria</option>
                                                            <option value="BF">Burkina Faso</option>
                                                            <option value="BI">Burundi</option>
                                                            <option value="KH">Cambodia</option>
                                                            <option value="CM">Cameroon</option>
                                                            <option value="CA">Canada</option>
                                                            <option value="CV">Cape Verde</option>
                                                            <option value="KY">Cayman Islands</option>
                                                            <option value="CF">Central African Rep</option>
                                                            <option value="TD">Chad</option>
                                                            <option value="CL">Chile</option>
                                                            <option value="CN">China</option>
                                                            <option value="CX">Christmas Island</option>
                                                            <option value="CC">Cocos (Keeling) Islands</option>
                                                            <option value="CO">Colombia</option>
                                                            <option value="KM">Comoros</option>
                                                            <option value="CG">Congo</option>
                                                            <option value="CK">Cook Islands</option>
                                                            <option value="CR">Costa Rica</option>
                                                            <option value="CI">Côte d'Ivoire</option>
                                                            <option value="HR">Croatia</option>
                                                            <option value="CU">Cuba</option>
                                                            <option value="CY">Cyprus</option>
                                                            <option value="CZ">Czech Republic</option>
                                                            <option value="CD">Dem Rep of Congo (Zaire)</option>
                                                            <option value="DK">Denmark</option>
                                                            <option value="DJ">Djibouti</option>
                                                            <option value="DM">Dominica</option>
                                                            <option value="DO">Dominican Republic</option>
                                                            <option value="EC">Ecuador</option>
                                                            <option value="EG">Egypt</option>
                                                            <option value="SV">El Salvador</option>
                                                            <option value="GQ">Equatorial Guinea</option>
                                                            <option value="ER">Eritrea</option>
                                                            <option value="EE">Estonia</option>
                                                            <option value="ET">Ethiopia</option>
                                                            <option value="FK">Falkland Islands (Malvinas)</option>
                                                            <option value="FO">Faeroe Islands</option>
                                                            <option value="FJ">Fiji</option>
                                                            <option value="FI">Finland</option>
                                                            <option value="FR">France</option>
                                                            <option value="GF">French Guiana</option>
                                                            <option value="PF">French Polynesia/Tahiti</option>
                                                            <option value="TF">French Southern Territories</option>
                                                            <option value="GA">Gabon</option>
                                                            <option value="GM">Gambia</option>
                                                            <option value="GE">Georgia</option>
                                                            <option value="DE">Germany</option>
                                                            <option value="GH">Ghana</option>
                                                            <option value="GI">Gibraltar</option>
                                                            <option value="GR">Greece</option>
                                                            <option value="GL">Greenland</option>
                                                            <option value="GD">Grenada</option>
                                                            <option value="GP">Guadeloupe</option>
                                                            <option value="GU">Guam</option>
                                                            <option value="GT">Guatemala</option>
                                                            <option value="GG">Guernsey</option>
                                                            <option value="GN">Guinea</option>
                                                            <option value="GW">Guinea-Bissau</option>
                                                            <option value="GY">Guyana</option>
                                                            <option value="HT">Haiti</option>
                                                            <option value="HM">Heard Island &amp; McDonald Islands</option>
                                                            <option value="VA">Holy See (Vatican City State)</option>
                                                            <option value="HN">Honduras</option>
                                                            <option value="HK">Hong Kong</option>
                                                            <option value="HU">Hungary</option>
                                                            <option value="IS">Iceland</option>
                                                            <option value="IN">India</option>
                                                            <option value="ID">Indonesia</option>
                                                            <option value="IR">Iran</option>
                                                            <option value="IQ">Iraq</option>
                                                            <option value="IE">Ireland</option>
                                                            <option value="IM">Isle of Man</option>
                                                            <option value="IL">Israel</option>
                                                            <option value="IT">Italy</option>
                                                            <option value="CI">Ivory Coast</option>
                                                            <option value="JM">Jamaica</option>
                                                            <option value="JP">Japan</option>
                                                            <option value="JE">Jersey</option>
                                                            <option value="JO">Jordan</option>
                                                            <option value="KZ">Kazakhstan</option>
                                                            <option value="KE">Kenya</option>
                                                            <option value="KI">Kiribati</option>
                                                            <option value="KP">Korea, Democratic Republic of</option>
                                                            <option value="KR">Korea, Republic of</option>
                                                            <option value="KW">Kuwait</option>
                                                            <option value="KG">Kyrgyzstan</option>
                                                            <option value="LA">Laos</option>
                                                            <option value="LV">Latvia</option>
                                                            <option value="LB">Lebanon</option>
                                                            <option value="LS">Lesotho</option>
                                                            <option value="LR">Liberia</option>
                                                            <option value="LY">Libya</option>
                                                            <option value="LI">Liechtenstein</option>
                                                            <option value="LT">Lithuania</option>
                                                            <option value="LU">Luxembourg</option>
                                                            <option value="MO">Macau</option>
                                                            <option value="MK">Macedonia</option>
                                                            <option value="MG">Madagascar</option>
                                                            <option value="MW">Malawi</option>
                                                            <option value="MY">Malaysia</option>
                                                            <option value="MV">Maldives</option>
                                                            <option value="ML">Mali</option>
                                                            <option value="MT">Malta</option>
                                                            <option value="MH">Marshall Islands</option>
                                                            <option value="MQ">Martinique</option>
                                                            <option value="MR">Mauritania</option>
                                                            <option value="MU">Mauritius</option>
                                                            <option value="MX">Mexico</option>
                                                            <option value="FM">Micronesia</option>
                                                            <option value="MD">Moldova</option>
                                                            <option value="MC">Monaco</option>
                                                            <option value="MN">Mongolia</option>
                                                            <option value="MS">Montserrat</option>
                                                            <option value="MA">Morocco</option>
                                                            <option value="MZ">Mozambique</option>
                                                            <option value="MM">Myanmar</option>
                                                            <option value="NA">Namibia</option>
                                                            <option value="NR">Nauru</option>
                                                            <option value="NP">Nepal</option>
                                                            <option value="NL">Netherlands</option>
                                                            <option value="AN">Netherlands Antilles</option>
                                                            <option value="NC">New Caledonia</option>
                                                            <option value="NZ">New Zealand</option>
                                                            <option value="NI">Nicaragua</option>
                                                            <option value="NE">Niger</option>
                                                            <option value="NG">Nigeria</option>
                                                            <option value="NU">Niue</option>
                                                            <option value="NF">Norfolk Island</option>
                                                            <option value="MP">Northern Mariana Islands</option>
                                                            <option value="NO">Norway</option>
                                                            <option value="OM">Oman</option>
                                                            <option value="PK">Pakistan</option>
                                                            <option value="PW">Palau</option>
                                                            <option value="PS">Palestinian Territory</option>
                                                            <option value="PA">Panama</option>
                                                            <option value="PG">Papua New Guinea</option>
                                                            <option value="PY">Paraguay</option>
                                                            <option value="PE">Peru</option>
                                                            <option value="PH">Philippines</option>
                                                            <option value="PN">Pitcairn</option>
                                                            <option value="PL">Poland</option>
                                                            <option value="PT">Portugal</option>
                                                            <option value="PR">Puerto Rico</option>
                                                            <option value="QA">Qatar</option>
                                                            <option value="RE">Reunion Is.</option>
                                                            <option value="RO">Romania</option>
                                                            <option value="RU">Russia</option>
                                                            <option value="RW">Rwanda</option>
                                                            <option value="SH">Saint Helena</option>
                                                            <option value="KN">Saint Kitts &amp; Nevis</option>
                                                            <option value="LC">Saint Lucia</option>
                                                            <option value="PM">Saint Pierre &amp; Miquelon</option>
                                                            <option value="VC">Saint Vincent &amp; Grenadines</option>
                                                            <option value="AS">Samoa (Amer.)</option>
                                                            <option value="WS">Samoa (Western)</option>
                                                            <option value="SM">San Marino</option>
                                                            <option value="KN">Sao Tome &amp; Principe</option>
                                                            <option value="SA">Saudi Arabia</option>
                                                            <option value="SN">Senegal</option>
                                                            <option value="CS">Serbia &amp; Montenegro</option>
                                                            <option value="SC">Seychelles</option>
                                                            <option value="SL">Sierra Leone</option>
                                                            <option value="SG">Singapore</option>
                                                            <option value="SK">Slovakia</option>
                                                            <option value="SI">Slovenia</option>
                                                            <option value="SB">Solomon Islands</option>
                                                            <option value="ZA">South Africa</option>
                                                            <option value="GS">South Georgia &amp; S. Sandwich Islands</option>
                                                            <option value="ES">Spain</option>
                                                            <option value="LK">Sri Lanka</option>
                                                            <option value="SD">Sudan</option>
                                                            <option value="SR">Suriname</option>
                                                            <option value="SR">Svalbard &amp; Jan Mayen</option>
                                                            <option value="SZ">Swaziland</option>
                                                            <option value="SE">Sweden</option>
                                                            <option value="CH">Switzerland</option>
                                                            <option value="SY">Syria</option>
                                                            <option value="TW">Taiwan</option>
                                                            <option value="TJ">Tajikistan</option>
                                                            <option value="TZ">Tanzania</option>
                                                            <option value="TH">Thailand</option>
                                                            <option value="TL">Timor-Leste</option>
                                                            <option value="TG">Togo</option>
                                                            <option value="TK">Tokelau</option>
                                                            <option value="TO">Tonga</option>
                                                            <option value="TT">Trinidad &amp; Tobago</option>
                                                            <option value="TN">Tunisia</option>
                                                            <option value="TR">Turkey</option>
                                                            <option value="TM">Turkmenistan</option>
                                                            <option value="TC">Turks &amp; Caicos Islands</option>
                                                            <option value="TV">Tuvalu</option>
                                                            <option value="UG">Uganda</option>
                                                            <option value="UA">Ukraine</option>
                                                            <option value="AE">United Arab Emirates</option>
                                                            <option value="GB">United Kingdom</option>
                                                            <option value="US">United States</option>
                                                            <option value="UM">United States Minor Outlying Islands</option>
                                                            <option value="UY">Uruguay</option>
                                                            <option value="UZ">Uzbekistan</option>
                                                            <option value="VU">Vanuatu</option>
                                                            <option value="VE">Venezuela</option>
                                                            <option value="VN">Vietnam</option>
                                                            <option value="VG">Virgin Islands, British</option>
                                                            <option value="VI">Virgin Islands, US</option>
                                                            <option value="WF">Wallis &amp; Futuna Isle</option>
                                                            <option value="EH">Western Sahara</option>
                                                            <option value="YE">Yemen</option>
                                                            <option value="ZM">Zambia</option>
                                                            <option value="ZW">Zimbabwe</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="BillingPhoneNumber">Phone Number</label>
                                                    <div class="col-lg-3 col-sm-4">
                                                        <input type="text" class="form-control" name="BillingPhoneNumber" id="BillingPhoneNumber" maxlength="25" />
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="BillingEmail">Email Address</label>
                                                    <div class="col-lg-6 col-sm-8">
                                                        <input type="text" class="form-control" name="BillingEmail" id="BillingEmail" maxlength="25" />
                                                    </div>
                                                </div>
                                            </div><!-- / Billing Address Fields -->

                                        </div>
                                    </div>

                                    <!-- Shipping Address -->
                                    <div class="panel panel-default" id="pos-panel-shipping">
                                        <div class="panel-heading text-uppercase">Shipping Address</div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label class="col-lg-3 col-sm-4 control-label" for="shippingDisabled">Shipping Not Req.</label>
                                                <div class="col-lg-4 col-sm-8">
                                                    <input type="checkbox" class="checkbox" name="shippingDisabled" id="shippingDisabled" value="true" />
                                                </div>
                                            </div>

                                            <div class="form-group" id="sameAsBilling">
                                                <label class="col-lg-3 col-sm-4 control-label" for="shippingSameAsBilling">Same as Billing</label>
                                                <div class="col-lg-4 col-sm-8">
                                                    <input type="checkbox" class="checkbox" name="shippingSameAsBilling" id="shippingSameAsBilling" value="true" data-switch-state="false" />
                                                </div>
                                            </div>

                                            <!-- Shipping Address Fields -->
                                            <div id="FormShippingAddress">

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="ShippingFirstName">First Name</label>
                                                    <div class="col-lg-3 col-sm-4">
                                                        <input class="form-control" type="text" name="ShippingFirstName" id="ShippingFirstName" required="required" maxlength="25" />
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="ShippingLastName">Last Name</label>
                                                    <div class="col-lg-3 col-sm-4">
                                                        <input type="text" class="form-control" name="ShippingLastName" id="ShippingLastName" required="required" maxlength="25" />
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="ShippingStreet">Street</label>
                                                    <div class="col-lg-4 col-sm-8">
                                                        <input type="text" class="form-control" name="ShippingStreet" id="ShippingStreet" required="required" maxlength="25" />
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="ShippingStreet2">Street 2</label>
                                                    <div class="col-lg-4 col-sm-8">
                                                        <input type="text" class="form-control" name="ShippingStreet2" id="ShippingStreet2" maxlength="25" />
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="ShippingCity">City</label>
                                                    <div class="col-lg-4 col-sm-8">
                                                        <input type="text" class="form-control" name="ShippingCity" id="ShippingCity" required="required" maxlength="25" />
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="ShippingState">State</label>
                                                    <div class="col-lg-4 col-sm-8">
                                                        <select class="form-control" name="ShippingState" id="ShippingState" required="required" />
                                                            <option value="" selected="selected">Choose a state</option>
                                                            <option value="AL">Alabama</option>
                                                            <option value="AK">Alaska</option>
                                                            <option value="AS">American Samoa</option>
                                                            <option value="AZ">Arizona</option>
                                                            <option value="AR">Arkansas</option>
                                                            <option value="CA">California</option>
                                                            <option value="CO">Colorado</option>
                                                            <option value="CT">Connecticut</option>
                                                            <option value="DE">Delaware</option>
                                                            <option value="DC">District Of Columbia</option>
                                                            <option value="FM">Federated States Of Micronesia</option>
                                                            <option value="FL">Florida</option>
                                                            <option value="GA">Georgia</option>
                                                            <option value="GU">Guam</option>
                                                            <option value="HI">Hawaii</option>
                                                            <option value="ID">Idaho</option>
                                                            <option value="IL">Illinois</option>
                                                            <option value="IN">Indiana</option>
                                                            <option value="IA">Iowa</option>
                                                            <option value="KS">Kansas</option>
                                                            <option value="KY">Kentucky</option>
                                                            <option value="LA">Louisiana</option>
                                                            <option value="ME">Maine</option>
                                                            <option value="MH">Marshall Islands</option>
                                                            <option value="MD">Maryland</option>
                                                            <option value="MA">Massachusetts</option>
                                                            <option value="MI">Michigan</option>
                                                            <option value="MN">Minnesota</option>
                                                            <option value="MS">Mississippi</option>
                                                            <option value="MO">Missouri</option>
                                                            <option value="MT">Montana</option>
                                                            <option value="NE">Nebraska</option>
                                                            <option value="NV">Nevada</option>
                                                            <option value="NH">New Hampshire</option>
                                                            <option value="NJ">New Jersey</option>
                                                            <option value="NM">New Mexico</option>
                                                            <option value="NY">New York</option>
                                                            <option value="NC">North Carolina</option>
                                                            <option value="ND">North Dakota</option>
                                                            <option value="MP">Northern Mariana Islands</option>
                                                            <option value="OH">Ohio</option>
                                                            <option value="OK">Oklahoma</option>
                                                            <option value="OR">Oregon</option>
                                                            <option value="PW">Palau</option>
                                                            <option value="PA">Pennsylvania</option>
                                                            <option value="PR">Puerto Rico</option>
                                                            <option value="RI">Rhode Island</option>
                                                            <option value="SC">South Carolina</option>
                                                            <option value="SD">South Dakota</option>
                                                            <option value="TN">Tennessee</option>
                                                            <option value="TX">Texas</option>
                                                            <option value="UT">Utah</option>
                                                            <option value="VT">Vermont</option>
                                                            <option value="VI">Virgin Islands</option>
                                                            <option value="VA">Virginia</option>
                                                            <option value="WA">Washington</option>
                                                            <option value="WV">West Virginia</option>
                                                            <option value="WI">Wisconsin</option>
                                                            <option value="WY">Wyoming</option>
                                                            <option value="AA">Armed Forces Americas</option>
                                                            <option value="AE">Armed Forces</option>
                                                            <option value="AP">Armed Forces Pacific</option>
                                                            <option value="AB">Alberta</option>
                                                            <option value="BC">British Columbia</option>
                                                            <option value="MB">Manitoba</option>
                                                            <option value="NB">New Brunswick</option>
                                                            <option value="NF">Newfoundland and Labrador</option>
                                                            <option value="NT">Northwest Territories</option>
                                                            <option value="NS">Nova Scotia</option>
                                                            <option value="NU">Nunavut</option>
                                                            <option value="ON">Ontario</option>
                                                            <option value="PE">Prince Edward Island</option>
                                                            <option value="QC">Quebec</option>
                                                            <option value="SK">Saskatchewan</option>
                                                            <option value="YK">Yukon</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="ShippingPostalCode">Postal Code</label>
                                                    <div class="col-lg-2 col-sm-4">
                                                        <input type="text" class="form-control" name="ShippingPostalCode" id="ShippingPostalCode" required="required" />
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="ShippingCountryCode">Country</label>
                                                    <div class="col-lg-4 col-sm-8">
                                                        <select class="form-control" name="ShippingCountryCode" id="ShippingCountryCode" required="required">
                                                            <option value="" selected="selected">Choose a country</option>
                                                            <option value="US">United States</option>
                                                            <option value="GB">United Kingdom</option>
                                                            <option value="AF">Afghanistan</option>
                                                            <option value="AX">Aland Islands</option>
                                                            <option value="AL">Albania</option>
                                                            <option value="DZ">Algeria</option>
                                                            <option value="AS">American Samoa</option>
                                                            <option value="AD">Andorra</option>
                                                            <option value="AO">Angola</option>
                                                            <option value="AI">Anguilla</option>
                                                            <option value="AQ">Antarctica</option>
                                                            <option value="AG">Antigua &amp; Barbuda</option>
                                                            <option value="AR">Argentina</option>
                                                            <option value="AM">Armenia</option>
                                                            <option value="AW">Aruba</option>
                                                            <option value="AU">Australia</option>
                                                            <option value="AT">Austria</option>
                                                            <option value="AZ">Azerbaijan</option>
                                                            <option value="BS">Bahamas</option>
                                                            <option value="BH">Bahrain</option>
                                                            <option value="BD">Bangladesh</option>
                                                            <option value="BB">Barbados</option>
                                                            <option value="BY">Belarus</option>
                                                            <option value="BE">Belgium</option>
                                                            <option value="BZ">Belize</option>
                                                            <option value="BJ">Benin</option>
                                                            <option value="BM">Bermuda</option>
                                                            <option value="BT">Bhutan</option>
                                                            <option value="BO">Bolivia</option>
                                                            <option value="BA">Bosnia &amp; Herzegovina</option>
                                                            <option value="BW">Botswana</option>
                                                            <option value="BV">Bouvet Island</option>
                                                            <option value="BR">Brazil</option>
                                                            <option value="IO">British Indian Ocean Territory</option>
                                                            <option value="BN">Brunei Darussalam</option>
                                                            <option value="BG">Bulgaria</option>
                                                            <option value="BF">Burkina Faso</option>
                                                            <option value="BI">Burundi</option>
                                                            <option value="KH">Cambodia</option>
                                                            <option value="CM">Cameroon</option>
                                                            <option value="CA">Canada</option>
                                                            <option value="CV">Cape Verde</option>
                                                            <option value="KY">Cayman Islands</option>
                                                            <option value="CF">Central African Rep</option>
                                                            <option value="TD">Chad</option>
                                                            <option value="CL">Chile</option>
                                                            <option value="CN">China</option>
                                                            <option value="CX">Christmas Island</option>
                                                            <option value="CC">Cocos (Keeling) Islands</option>
                                                            <option value="CO">Colombia</option>
                                                            <option value="KM">Comoros</option>
                                                            <option value="CG">Congo</option>
                                                            <option value="CK">Cook Islands</option>
                                                            <option value="CR">Costa Rica</option>
                                                            <option value="CI">Côte d'Ivoire</option>
                                                            <option value="HR">Croatia</option>
                                                            <option value="CU">Cuba</option>
                                                            <option value="CY">Cyprus</option>
                                                            <option value="CZ">Czech Republic</option>
                                                            <option value="CD">Dem Rep of Congo (Zaire)</option>
                                                            <option value="DK">Denmark</option>
                                                            <option value="DJ">Djibouti</option>
                                                            <option value="DM">Dominica</option>
                                                            <option value="DO">Dominican Republic</option>
                                                            <option value="EC">Ecuador</option>
                                                            <option value="EG">Egypt</option>
                                                            <option value="SV">El Salvador</option>
                                                            <option value="GQ">Equatorial Guinea</option>
                                                            <option value="ER">Eritrea</option>
                                                            <option value="EE">Estonia</option>
                                                            <option value="ET">Ethiopia</option>
                                                            <option value="FK">Falkland Islands (Malvinas)</option>
                                                            <option value="FO">Faeroe Islands</option>
                                                            <option value="FJ">Fiji</option>
                                                            <option value="FI">Finland</option>
                                                            <option value="FR">France</option>
                                                            <option value="GF">French Guiana</option>
                                                            <option value="PF">French Polynesia/Tahiti</option>
                                                            <option value="TF">French Southern Territories</option>
                                                            <option value="GA">Gabon</option>
                                                            <option value="GM">Gambia</option>
                                                            <option value="GE">Georgia</option>
                                                            <option value="DE">Germany</option>
                                                            <option value="GH">Ghana</option>
                                                            <option value="GI">Gibraltar</option>
                                                            <option value="GR">Greece</option>
                                                            <option value="GL">Greenland</option>
                                                            <option value="GD">Grenada</option>
                                                            <option value="GP">Guadeloupe</option>
                                                            <option value="GU">Guam</option>
                                                            <option value="GT">Guatemala</option>
                                                            <option value="GG">Guernsey</option>
                                                            <option value="GN">Guinea</option>
                                                            <option value="GW">Guinea-Bissau</option>
                                                            <option value="GY">Guyana</option>
                                                            <option value="HT">Haiti</option>
                                                            <option value="HM">Heard Island &amp; McDonald Islands</option>
                                                            <option value="VA">Holy See (Vatican City State)</option>
                                                            <option value="HN">Honduras</option>
                                                            <option value="HK">Hong Kong</option>
                                                            <option value="HU">Hungary</option>
                                                            <option value="IS">Iceland</option>
                                                            <option value="IN">India</option>
                                                            <option value="ID">Indonesia</option>
                                                            <option value="IR">Iran</option>
                                                            <option value="IQ">Iraq</option>
                                                            <option value="IE">Ireland</option>
                                                            <option value="IM">Isle of Man</option>
                                                            <option value="IL">Israel</option>
                                                            <option value="IT">Italy</option>
                                                            <option value="CI">Ivory Coast</option>
                                                            <option value="JM">Jamaica</option>
                                                            <option value="JP">Japan</option>
                                                            <option value="JE">Jersey</option>
                                                            <option value="JO">Jordan</option>
                                                            <option value="KZ">Kazakhstan</option>
                                                            <option value="KE">Kenya</option>
                                                            <option value="KI">Kiribati</option>
                                                            <option value="KP">Korea, Democratic Republic of</option>
                                                            <option value="KR">Korea, Republic of</option>
                                                            <option value="KW">Kuwait</option>
                                                            <option value="KG">Kyrgyzstan</option>
                                                            <option value="LA">Laos</option>
                                                            <option value="LV">Latvia</option>
                                                            <option value="LB">Lebanon</option>
                                                            <option value="LS">Lesotho</option>
                                                            <option value="LR">Liberia</option>
                                                            <option value="LY">Libya</option>
                                                            <option value="LI">Liechtenstein</option>
                                                            <option value="LT">Lithuania</option>
                                                            <option value="LU">Luxembourg</option>
                                                            <option value="MO">Macau</option>
                                                            <option value="MK">Macedonia</option>
                                                            <option value="MG">Madagascar</option>
                                                            <option value="MW">Malawi</option>
                                                            <option value="MY">Malaysia</option>
                                                            <option value="MV">Maldives</option>
                                                            <option value="ML">Mali</option>
                                                            <option value="MT">Malta</option>
                                                            <option value="MH">Marshall Islands</option>
                                                            <option value="MQ">Martinique</option>
                                                            <option value="MR">Mauritania</option>
                                                            <option value="MU">Mauritius</option>
                                                            <option value="MX">Mexico</option>
                                                            <option value="FM">Micronesia</option>
                                                            <option value="MD">Moldova</option>
                                                            <option value="MC">Monaco</option>
                                                            <option value="MN">Mongolia</option>
                                                            <option value="MS">Montserrat</option>
                                                            <option value="MA">Morocco</option>
                                                            <option value="MZ">Mozambique</option>
                                                            <option value="MM">Myanmar</option>
                                                            <option value="NA">Namibia</option>
                                                            <option value="NR">Nauru</option>
                                                            <option value="NP">Nepal</option>
                                                            <option value="NL">Netherlands</option>
                                                            <option value="AN">Netherlands Antilles</option>
                                                            <option value="NC">New Caledonia</option>
                                                            <option value="NZ">New Zealand</option>
                                                            <option value="NI">Nicaragua</option>
                                                            <option value="NE">Niger</option>
                                                            <option value="NG">Nigeria</option>
                                                            <option value="NU">Niue</option>
                                                            <option value="NF">Norfolk Island</option>
                                                            <option value="MP">Northern Mariana Islands</option>
                                                            <option value="NO">Norway</option>
                                                            <option value="OM">Oman</option>
                                                            <option value="PK">Pakistan</option>
                                                            <option value="PW">Palau</option>
                                                            <option value="PS">Palestinian Territory</option>
                                                            <option value="PA">Panama</option>
                                                            <option value="PG">Papua New Guinea</option>
                                                            <option value="PY">Paraguay</option>
                                                            <option value="PE">Peru</option>
                                                            <option value="PH">Philippines</option>
                                                            <option value="PN">Pitcairn</option>
                                                            <option value="PL">Poland</option>
                                                            <option value="PT">Portugal</option>
                                                            <option value="PR">Puerto Rico</option>
                                                            <option value="QA">Qatar</option>
                                                            <option value="RE">Reunion Is.</option>
                                                            <option value="RO">Romania</option>
                                                            <option value="RU">Russia</option>
                                                            <option value="RW">Rwanda</option>
                                                            <option value="SH">Saint Helena</option>
                                                            <option value="KN">Saint Kitts &amp; Nevis</option>
                                                            <option value="LC">Saint Lucia</option>
                                                            <option value="PM">Saint Pierre &amp; Miquelon</option>
                                                            <option value="VC">Saint Vincent &amp; Grenadines</option>
                                                            <option value="AS">Samoa (Amer.)</option>
                                                            <option value="WS">Samoa (Western)</option>
                                                            <option value="SM">San Marino</option>
                                                            <option value="KN">Sao Tome &amp; Principe</option>
                                                            <option value="SA">Saudi Arabia</option>
                                                            <option value="SN">Senegal</option>
                                                            <option value="CS">Serbia &amp; Montenegro</option>
                                                            <option value="SC">Seychelles</option>
                                                            <option value="SL">Sierra Leone</option>
                                                            <option value="SG">Singapore</option>
                                                            <option value="SK">Slovakia</option>
                                                            <option value="SI">Slovenia</option>
                                                            <option value="SB">Solomon Islands</option>
                                                            <option value="ZA">South Africa</option>
                                                            <option value="GS">South Georgia &amp; S. Sandwich Islands</option>
                                                            <option value="ES">Spain</option>
                                                            <option value="LK">Sri Lanka</option>
                                                            <option value="SD">Sudan</option>
                                                            <option value="SR">Suriname</option>
                                                            <option value="SR">Svalbard &amp; Jan Mayen</option>
                                                            <option value="SZ">Swaziland</option>
                                                            <option value="SE">Sweden</option>
                                                            <option value="CH">Switzerland</option>
                                                            <option value="SY">Syria</option>
                                                            <option value="TW">Taiwan</option>
                                                            <option value="TJ">Tajikistan</option>
                                                            <option value="TZ">Tanzania</option>
                                                            <option value="TH">Thailand</option>
                                                            <option value="TL">Timor-Leste</option>
                                                            <option value="TG">Togo</option>
                                                            <option value="TK">Tokelau</option>
                                                            <option value="TO">Tonga</option>
                                                            <option value="TT">Trinidad &amp; Tobago</option>
                                                            <option value="TN">Tunisia</option>
                                                            <option value="TR">Turkey</option>
                                                            <option value="TM">Turkmenistan</option>
                                                            <option value="TC">Turks &amp; Caicos Islands</option>
                                                            <option value="TV">Tuvalu</option>
                                                            <option value="UG">Uganda</option>
                                                            <option value="UA">Ukraine</option>
                                                            <option value="AE">United Arab Emirates</option>
                                                            <option value="GB">United Kingdom</option>
                                                            <option value="US">United States</option>
                                                            <option value="UM">United States Minor Outlying Islands</option>
                                                            <option value="UY">Uruguay</option>
                                                            <option value="UZ">Uzbekistan</option>
                                                            <option value="VU">Vanuatu</option>
                                                            <option value="VE">Venezuela</option>
                                                            <option value="VN">Vietnam</option>
                                                            <option value="VG">Virgin Islands, British</option>
                                                            <option value="VI">Virgin Islands, US</option>
                                                            <option value="WF">Wallis &amp; Futuna Isle</option>
                                                            <option value="EH">Western Sahara</option>
                                                            <option value="YE">Yemen</option>
                                                            <option value="ZM">Zambia</option>
                                                            <option value="ZW">Zimbabwe</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="ShippingPhoneNumber">Phone Number</label>
                                                    <div class="col-lg-3 col-sm-4">
                                                        <input type="text" class="form-control" name="ShippingPhoneNumber" id="ShippingPhoneNumber" maxlength="25" />
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-3 col-sm-4 control-label" for="ShippingEmail">Email Address</label>
                                                    <div class="col-lg-6 col-sm-8">
                                                        <input type="text" class="form-control" name="ShippingEmail" id="ShippingEmail" maxlength="25" />
                                                    </div>
                                                </div>

                                            </div><!-- / Shipping Address Fields -->

                                        </div>
                                    </div>

                                    <!-- Errors panel -->
                                    <div class="panel panel-red" id="pos-panel-errors">
                                        <div class="panel-heading">PayPal Errors</div>
                                        <div class="panel-body">
                                            <p>The following errors have occurred:</p>
                                            <div id="pos-panel-errors-output"></div>
                                        </div>
                                    </div>

                                    <!-- Success panel -->
                                    <div class="panel panel-green" id="pos-panel-success">
                                        <div class="panel-heading">Payment Complete</div>
                                        <div class="panel-body">
                                            <p>Your payment has been processed; see payment details below:</p>
                                            <div id="pos-panel-success-output"></div>
                                        </div>
                                    </div>

                                </div>
                                <!-- /.col-lg-12 -->
                            </div>
                            <!-- /.row -->

                            <div class="row">
                                <div class="col-lg-12">
                                    <div id="pos-panel-submit">
                                        <button class="btn btn-primary" id="pos-submit-btn">Process Payment</button>
                                    </div>

                                    <div id="pos-panel-reset">
                                        <a class="btn btn-default" id="pos-reset-btn">Reset Form</a>
                                    </div>
                                </div>
                                <!-- /.col-lg-12 -->
                            </div>
                            <!-- /.row -->

                        </form>
                        <!-- END POS form -->

                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->

            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- Bootstrap Switch Plugin -->
    <script src="../js/bootstrap-switch.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

    <!-- Custom JavaScript -->
    <script src="../js/pos-functions.js"></script>

    <!-- Validate Credit Card Number JavaScript -->
    <script src="../js/validate-credit-card-number.js"></script>

    <!-- CardStripeData Parser JavaScript -->
    <script src="../js/parse-track-data.js"></script>

    <!-- AutoNumeric JavaScript -->
    <script src="../js/autoNumeric.js"></script>

    <!-- FormValidation.io JavaScript -->
    <script src="../js/formvalidation/formValidation.min.js"></script>
    <script src="../js/formvalidation/framework/bootstrap.min.js"></script>

    <!-- Form Validation JavaScript -->
    <script src="../js/validate.js"></script>


</body>

</html>