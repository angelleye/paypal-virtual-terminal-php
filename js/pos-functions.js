$(function() {

    $(document).ready(function(){

        /* Auto hide Card Issue Number field div */
        $('#DivCreditCardIssueNumber').hide();

        /* Bootstrap Switch Plugin on checkboxes for POS form */
        if($('#ae-paypal-pos-form').html().length != 0)
        {
            $("#ae-paypal-pos-form .checkbox").each(function() {
                $(this).bootstrapSwitch();
            });
            //return false;
        }

        /* AutoNumeric JavaScript plugin */
        $('#NetAmount, #ShippingAmount, #HandlingAmount').autoNumeric('init',
            {
                mDec: '2',
                aSign: '',
                wEmpty: '0',
                lZero: 'allow',
                aForm: false,
                vMin: '0'
            }
        );

        /* Toggle Billing Fields */
        $('input[name="billingInfo"]').on('switchChange.bootstrapSwitch', function(event, state) {
            //console.log(this); // DOM element
            //console.log(event); // jQuery event
            //console.log(state); // true | false
            if(state)
            {
                $('#BillingStreet, #BillingCity, #BillingState, #BillingCountryCode, #BillingPostalCode').attr('required', 'required');
            }
            else
            {
                $('#BillingStreet, #BillingCity, #BillingState, #BillingCountryCode, #BillingPostalCode').removeAttr('required');
            }
            $('#FormBillingAddress').slideToggle('400');
            return false;
        });

        /* Toggle Billing Fields */
        $('input[name="shippingDisabled"]').on('switchChange.bootstrapSwitch', function(event, state) {
            //console.log(this); // DOM element
            //console.log(event); // jQuery event
            //console.log(state); // true | false
            if(state) {
                $('#ShippingFirstName, #ShippingLastName, #ShippingStreet, #ShippingCity, #ShippingState, #ShippingCountryCode, #ShippingPostalCode').removeAttr('required');
                $('#FormShippingAddress').slideUp('400');
                $('#sameAsBilling').hide();
            }
            else
            {
                $('#sameAsBilling').show();
                //$('#ShippingFirstName, #ShippingLastName, #ShippingStreet, #ShippingCity, #ShippingState, #ShippingCountryCode, #ShippingPostalCode').attr('required', 'required');
                if(!$('#shippingInfo').bootstrapSwitch('state')) {
                    $('#FormShippingAddress').slideDown('400');
                }
                else
                {
                }
            }
            return false;
        });

        /* Toggle Billing Fields */
        $('input[name="shippingInfo"]').on('switchChange.bootstrapSwitch', function(event, state) {
            //console.log(this); // DOM element
            //console.log(event); // jQuery event
            //console.log(state); // true | false
            if(state)
            {
                $('#ShippingFirstName, #ShippingLastName, #ShippingStreet, #ShippingCity, #ShippingState, #ShippingCountryCode, #ShippingPostalCode').removeAttr('required');
            }
            else
            {
                $('#ShippingFirstName, #ShippingLastName, #ShippingStreet, #ShippingCity, #ShippingState, #ShippingCountryCode, #ShippingPostalCode').attr('required', 'required');
            }
            $('#FormShippingAddress').slideToggle('400');
            return false;
        });

        /* Update Tax Amount and Grand Total on change */
        $('#NetAmount, #ShippingAmount, #HandlingAmount, #TaxRate').change(function(){
            updateSalesTax();
            updateGrandTotal();
        });

        /* Toggle Issue Number on Credit Card Type change */
        $('#CreditCardType').change(function(){
            ToggleIssueNumber();
        });

        /* Validate Credit Card Number */
        $('#CreditCardNumber').change(function(){
            //ValidateCreditCardNumber();
        });

        // Submit offer form
        /*$('#ae-paypal-pos-form').submit(function() {
            alert('submit');
            return false;
        });*/

    });
});

/* Validate Credit Card Number field */
function ValidateCreditCardNumber()
{
    var CardNo = $('#CreditCardNumber').val();
    var CardType = $('#CreditCardType').val();
    return (checkCreditCard(CardNo,CardType)) ? true : false;
}

/* Parse data from card stripe swiped */
function ParseStripeData()
{
    var TrackData = $('%CreditCardStripe').val();
    var p = new SwipeParserObj(TrackData);

    if(p.hasTrack1)
    {
        // Populate form fields using track 1 data
        var CardType = null;

        if(p.account.charAt(0) == 4)
            CardType = 'Visa';
        else if(p.account.charAt(0) == 5)
            CardType = 'MasterCard';
        else if(p.account.charAt(0) == 3)
            CardType = 'Amex';
        else if(p.account.charAt(0) == 6)
            CardType = 'Discover';
        else
            CardType = 'Visa';

        $('#BillingFirstName').val(p.firstname);
        $('#BillingLastName').val(p.surname);
        $('#CreditCardExpMo').val(p.exp_month);
        $('#CreditCardExpYear').val(p.exp_year);
        $('#CreditCardNumber').val(p.account);
        $('#CreditCardType').val(CardType);
    }
    else
    {
        $('#BillingFirstName').val('');
        $('#BillingLastName').val('');
        $('#CreditCardExpMo').val('');
        $('#CreditCardExpYear').val('');
        $('#CreditCardNumber').val('');
        $('#CreditCardType').val('');
    }

    ToggleIssueNumber();
    ToggleSecurityCode();
}

/* Toggle Issue Number */
function ToggleIssueNumber()
{
    var creditCardType = $('#CreditCardType').val();
    if( creditCardType == 'Solo' || creditCardType == 'Switch')
    {
        $('#DivCreditCardIssueNumber').show();
        $('#CreditCardIssueNumber').attr('required', 'required');
    }
    else
    {
        $('#DivCreditCardIssueNumber').hide();
        $('#CreditCardIssueNumber').removeAttr('required');
    }
}

/* Toggle Security Code */
function ToggleSecurityCode()
{
    if($('#CreditCardSecurityCodeIndicator').val() == 1)
    {
        $('#DivCreditCardSecurityCode').show();
    }
    else
    {
        $('#DivCreditCardSecurityCode').hide();
    }
}

/* Update Sales Tax */
function updateSalesTax()
{
    var currencySign = $('#ae-paypal-pos-form').attr('data-currency-sign');
    var taxAmount = ( $('#TaxRate').val().replace(/,/g, '') / 100 ) * $('#NetAmount').val().replace(/,/g, '');
    $('#TaxAmountDisplay').html('<i>(' + currencySign + ' ' + roundNumber(taxAmount, 2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + ')</i>');
    var taxAmountRounded = roundNumber(taxAmount,2);
    $('#TaxAmount').val(taxAmountRounded);
    return false;
}

/* Update Grand Total */
function updateGrandTotal()
{
    var currencySign = $('#ae-paypal-pos-form').attr('data-currency-sign');
    var netAmount = ( $('#NetAmount').val().replace(/,/g, '') * 1 );
    var shippingAmount = ( $('#ShippingAmount').val().replace(/,/g, '') * 1 );
    var handlingAmount = ( $('#HandlingAmount').val().replace(/,/g, '') * 1 );
    var taxAmount = $('#TaxAmount').val().replace(/,/g, '') * 1;
    var grandTotal = (netAmount + shippingAmount + handlingAmount + taxAmount);
    var grandTotal = grandTotal.toFixed(2);
    $('#GrandTotalDisplay').html('<strong>' + currencySign + ' ' + grandTotal.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</strong>');
    $('#GrandTotal').val(grandTotal);
    return false;
}

/* Round Number */
function roundNumber(num, dec)
{
    var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
    return result.toFixed(2);
}