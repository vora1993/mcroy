$('input[name=property_status]').on('change', function() {
 var value =$('input[type="radio"][name="property_status"]:checked').val();
 if(value === 'Completed') $(".offer_opts h4").html('{$_text_completed}');
 else  $(".offer_opts h4").html('{$_text_under_construction}');
});
$('input[name=option_fee]').on('change', function() {
 var value =$('input[type="radio"][name="option_fee"]:checked').val();
 if(value === 'not_paid') $(".offer_opts").show();
 else  $(".offer_opts").hide();
});

$('input[name="property"]').on('change', function(){
  var value =$('input[type="radio"][name="property"]:checked').val();
  if (value == 'refinancing') {
    window.location.href = base_path + '/refinancing/step/1';
  }
})

// Handle number
Loan.integer();
Loan.initEasyPieCharts();

$('input[name=purchase_price]').on('blur', function(){
  $(this).parent().removeClass('focus');
}).on('focus', function(){
  $(this).parent().addClass('focus');
});

$("input[name=purchase_price]").on("keydown", function(evt) {
  var charCode = charCode = (evt.which) ? evt.which : event.keyCode;
  if(charCode == 8 || charCode == 46 ){
    return true;
  }
  return isEnterNumber(charCode);
});

$("input[name=purchase_price]").on("keyup", function() {
  var val = $(this).val();
  val = Number(val.replace(/,/g, ""));
  $(this).val(formatNumber(val, ""));
  updateLoanAmount();
  Loan.totalCostOutlay();
});

var $slider_loan_percent = $(".slider-loan-amount").slider({
  value: $(".slider-loan-amount").data("value"),
  min: $(".slider-loan-amount").data("min"),
  max: $(".slider-loan-amount").data("max"),
  orientation: "horizontal",
  animate: !0,
  range: "min",
  create: function(event, ui){
    var current = $("input[name=loan_percent]").val();
    $(".slider-loan-amount").find(".ui-slider-handle").html('<span class="arrow_box">'+current+'</span>');
  },
  slide: function(event, ui) {
    if ("" != $(".slider-loan-amount").attr("data-soft-cap")) {
      var o = parseInt($(".slider-loan-amount").attr("data-soft-cap"));
      if (ui.value > o) return !1
    }
    $(".slider-loan-amount").find(".ui-slider-handle").html('<span class="arrow_box">'+ui.value+'</span>');
    $("#loan_amount-label").html(ui.value);
    $(".easy-pie-chart .number.loans").attr("data-percent", ui.value);
    $("input[name=loan_percent]").val(ui.value);
  },
  change: function(event, ui) {
    $(".slider-loan-amount").find(".ui-slider-handle").html('<span class="arrow_box">'+ui.value+'</span>');
    updatePieChart(ui.value);
    updateLoanAmount();
  }
});

var $slider_loan_tenture = $(".slider-loan-tenure").slider({
  value: $(".slider-loan-tenure").data("value"),
  min: $(".slider-loan-tenure").data("min"),
  max: $(".slider-loan-tenure").data("max"),
  orientation: "horizontal",
  animate: !0,
  range: "min",
  create: function(event, ui){
    var current = $("input[name=loan_tenure]").val();
    $(".slider-loan-tenure").find(".ui-slider-handle").html('<span class="arrow_box">'+current+'</span>');
  },
  slide: function(event, ui) {
    if ("" != $(".slider-loan-tenure").attr("data-soft-cap")) {
      var o = parseInt($(".slider-loan-tenure").attr("data-soft-cap"));
      if (ui.value > o) return !1
    }

    $(".slider-loan-tenure").find(".ui-slider-handle").html('<span class="arrow_box">'+ui.value+'</span>');
    $("#loan_tenure-label").html(ui.value);
    $("input[name=loan_tenure]").val(ui.value);
  },
  change: function(event, ui) {
    $(".slider-loan-tenure").find(".ui-slider-handle").html('<span class="arrow_box">'+ui.value+'</span>');
    // updateSlider();
    updatePieChartResidential($("input[name=loan_tenure").val(),$("input[name=existing_home_loans").val());
    updateLoanAmount();
  }
});
$(".slider-loan-tenure").find(".ui-slider-handle").html('<span class="arrow_box">'+$(".slider-loan-tenure").data("value")+'</span>');

var $slider_new_loan_tenture = $(".slider-new-loan-tenure").slider({
  value: $(".slider-new-loan-tenure").data("value"),
  min: $(".slider-new-loan-tenure").data("min"),
  max: $(".slider-new-loan-tenure").data("max"),
  orientation: "horizontal",
  animate: !0,
  range: "min",
  create: function(event, ui){
    var current = $("input[name=new_loan_tenure]").val();
    $(".slider-new-loan-tenure").find(".ui-slider-handle").html('<span class="arrow_box">'+current+'</span>');
  },
  slide: function(event, ui) {
    if ("" != $(".slider-new-loan-tenure").attr("data-soft-cap")) {
      var o = parseInt($(".slider-new-loan-tenure").attr("data-soft-cap"));
      if (ui.value > o) return !1
    }

    $(".slider-new-loan-tenure").find(".ui-slider-handle").html('<span class="arrow_box">'+ui.value+'</span>');
    $("#loan_tenure-label").html(ui.value);
    $("input[name=new_loan_tenure]").val(ui.value);
  },
  change: function(event, ui) {
    $(".slider-new-loan-tenure").find(".ui-slider-handle").html('<span class="arrow_box">'+ui.value+'</span>');
  }
});
$(".slider-new-loan-tenure").find(".ui-slider-handle").html('<span class="arrow_box">'+$(".slider-new-loan-tenure").data("value")+'</span>');

var form = $('.form-wizard.form-home-loan-wizard');
form.validate({
  focusInvalid: false, // do not focus the last invalid input
  ignore: "", // validate all fields including form hidden input
  rules: {
    property: {
      required: true
    },
    property_type: {
      required: true
    },
    property_status: {
      required: true
    },
    option_fee: {
      required: true
    },
    existing_home_loans: {
      required: true
    },
    purchase_price: {
      required: true
    }
  },

  invalidHandler: function(event, validator) { //display error alert on form submit
    App.scrollTo($(".error"), -15);
  },

  errorPlacement: function(error, element) {
    if(element.attr("name") == "property_type") {
      $('.property_type').addClass("error");
    }
    if(element.attr("name") == "property_status") {
      $('.property_status').addClass("error");
    }
    if(element.attr("name") == "option_fee") {
      $('.option_fee').addClass("error");
    }
    if(element.attr("name") == "purchase_price") {
      $('.purchase_price').addClass("error");
    }
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
    $('.form-group .error').removeClass('error');

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
      var result = $.parseJSON(response);
      if(result.success) {
        App.blockUI({boxed: true});
      }
      setTimeout(function(){
        App.unblockUI();
        window.location.href = result.redirect;
      }, 1500);
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
        App.blockUI({boxed: true});
        inputs.prop("disabled", false);
      });
    }
  });

var formRefinancing = $('.form-wizard.form-refinancing');

formRefinancing.validate({
  focusInvalid: false, // do not focus the last invalid input
  ignore: "", // validate all fields including form hidden input
  rules: {
    property_type: {
      required: true
    },
    current_bank_name: {
      required: true
    },
    remaining_loan_amount: {
      required: true
    },
    current_interest_rate: {
      required: true
    },
    locked_in: {
      required: true
    }
  },

  invalidHandler: function(event, validator) { //display error alert on form submit
    App.scrollTo($(".error"), -15);
  },

  errorPlacement: function(error, element) {
    if(element.attr("name") == "property_type") {
      $('.property_type').addClass("error");
    }
    if(element.attr("name") == "property_status") {
      $('.property_status').addClass("error");
    }
    if(element.attr("name") == "option_fee") {
      $('.option_fee').addClass("error");
    }
    if(element.attr("name") == "purchase_price") {
      $('.purchase_price').addClass("error");
    }
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
    $('.form-group .error').removeClass('error');

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
      var result = $.parseJSON(response);
      if(result.success) {
        App.blockUI({boxed: true});
      }
      setTimeout(function(){
        App.unblockUI();
        window.location.href = result.redirect;
      }, 1500);
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
        App.blockUI({boxed: true});
        inputs.prop("disabled", false);
      });
    }
  });

$('#expected-total-costs-outlay').click(function(){
  $('#total-costs-outlay').slideToggle(function() {
    visible = $("#total-costs-outlay").is(":visible");
    $("#expected-total-costs-outlay i").toggleClass('fa-plus', !visible);
    $("#expected-total-costs-outlay i").toggleClass('fa-minus', visible);
  });
});