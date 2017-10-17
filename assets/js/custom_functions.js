function cancel(route) {
    window.location.href = full_url + '/' + route;
}

function refresh_error() {
    if ($(".has-error").length) {
        $('html, body').animate({
            scrollTop: ($(".has-error").first().offset().top - 20)
        },500);
    }
}

function set_status(status) {
	$('input[name=status]').val(status);
}

function make_friendly_seo(s) {
    if (typeof s == "undefined") {
        return;
    }
 
    var i=0,uni1,arr1;
    var newclean=s;
    uni1 = 'à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ|À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ|A';
    arr1 = uni1.split('|');
    for (i=0; i<uni1.length; i++) newclean = newclean.replace(uni1[i],'a');
  
    uni1 = 'è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ|È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ|E';
    arr1 = uni1.split('|');
    for (i=0; i<uni1.length; i++) newclean = newclean.replace(uni1[i],'e');
  
    uni1 = 'ì|í|ị|ỉ|ĩ|Ì|Í|Ị|Ỉ|Ĩ|I';
    arr1 = uni1.split('|');
    for (i=0; i<uni1.length; i++) newclean = newclean.replace(uni1[i],'i');
    
    uni1 = 'ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ|Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ|O';
    arr1 = uni1.split('|');
    for (i=0; i<uni1.length; i++) newclean = newclean.replace(uni1[i],'o');
 
    uni1 = 'ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ|Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ|U';
    arr1 = uni1.split('|');
    for (i=0; i<uni1.length; i++) newclean = newclean.replace(uni1[i],'u');
 
    uni1 = 'ỳ|ý|ỵ|ỷ|ỹ|Ỳ|Ý|Ỵ|Ỷ|Ỹ|Y';
    arr1 = uni1.split('|');
    for (i=0; i<uni1.length; i++) newclean = newclean.replace(uni1[i],'y');
  
    uni1 = 'd|đ|Đ|D';
    arr1 = uni1.split('|');
    for (i=0; i<uni1.length; i++) newclean = newclean.replace(uni1[i],'d');
    
    newclean = newclean.toLowerCase()
    ret = newclean.replace(/[\&]/g, '-and-').replace(/[^a-zA-Z0-9._-]/g, '-').replace(/[-]+/g, '-').replace(/-$/, '');
 
    return ret;
} 

// Bank functions 
var Bank = {
    'update_interest_rate' : function() {
        var id = $("select[name=bank_id]").val();
        var type = $("select[name=type]").val();
        if(id > 0) {
            var action = 'update-interest-rate-business-loan';
            if(type == 2) {
                action = 'update-interest-rate-property-loan';
            } 
            window.location.href = full_url + '/admin/bank/'+action+'/'+id;
        } else {
            toastr.error("Please choose a bank");
        }
    },
    'view_interest_rate' : function(button) {
        var id = $("select[name=bank_id]").val();
        var type = $("select[name=type]").val();
        if(id > 0) {
            var l = Ladda.create(button);
            var data = 'id='+id+'&type='+type;
            $.ajax({
                url: full_url + '/admin/bank/view-interest-rate',
                type: 'post',
                data: data,
                dataType: 'json',
                beforeSend: function(xhr, settings) {
                    l.start();
    			},
                success: function(json) {
                    if (json['success']) {
    				    l.stop();
                    	$('#interest-rate').html(json['html']);
    				}
                },
                error : function(xhr, ajaxOptions, thrownError){
                    toastr.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                },
            });
        } else {
            toastr.error("Please choose a bank");
        }
    }
}

jQuery(document).ready(function() {
    if (jQuery().datepicker) {
        $('.date-picker').datepicker({
            rtl: App.isRTL(),
            orientation: "left",
            autoclose: true,
        });
    }
    
    if (jQuery().timepicker) {
        $('.timepicker-24').timepicker({
            autoclose: true,
            minuteStep: 5,
            showSeconds: false,
            showMeridian: false
        });
    }
    
    if (jQuery().pulsate) {
        jQuery('.pulsate-regular').pulsate({
            color: "#bf1c56"
        });
    }
    
});