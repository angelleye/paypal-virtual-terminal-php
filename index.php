<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PayPal Payments Pro Virtual Terminal</title>
<style type="text/css">
<!--
@import "css/style.css";
@import "css/fieldset-styling.css";
@import "css/live-validation-styles.css";
-->
</style>
<!--[if lte IE 7]>    
<style type="text/css" media="all">    
@import "css/fieldset-styling-ie.css";    
</style>    
<![endif]-->
<script type="text/javascript" src="js/validate-credit-card-number.js"></script>
<script type="text/javascript" src="js/parse-track-data.js"></script>
<script type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" src="js/functions.js"></script>
</head>
<body onload="document.getElementById('CreditCardStripe').focus();">
<div id="FormContainer">
  <form action="direct-payment.php" method="post" name="CCForm" id="CCForm">
    <div id="PaymentInformation">
      <fieldset>
        <legend>Payment Information</legend>
        <ul>
          <li>
            <label for="NetAmount">Transaction Type</label>
            <select name="TransactionType" id="TransactionType">
              <option value="Authorization">Auth</option>
              <option value="Sale" selected="selected">Sale</option>
            </select>
          </li>
          <li>
            <label for="NetAmount">Net Order Amount</label>
            <input name="NetAmount" type="text" class="text" id="NetAmount" size="7" onchange="updateGrandTotal()" />
          </li>
          <li>
            <label for="ShippingAmount">Shipping Amount</label>
            <input name="ShippingAmount" type="text" class="text" id="ShippingAmount" size="7" onchange="updateGrandTotal()" />
          </li>
          <li>
            <label for="HandlingAmount">Handling Amount</label>
            <input name="HandlingAmount" type="text" class="text" id="HandlingAmount" size="7" onchange="updateGrandTotal()" />
          </li>
          <li>
            <label for="TaxRate">Tax Rate</label>
            <input name="TaxRate" type="text" class="text" id="TaxRate" size="4" onchange="updateSalesTax(); updateGrandTotal()"/>
            %
            <div id="TaxAmountDisplay"></div>
            <input name="TaxAmount" id="TaxAmount" type="hidden" value="" />
          </li>
          <li><strong>Grand Total </strong>
            <div id="GrandTotalDisplay"><strong>$0.00</strong></div>
            <strong>
              <input name="GrandTotal" id="GrandTotal" type="hidden" value="" />
            </strong></li>
          <li>
            <label for="InvoiceID">Invoice Number</label>
            <input name="InvoiceID" type="text" class="text" id="InvoiceID" size="35" />
          </li>
          <li>Item/Service Name
            <label for="ItemName"></label>
            <input name="ItemName" type="text" class="text" id="ItemName" size="70" />
          </li>
          <li>Notes
            <label for="Notes"></label>
            <textarea name="Notes" id="Notes" cols="45" rows="5"></textarea>
          </li>
        </ul>
      </fieldset>
    </div>
    <div id="FormSwipe">
      <fieldset>
        <legend><span>Swipe Card</span></legend>
        <ul>
          <li>
            <label for="CreditCardStripe">Swipe</label>
            <!-- onkeypress="return DisableEnterKey(event);" -->
            <input name="CreditCardStripe" type="password" class="text" id="CreditCardStripe" onkeypress="return DisableEnterKey(event);" onchange="ParseStripeData();" onblur="ClearStripeData();" onfocus="ClearStripeData()" size="35" />
          </li>
          <li>
          <em><strong>NOTE</strong>: You may purchase a USB credit card reader from <a href="http://www.usbswiper.com/usbswiper-usb-magnetic-stripe-credit-card-reader.html" target="_blank">USBSwiper</a>. </em></li>
        </ul>
      </fieldset>
    </div>
    <div id="FormCreditCardInfo">
      <fieldset>
        <legend><span>Credit Card Info</span></legend>
        <ul>
          <li>
            <label for="BillingFirstName">First Name </label>
            <input name="BillingFirstName" type="text" class="text" id="BillingFirstName" size="35" />
          </li>
          <li>
            <label for="BillingLastName">Last Name </label>
            <input name="BillingLastName" type="text" class="text" id="BillingLastName" size="35" />
          </li>
          <li>
            <label for="CreditCardType">Card Type</label>
            <select name="CreditCardType" id="CreditCardType" onchange="ToggleIssueNumber()">
              <option value="">Choose a card type...</option>
              <option value="Visa">Visa</option>
              <option value="MasterCard">Master Card</option>
              <option value="Discover">Discover</option>
              <option value="Amex">American Express</option>
              <option value="Switch">Switch</option>
              <option value="Solo">Solo</option>
            </select>
          </li>
          <li>
            <label for="CreditCardNumber">Card No. </label>
            <input name="CreditCardNumber" type="text" class="text" id="CreditCardNumber" size="35" />
          </li>
          <li>
            <label for="CreditCardExpMo">Exp Mo. </label>
            <select name="CreditCardExpMo" id="CreditCardExpMo">
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
          </li>
          <li>
            <label for="CreditCardExpYear">Exp. Year </label>
            <select name="CreditCardExpYear" id="CreditCardExpYear">
              <option value="2010">2010</option>
              <option value="2011">2011</option>
              <option value="2012">2012</option>
              <option value="2013">2013</option>
              <option value="2014">2014</option>
              <option value="2015">2015</option>
              <option value="2016">2016</option>
              <option value="2017">2017</option>
              <option value="2018">2018</option>
              <option value="2019">2019</option>
              <option value="2020">2020</option>
            </select>
          </li>
          <div id="DivCreditCardIssueNumber">
            <li>Issue Number
              <label for="CreditCardExpYear5"></label>
              <input name="CreditCardIssueNumber" type="text" class="text" id="CreditCardIssueNumber" size="5" maxlength="2" />
            </li>
          </div>
          <div id="DivCreditCardSecurityCode">
            <li>
              <label for="CreditCardSecurityCode">Security Code</label>
              <input type="text" class="text" name="CreditCardSecurityCode" id="CreditCardSecurityCode" size="5" />
            </li>
          </div>
        </ul>
      </fieldset>
    </div>
    <div class="checkmark">
      <label>
        <input type="checkbox" name="billingInfo" id="billingInfo" onclick="toggle('FormBillingAddress');" value="true" />
        Enter Billing Address</label>
    </div>
    <div id="FormBillingAddress">
      <fieldset>
        <legend><span>Billing Address</span></legend>
        <ul>
          <li>
            <label for="BillingStreet">Street</label>
            <input type="text" class="text" name="BillingStreet" size="25" id="BillingStreet" />
          </li>
          <li>
            <label for="BillingStreet2">Street 2</label>
            <input type="text" class="text" name="BillingStreet2" size="25" id="BillingStreet2" />
          </li>
          <li>
            <label for="BillingCity">City</label>
            <input type="text" class="text" name="BillingCity" id="BillingCity" size="25" />
          </li>
          <li>
            <label for="BillingState">State</label>
            <select name="BillingState" id="BillingState" >
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
          </li>
          <li>
            <label for="BillingPostalCode">Postal Code</label>
            <input type="text" class="text" name="BillingPostalCode" id="BillingPostalCode" size="25" />
          </li>
          <li>
            <label for="BillingCountryCode">Country</label>
            <select name="BillingCountryCode" id="BillingCountryCode" >
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
          </li>
          <li>
            <label for="BillingPhoneNumber">Phone Number</label>
            <input type="text" class="text" name="BillingPhoneNumber" id="BillingPhoneNumber" size="25" />
          </li>
          <li>
            <label for="BillingEmail">Email Address</label>
            <input type="text" class="text" name="BillingEmail" id="BillingEmail" size="25" />
          </li>
        </ul>
      </fieldset>
    </div>
    <div class="checkmark">
      <label>
        <input type="checkbox" name="shippingDisabled" id="shippingDisabled" onclick="ToggleShippingDisabled()" value="true" />
        Shipping Not Required</label>
    </div>
    <div class="checkmark">
      <label>
        <input type="checkbox" name="shippingInfo" id="shippingInfo" onclick="toggle('FormShippingAddress');" value="true" />
        Enter Shipping Address (If different from billing)</label>
    </div>
    <div id="FormShippingAddress">
      <fieldset>
        <legend><span>Shipping Address</span></legend>
        <ul>
          <li>
            <label for="ShippingFirstName">First Name</label>
            <input name="ShippingFirstName" class="text" type="text" id="ShippingFirstName" size="25" />
          </li>
          <li>
            <label for="ShippingLastName">Last Name</label>
            <input name="ShippingLastName" class="text" type="text" id="ShippingLastName" size="25" />
          </li>
          <li>
            <label for="ShippingStreet">Street</label>
            <input type="text" class="text" name="ShippingStreet" size="25" id="ShippingStreet" />
          </li>
          <li>
            <label for="ShippingStreet2">Street 2</label>
            <input type="text" class="text" name="ShippingStreet2" size="25" id="ShippingStreet2" />
          </li>
          <li>
            <label for="ShippingCity">City</label>
            <input type="text" class="text" name="ShippingCity" id="ShippingCity" size="25" />
          </li>
          <li>
            <label for="ShippingState">State</label>
            <select name="ShippingState" id="ShippingState" >
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
          </li>
          <li>
            <label for="ShippingPostalCode">Postal Code</label>
            <input type="text" class="text" name="ShippingPostalCode" id="ShippingPostalCode" size="25" />
          </li>
          <li>
            <label for="ShippingCountryCode">Country</label>
            <select name="ShippingCountryCode" id="ShippingCountryCode" >
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
          </li>
          <li>
            <label for="ShippingPhoneNumber">Phone Number</label>
            <input type="text" class="text" name="ShippingPhoneNumber" id="ShippingPhoneNumber" size="25" />
          </li>
          <li>
            <label for="ShippingEmail">Email Address</label>
            <input type="text" class="text" name="ShippingEmail" id="ShippingEmail" size="25" />
          </li>
        </ul>
      </fieldset>
    </div>
    <div id="FormSubmit">
      <fieldset class="submit">
        <input class="submit" type="submit" value="Process Card" />
      </fieldset>
    </div>
  </form>
  <script language="JavaScript" type="text/javascript">
	  //You should create the validator only after the definition of the HTML form
	  var frmvalidator  = new Validator("CCForm");
	
	  frmvalidator.EnableMsgsTogether();
	  //frmvalidator.addValidation("cc_type","dontselect=0");
	  frmvalidator.addValidation("BillingFirstName","req","FIRST NAME is required");
	  frmvalidator.addValidation("BillingLastName","req","LAST NAME is required");
	  
	  frmvalidator.addValidation("CreditCardType","dontselect=0");
	  frmvalidator.addValidation("CreditCardNumber","regexp=^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$","CREDIT CARD NUMBER is requried.");
	 // frmvalidator.addValidation("CreditCardExpMo","dontselect=0");
	  //frmvalidator.addValidation("CreditCardExpYear","dontselect=0");
	  //frmvalidator.addValidation("CreditCardSecurityCodeIndicator","dontselect=-1");
	  //frmvalidator.addValidation("CreditCardSecurityCode","req","CREDIT CARD SECURITY CODE is required");
	  
	  //frmvalidator.addValidation("BillingPostalCode","req","BILLING POSTAL CODE is required");
	  
	  frmvalidator.addValidation("Amount","req","AMOUNT is required");
	  
	</script>
    <hr />
  <a href="http://www.angelleye.com/download-angell-eye-php-class-library-for-paypal/"><p align="center" style="font-size:10px;">Powered by Angell EYE PHP PayPal Library<br />
  <img border="0" src="images/logo.png" alt="Angell EYE Consulting Services" /></p></a>
</div>
</body>
</html>