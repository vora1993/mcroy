$('#photoupload').on("change", function(){
  var form = document.getElementById('form_credit_card');
  var fileInput = document.getElementById('photoupload');
  var file = fileInput.files[0];
  var formData = new FormData();
  formData.append('file', file);
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if (xhr.readyState == 4) {
      var result = $.parseJSON(xhr.responseText);
      if(result.success === true) {
        toastr.success(result.msg);
        $('#photo').attr("src", full_url + result.src);
        $('#logo').val(result.name);
      } else {
        toastr.warning(result.msg);
      }
    }
  }
    // Add any event handlers here...
  xhr.open('POST', full_url + "/admin/providers/change-logo", true);
  xhr.send(formData);
});

var form = $('#form_credit_card');
form.validate({
  errorElement: 'span', //default input error message container
  errorClass: 'help-block', // default input error message class
  focusInvalid: true, // do not focus the last invalid input
  ignore: "",
  rules: {
    name: {
      required: true
    },
    apply_url: {
      url: true
    },
    provider_id: {
      required: true
    },
    bank_id: {
      required: true
    }
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
    error.insertAfter(element);
  },

  submitHandler: function (form) {
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
    $('#form_credit_card .form-group').removeClass('has-error');
    $('#form_credit_card .help-block, #form_credit_card .alert').remove();

        // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
      var result = $.parseJSON(response);
      if(result.error) {
        toastr.error("Something error. Please check");
      } else {
        if(result.success) {
          toastr.success(result.msg);
          setTimeout(function(){ window.location.href = full_url+'/admin/providers'; }, 1500);
        } else {
          toastr.warning(result.msg);
        }
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