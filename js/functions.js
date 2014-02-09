function DisableEnterKey(e)
{   
	var key;     
    if(window.event)
         key = window.event.keyCode; //IE
    else
         key = e.which; //firefox
	  
    return (key != 13);
}

function checkItem(e)
{
	if (window.event)
	{
		if (event.keyCode==13) //trap enter
		{
			//if not textarea type
			if(document.activeElement.type!='textarea')
			{ 
				event.keyCode=9; 
			} //convert to Tab key
		}
		return event.keyCode;    
	}
			
	if (e.keyCode) 
		code = e.keyCode;
	else if (e.which) 
		code = e.which;
	
	if (code==13)
	{ 
		document.getElementById('CreditCardSecurityCode').focus();
		return false;
	}
}
        
document.onkeydown=checkItem;

function ValidateCreditCardNumber()
{
	var CardNo = document.getElementById('CreditCardNumber').value;
	var CardType = document.getElementById('CreditCardType').value;
	if(checkCreditCard(CardNo,CardType))
	{
		return true;	
	}
	else
	{
		return false;	
	}
}

function ClearStripeData()
{
	document.getElementById('CreditCardStripe').value = '';	
}

function BlurStripeField()
{
	document.getElementById('CreditCardSecurityCode').focus();
}

function ParseStripeData()
{	
	var TrackData = document.getElementById('CreditCardStripe').value;
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
					
		document.getElementById('BillingFirstName').value = p.firstname;
		document.getElementById('BillingLastName').value = p.surname;
		document.getElementById('CreditCardExpMo').value = p.exp_month;
		document.getElementById('CreditCardExpYear').value = p.exp_year;
		document.getElementById('CreditCardNumber').value = p.account;
		document.getElementById('CreditCardType').value = CardType;
	}
	else
	{
		document.getElementById('BillingFirstName').value = '';
		document.getElementById('BillingLastName').value = '';
		document.getElementById('CreditCardExpMo').value = '';
		document.getElementById('CreditCardExpYear').value = '';
		document.getElementById('CreditCardNumber').value = '';
		document.getElementById('CreditCardType').value = '';	
	}
	
	ToggleIssueNumber();
	ToggleSecurityCode();
}

function HideDiv(DivID)
{
	document.getElementById(DivID).style.display = 'none';	
}

function ShowDiv(DivID)
{
	document.getElementById(DivID).style.display = 'block';		
}

// I wrote this because it needs to do both ~ Corey
function toggle(id) {
       var e = document.getElementById(id);
       if(e.style.display == 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';
}


function ToggleIssueNumber()
{
	if(document.getElementById('CreditCardType').value == 'MD' || document.getElementById('CreditCardType').value == 'SO' || document.getElementById('CreditCardType').value == 'SW')
	{
		ShowDiv('DivCreditCardIssueNumber');	
	}
	else
	{
		HideDiv('DivCreditCardIssueNumber');	
	}
}

function ToggleSecurityCode()
{
	if(document.getElementById('CreditCardSecurityCodeIndicator').value == 1)
	{
		ShowDiv('DivCreditCardSecurityCode');	
	}
	else
	{
		HideDiv('DivCreditCardSecurityCode');	
	}
}

function ToggleShippingDisabled()
{
	if(document.getElementById('shippingDisabled').checked == true)
	{
		document.getElementById('shippingInfo').disabled = true;
	}
	else
	{
		document.getElementById('shippingInfo').disabled = false;	
	}
}

function updateSalesTax()
{
	taxAmount = (document.getElementById('TaxRate').value / 100) * document.getElementById('NetAmount').value;
	document.getElementById('TaxAmountDisplay').innerHTML = '<i>($' + roundNumber(taxAmount,2) + ')</i>';
	taxAmountRounded = roundNumber(taxAmount,2);
	document.getElementById('TaxAmount').value = taxAmountRounded;
}

function updateGrandTotal()
{
	netAmount = document.getElementById('NetAmount').value * 1;
	shippingAmount = document.getElementById('ShippingAmount').value * 1;
	handlingAmount = document.getElementById('HandlingAmount').value * 1;
	taxAmount = document.getElementById('TaxAmount').value * 1;
	grandTotal = netAmount + shippingAmount + handlingAmount + taxAmount;
	grandTotal = grandTotal.toFixed(2);

	document.getElementById('GrandTotalDisplay').innerHTML = '<strong>$' + grandTotal + '</strong>';
	document.getElementById('GrandTotal').value = grandTotal;
}

function roundNumber(num, dec) 
{
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result.toFixed(2);
}