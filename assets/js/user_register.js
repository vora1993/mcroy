var Register = function() {

    var handleRegister = function() {
        var form = $('.register-form'); 
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                firstname: {
                    required: true,
                    minlength: 2
                },
                lastname: {
                    required: true,
                    minlength: 2
                },
                password: {
                    required: true,
                    minlength: 6    
                },
                passwordVerify: {
                    required: true,
                    equalTo: "#password"
                },
                email: {
                    required: true,
                    email: true
                },
                phone: {
                    required: true,
                    number: true
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit   
                $('.alert-danger', form).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                // form validation success, call ajax form submit
                // setup some local variables
                var form = $(form);
                
                // let's select and cache all the fields
                var inputs = form.find("input, select, button, textarea");
                
                // serialize the data in the form
                var serializedData = form.serialize();
            
                // let's disable the inputs for the duration of the ajax request
                inputs.prop("disabled", true);
                
                // fire off the request to /form.php
        
                request = $.ajax({
                    url: form.attr("action"),
                    type: "post",
                    data: serializedData
                });
                
                // Clear Message
                $('.register-form .form-group').removeClass('has-error');
                $('.register-form .help-block, .register-form .alert').hide();
                
                // callback handler that will be called on success
                request.done(function (response, textStatus, jqXHR) {
                    var result = $.parseJSON(response);
                    if(result.success) {
                        $('.alert-success > span').html(result.msg); 
                        $('.alert-danger', form).hide();
                        $('.alert-success', form).show();
                    } else {
                        $('.alert-danger > span').html(result.msg);    
                        $('.alert-danger', form).show();
                        $('.alert-success', form).hide();
                        
                        if(result.error.email){
                            $('input[name=email]', form).parent().addClass('has-error');
                            $('input[name=email]', form).after('<span class="help-block">' + result.error.email + '</span>');
                        }    
                    }
                    if(result.redirect) setTimeout(function(){ window.location.href = result.redirect; }, 1000);
                });
                
                // callback handler that will be called on failure
                request.fail(function (jqXHR, textStatus, errorThrown) {
                    // log the error to the console
                    console.log("The following error occured: " + textStatus, errorThrown);
                });
                
                // callback handler that will be called regardless
                // if the request failed or succeeded
                request.always(function () {
                    // reenable the inputs
                    inputs.prop("disabled", false);
                });
            }
        });

        $('.register-form input').keypress(function(e) {
            if (e.which == 13) {
                if (form.validate().form()) {
                    form.submit(); //form validation success, call ajax form submit
                }
                return false;
            }
        });
    }

    return {
        //main function to initiate the module
        init: function() {
            handleRegister();
        }

    };

}();

jQuery(document).ready(function() {
    Register.init();
});