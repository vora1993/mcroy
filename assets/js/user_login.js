var Login = function() {

    var handleLogin = function() {
        var form = $('.login-form'); 
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                identity: {
                    required: true
                },
                credential: {
                    required: true
                },
                remember: {
                    required: false
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
                $('.login-form .form-group').removeClass('has-error');
                $('.login-form .help-block, .login-form .alert').hide();
                
                // callback handler that will be called on success
                request.done(function (response, textStatus, jqXHR) {
                    var result = $.parseJSON(response);
                    if(result.success) {
                        $('.alert-danger', form).hide();
                        $('.alert-success > span').html(result.msg);
                        $('.alert-success', form).show();
                    } else {
                        $('.alert-success', form).hide();
                        $('.alert-danger > span').html(result.msg);   
                        $('.alert-danger', form).show();
                    }
                    if(result.redirect) {
                        setTimeout(function(){ window.location.href = result.redirect; }, 1000);
                    } else {
                        setTimeout(function(){ window.history.go(-1); }, 1000);
                    }
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

        $('.login-form input').keypress(function(e) {
            if (e.which == 13) {
                if (form.validate().form()) {
                    form.submit(); //form validation success, call ajax form submit
                }
                return false;
            }
        });
    }
    
    var handleRandom = function(min,max,interval) {
        if (typeof(interval)==='undefined') interval = 1;
        var r = Math.floor(Math.random()*(max-min+interval)/interval);
        return r*interval+min;
    }

    return {
        //main function to initiate the module
        init: function() {
            handleLogin();
        }

    };

}();

jQuery(document).ready(function() {
    Login.init();
});