var Login = function() {

    var handleLogin = function() {
        var form = $('.login-form'); 
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                username: {
                    required: true
                },
                password: {
                    required: true
                },
                remember: {
                    required: false
                }
            },

            messages: {
                username: {
                    required: "Username is required."
                },
                password: {
                    required: "Password is required."
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
                error.insertAfter(element.closest('.form-group'));
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
                $('.login-form .help-block, .login-form .alert').remove();
                
                // callback handler that will be called on success
                request.done(function (response, textStatus, jqXHR) {
                    var result = $.parseJSON(response);
                    if(result.success) {
                        toastr.success(result.msg);
                        setTimeout(function(){ window.location.href = full_url+'/'+result.redirect; }, 1500);
                    } else {
                        toastr.error(result.msg);
                    }
                });
                
                // callback handler that will be called on failure
                request.fail(function (jqXHR, textStatus, errorThrown) {
                    // log the error to the console
                    toastr.error("The following error occured: " + textStatus, errorThrown);
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
            var bg1 = handleRandom(1,15);
            var bg2 = handleRandom(16,30);
            var bg3 = handleRandom(31,45);
            var bg4 = handleRandom(46,60);
            // init background slide images
		    if (jQuery().backstretch) {
    		    $.backstretch([
    		        full_url + "/assets/img/background/"+bg1+".jpg",
    		        full_url + "/assets/img/background/"+bg2+".jpg",
    		        full_url + "/assets/img/background/"+bg3+".jpg",
    		        full_url + "/assets/img/background/"+bg4+".jpg"
    		        ], {
    		          fade: 1000,
    		          duration: 8000
    		    	}
            	);
            }
        }

    };

}();

jQuery(document).ready(function() {
    Login.init();
});