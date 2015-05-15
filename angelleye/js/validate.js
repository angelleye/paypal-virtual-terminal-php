$(function() {

    $(document).ready(function(){

        $('#ae-paypal-pos-form').formValidation({
            framework: 'bootstrap',
            excluded: [':disabled', ':hidden', ':not(:visible)'],
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                NetAmount: {
                    validators: {
                        callback: {
                            callback: function(value, validator, $field) {
                                var netAmountVal = $('#NetAmount').autoNumeric('get');
                                return (netAmountVal !== '0') ? true : false;
                            }
                        }
                    }
                },
                CreditCardNumber: {
                    validators: {
                        callback: {
                            callback: function(value, validator, $field) {
                                return ValidateCreditCardNumber();
                            }
                        }
                    }
                }

            }
        })
            .on('change', '[name="CreditCardType"]', function(e) {
                var cardNumberVal = $('#CreditCardNumber').val();
                if(cardNumberVal !== '')
                {
                    $('#ae-paypal-pos-form').formValidation('revalidateField', 'CreditCardNumber');
                }
            })
            .on('change', '[name="CreditCardNumber"]', function(e) {
                $('#ae-paypal-pos-form').formValidation('revalidateField', 'CreditCardNumber');
            })
            .on('change', '[name="NetAmount"]', function(e) {
                $('#ae-paypal-pos-form').formValidation('revalidateField', 'NetAmount');
            })
            .on('success.form.fv', function(e) {
                // Prevent form submission
                e.preventDefault();

                var $form = $(e.target),
                    fv    = $form.data('formValidation');

                // VALID FORM - OK TO SUBMIT

                // Use Ajax to submit form data
                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        var responseData = $.parseJSON(response);

                        console.log(responseData);
                        return false;

                        if ( "error" != responseData.result )
                        {
                            $('#data-output-panel').hide();
                            $('#profile-cancel-submit-btn').html('Cancel Profile &amp; Refund');
                            $('#profile-cancel-submit-btn').removeAttr('disabled');

                            $('#success-output-panel').slideDown(400);
                            $('#payflow-refund-success-overview').html(responseData.result_html);
                            return false;
                        }
                        else
                        {
                            if(refundAmount == "0.00")
                            {
                                $('#profile-cancel-submit-btn').html('Cancel Profile');
                            }
                            else
                            {
                                $('#profile-cancel-submit-btn').html('Cancel Profile &amp; Refund');
                            }

                            $('#profile-cancel-submit-btn').removeAttr('disabled');

                            $('#errors-output-panel').slideDown(400);
                            $('#errors-output').html('<strong>ERROR:&nbsp;</strong>' + responseData.result_data);
                            return false;
                        }

                    }
                });
            }
        );

    });
});