$(function() {

    $(document).ready(function(){

        /* Auto hide Card Issue Number field div */
        $('#DivCreditCardIssueNumber').hide();

        /* Auto focus on swipe field if present */
        if($('#pos-panel-swipe').length > 0)
        {
            $('#swiper').focus();
        }
        else
        {
            $('#BillingFirstName').focus();
        }

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

       /* Toggle Shipping Fields */
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
                if(!$('#shippingSameAsBilling').bootstrapSwitch('state')) {
                    $('#FormShippingAddress').slideDown('400');
                }
                else
                {
                }
            }
            return false;
        });

        /* Toggle Shipping Fields */
        $('input[name="shippingSameAsBilling"]').on('switchChange.bootstrapSwitch', function(event, state) {
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

        /* Swipe field */
        $('#swiper').change(function(){
            ParseStripeData();
        });

        $('#swiper').focus(function(){
            ClearStripeData();
        });

        $('#swiper').blur(function(){
            BlurStripeField();
        });

        /* Toggle Issue Number on Credit Card Type change */
        $('#CreditCardType').change(function(){
            ToggleIssueNumber();
        });

        $('#pos-reset-btn').on('click', function (e) {
            if( $('#pos-submit-btn').is(':visible') )
            {
                $('#posResetConfirmModal')
                    .modal({backdrop: 'static', keyboard: false})
                    .one('click', '#resetPos', function (e) {
                        //reset function
                        window.location = 'index.php';
                    });
            }
            else
            {
                window.location = 'index.php';
            }
            return false;
        });

        /* Toggle defaults on checkboxes */
        window.setTimeout(function(){
            if($('#billingInfo').attr('data-default-checked') != 'TRUE')
            {
                $('#billingInfo').bootstrapSwitch('toggleState');
            }
            if($('#shippingDisabled').attr('data-default-checked') == 'TRUE')
            {
                if($('#shippingSameAsBilling').attr('data-default-checked') == 'TRUE')
                {
                    $('#shippingSameAsBilling').bootstrapSwitch('state', true, true);
                }
                $('#shippingDisabled').bootstrapSwitch('toggleState');
            }
            else
            {
                if($('#shippingSameAsBilling').attr('data-default-checked') == 'TRUE' && $('#shippingDisabled').attr('data-default-checked') != 'TRUE')
                {
                    $('#shippingSameAsBilling').bootstrapSwitch('toggleState');
                }
            }
        }, 700);

        if($('#shippingDisabled').is(':checked'))
        {
            $('#ShippingFirstName, #ShippingLastName, #ShippingStreet, #ShippingCity, #ShippingState, #ShippingCountryCode, #ShippingPostalCode').removeAttr('required');
            $('#FormShippingAddress').hide();
            $('#sameAsBilling').hide();
        }

        if($('#sameAsBilling').is(':checked'))
        {
            $('#ShippingFirstName, #ShippingLastName, #ShippingStreet, #ShippingCity, #ShippingState, #ShippingCountryCode, #ShippingPostalCode').removeAttr('required');
            $('#FormShippingAddress').hide();
        }

    });
});

document.onkeydown = function(e) {
    var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
    if(key == 13) {
        e.preventDefault();
        if(document.activeElement.name == 'swiper' && $('#swiper').val() != '')
        {
            BlurStripeField();
        }
        else
        {
            var currentInput = document.activeElement;
            var inputs = $(currentInput).closest('form').find(':input:visible');
            inputs.eq( inputs.index(currentInput)+ 1 ).focus();
        }
        return false;
    }
};

$('#Notes').keydown( function(e) {
    var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
    if (key == 13) {
        e.preventDefault();
    }
});

/* Validate Credit Card Number field */
function ValidateCreditCardNumber()
{
    var CardNo = $('#CreditCardNumber').val();
    var CardType = $('#CreditCardType').val();
    return (checkCreditCard(CardNo,CardType)) ? true : false;
}

/* Clear data from card stripe swiped */
function ClearStripeData() {
    var TrackData = $('#swiper');
    TrackData.val('');
}

/* Blur swipe field */
function BlurStripeField() {
    if($('#swiper').val() != '')
    {
        $('#CreditCardSecurityCode').focus();
        ClearStripeData();
    }
}

/* Parse data from card stripe swiped */
function ParseStripeData() {
    var TrackData = $('#swiper').val();
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
    return false;
}

/* Update Sales Tax */
function updateSalesTax()
{
    var currencySign = $('#ae-paypal-pos-form').attr('data-currency-sign');
    var taxAmount = ( $('#TaxRate').val().replace(/,/g, '') / 100 ) * $('#NetAmount').val().replace(/,/g, '');
    if(!taxAmount) taxAmount = 0;
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