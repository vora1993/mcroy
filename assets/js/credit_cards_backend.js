tinymce.baseURL = "/assets/plugins/tinymce/js/tinymce";
tinymce.PluginManager.add('mce_mlib', function(editor, url) {
  var tinyedit_id = editor.id;
    editor.addButton('mce_mlib', {
        text: 'Insert Media',
        icon: false,
        id: 'mediabtn_'+tinyedit_id,
        icon : 'image',
        onclick: function() {
            var newid = '#mediabtn_'+tinyedit_id;
            if($(newid).attr('mboxmce_init')===undefined){
                $(newid).attr('mboxmce_init', tinyedit_id);
                $(newid).mlibready({allowed:'jpg,png,gif,jpeg,txt,zip,rar,doc,docx,ppt,pptx,xls,xlsx,csv,tar,gz', mcename:tinyedit_id, returnas:'all'});
                $(newid).trigger('click');
      }
        }
    });

    // Adds a menu item
    editor.addMenuItem('mce_mlib', {
        text: 'Insert Media',
        context: 'file',
    icon : 'image',
    id: 'mediabtn_'+tinyedit_id,
        onclick: function() {
      var newid = '#mediabtn_'+tinyedit_id;
      if($(newid).attr('mboxmce_init')===undefined){
      $(newid).attr('mboxmce_init', tinyedit_id);
      $(newid).mlibready({allowed:'jpg,png,gif,jpeg,txt,zip,rar,doc,docx,ppt,pptx,xls,xlsx,csv,tar,gz', mcename:tinyedit_id, returnas:'all'});
      $(newid).trigger('click');
      }
        }
    });
});
tinymce.init({
    selector: '.tinymce',
    height: 300,
    menubar: false,
    statusbar: false,
    //plugins: 'mce_mlib advlist table link textcolor code',
    //toolbar: 'mce_mlib | advlist | table | link | styleselect | bold italic | alignment | alignmentv2 | fontsizeselect | forecolor | backcolor | code',
    plugins: [
        'advlist autolink lists link image charmap print preview hr anchor pagebreak',
        'searchreplace wordcount visualblocks visualchars code fullscreen',
        'insertdatetime media nonbreaking save table contextmenu directionality',
        'emoticons template paste textcolor colorpicker textpattern imagetools codesample'
    ],
    toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
    toolbar2: 'print preview media | forecolor backcolor emoticons | codesample help | code',
    image_advtab: true,
    fontsize_formats: '8px 10px 12px 14px 18px 24px 36px',
    setup: function (editor) {
        editor.on('change', function () {
            editor.save();
        });
    }
});

$('input[name="cash_back"]').change(function(){
  $('.card-back-section').slideToggle();
});

$('input[name="air_miles"').change(function(){
  $('.air-miles-section').slideToggle();
});

$('input[name="discount"').change(function(){
  $('.discount-section').slideToggle();
});

$('input[name="points"').change(function(){
  $('.points-section').slideToggle();
});

$('.colorpicker').minicolors({
  theme: 'bootstrap'
});
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
  xhr.open('POST', full_url + "/admin/credit-cards/change-logo", true);
  xhr.send(formData);
});

var form = $('#form_credit_card');
form.validate({
  errorElement: 'span', //default input error message container
  errorClass: 'help-block', // default input error message class
  focusInvalid: false, // do not focus the last invalid input
  ignore: "",
  rules: {
    name: {
      required: true
    },
    apply_url: {
      url: true
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
          setTimeout(function(){ window.location.href = full_url+'/admin/credit-cards'; }, 1500);
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