$(function() {

    $(document).ready(function(){

        $('#ae-paypal-pos-form').formValidation({
            framework: 'bootstrap',
            excluded: [':disabled', ':hidden', ':not(:visible)'],
            focusInvalid: false,
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
            .on('err.form.fv', function(e) {
                $('html, body').animate({
                    scrollTop: $('.has-error:first').offset().top - 20
                }, '400');

                // focus first error input
                $('.has-error:first').find(':input').focus();
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

                $('#pos-panel-success').hide();
                $('#pos-panel-errors').hide();

                var $form = $(e.target),
                    fv    = $form.data('formValidation');

                // VALID FORM - OK TO SUBMIT

                // disable/animate process button
                $('#pos-submit-btn').attr('disabled', 'disabled');
                $('#pos-submit-btn').html('<i class="fa fa-spinner fa-spin"></i>&nbsp;Processing...');

                // Use Ajax to submit form data
                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        var responseData = $.parseJSON(response);

                        //console.log(responseData);
                        //return false;

                        if ( "error" != responseData.result )
                        {
                            // reset and hide process button
                            $('#pos-submit-btn').removeAttr('disabled');
                            $('#pos-submit-btn').html('Process Payment');
                            $('#pos-submit-btn').hide();
                            $('#ae-paypal-pos-input-panels').hide();
                            $('#pos-intro-alert-msg').hide();
                            // show success panel
                            $('#pos-panel-success').slideDown(400);
                            $('#pos-panel-success-output').html(responseData.result_html);
                            return false;
                        }
                        else
                        {
                            $('#pos-submit-btn').removeAttr('disabled');
                            $('#pos-submit-btn').html('Process Payment');
                            // show success panel
                            $('#pos-panel-errors').slideDown(400);
                            $('#pos-panel-errors-output').html(responseData.result_data);
                            return false;
                        }
                    }
                });

            }
        );

    });
});