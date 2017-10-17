function cancel(route) {
    window.location.href = full_url + '/' + route;
}

function formatNumber (num, strDollar) {
    return strDollar + num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
}

function formatIntNumber (str) {
    if(str) return str.replace(/,/g, '');
}

function isEnterNumber(keyCode) {
    if ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 96 && keyCode <= 105)) {
        return true;
    }
    return false;
}

function resizeFacebookComments(){
    var src   = $('.fb-comments iframe').attr('src').split('width='),
        width = $('#container').width();

    $('.fb-comments iframe').attr('src', src[0] + 'width=' + width);
}

// Loan functions 
var Loan = {
    'apply' : function(button) {
        var id = $(button).data("id");
        var loan_amount = formatIntNumber($("input[name=loan_amount]").val());
        var loan_tenure = $("input[name=loan_tenure]").val();
        if(loan_amount > 0 && loan_tenure > 0) {
            var l = Ladda.create(button);
            var data = 'id='+id+'&loan_amount='+loan_amount+'&loan_tenure='+loan_tenure;
            $.ajax({
                url: full_url + '/alternative-funding/funding-apply',
                type: 'post',
                data: data,
                dataType: 'json',
                beforeSend: function(xhr, settings) {
                    l.start();
    			},
                success: function(json) {
                    if (json['success']) {
    				    l.stop();
                    	window.location.href = full_url+json['redirect']; 
    				}
                },
                error : function(xhr, ajaxOptions, thrownError){
                    toastr.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                },
            });
        } else {
            toastr.error("Sorry you haven't selected right.");
        }
    },
    'compare' : function(button) {
        var id = $(button).data("id");
        var loan_amount = formatIntNumber($("input[name=loan_amount]").val());
        var loan_tenure = $("input[name=loan_tenure]").val();
        if(loan_amount > 0 && loan_tenure > 0) {
            var data = 'id='+id+'&loan_amount='+loan_amount+'&loan_tenure='+loan_tenure;
            $.ajax({
                url: full_url + '/alternative-funding/funding-compare',
                type: 'post',
                data: data,
                dataType: 'json',
                beforeSend: function(xhr, settings) {
                    App.blockUI({boxed: true});
    			},
                success: function(json) {
                    App.unblockUI();
                    if(json['success'] === false) toastr.error(json['msg']);
                    $(button).removeClass(json['cr']).addClass(json['ca']);
                    
                    Loan.load_compare();
                },
                error : function(xhr, ajaxOptions, thrownError){
                    toastr.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                },
            });
        } else {
            toastr.error("Sorry you haven't selected right.");
        }
    },
    'popup': function() {
        $("#popup-compare").load(full_url + "/business-term-loan/compare");
    },
    'shortlist': function(count) {
        if(count > 0) {
            $(".shortlist").slideDown();
            var $scrollTo = $("#compare-tab").offset().top;
            var $container = $("html,body").offset().top;
            $(window).scroll(function() {
                var scrollLeft = $scrollTo - $container;
                if ($(this).scrollTop() > scrollLeft) {
                    $(".shortlist").slideDown();
                } else {
                    $(".shortlist").slideUp();
                }
            });
        } else {
            $(".shortlist").slideUp();
        }            
    },
    'load_compare': function() {
        $.ajax({
            url: full_url + '/alternative-funding/funding-load-compare',
            type: 'post',
            dataType: 'json',
            success: function(json) {
                $("#compare-holder").html(json['html']);
                $("#compare-title > span").html(json['count']);
                $("input[name=count_compare]").val(json['count']);
                $(".drawercard-container").hover(function() {
    				if ($(this).children(".fa.fa-times").css("visibility") == "hidden") {
    					$(this).children(".fa.fa-times").css("visibility", "visible");
    				}
    			});
                Loan.shortlist(json['count']);
                Loan.sticky_header();
                Loan.popup();
    			$(".drawercard-container").mouseleave(function() {
    				if ($(this).children(".fa.fa-times").css("visibility") == "visible") {
    					$(this).children(".fa.fa-times").css("visibility", "hidden");
    				}
    			});
            },
            error : function(xhr, ajaxOptions, thrownError){
                toastr.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            },
        });
    },
    'clear_compare': function(button) {
        var id = $(button).data("id") ? $(button).data("id") : 0;
        var page = $(button).data("page") ? true : false;
        $.ajax({
            url: full_url + '/alternative-funding/funding-clear-compare',
            type: 'post',
            data: 'id='+id,
            dataType: 'json',
            beforeSend: function(xhr, settings) {
                App.blockUI({boxed: true});
 			},
            success: function(json) {
                App.unblockUI();
                if(json['success']) {
                    toastr.success(json['msg']);
                    if(id > 0) {
                        if($('.filters-content .box__compare > a[data-id=' + id + ']').hasClass('active')){
                              $('.filters-content .box__compare > a[data-id=' + id + ']').removeClass('active');
                        }
                    } else {
                        if($('.filters-content .box__compare > a').hasClass('active')){
                              $('.filters-content .box__compare > a').removeClass('active');
                        }
                    }
                    if(page) {
                        setTimeout(function(){ window.location.reload(); }, 1500);
                    } else {
                        if(json['count'] > 0) $(".shortlist").slideUp();
                        Loan.load_compare();
                    }
                }
            },
            error : function(xhr, ajaxOptions, thrownError){
                toastr.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            },
        });
    },
    'sticky_header': function() {
        if ($('.filters-table-container').length > 0){
            $(window).on('scroll', function () {
                var scrollTop       = $(window).scrollTop(),
                    elementOffset   = $('.filters-table-container').offset().top,
                    elementHeight   = $('.filters-table-container').innerHeight();
                    elementWidth    = $('.filters-table-body').innerWidth();
                    elementScroll   = ($('.filters-table-head').length > 0) ? $('.filters-table-head')[0].scrollHeight : 0;
                    distance        = (elementHeight+ elementOffset - elementScroll);
                    headHeight      = $('.filters-table-head').innerHeight();
                
                if(scrollTop > elementOffset ) {
                    $('.filters-table-head').css('max-width',elementWidth);
    
                    if ( scrollTop >= distance ) {
                        $('.filters-table-head').removeClass('fixed');
                        $('.filters-table-body').css('margin-top', '0');
                    } else {
                        $('.filters-table-head').addClass('fixed');
                        $('.filters-table-body').css('margin-top', headHeight);
                    }
                } else {
                    $('.filters-table-head').removeClass('fixed');
                    $('.filters-table-body').css('margin-top', '0');
                }
            });
        }
    },
    'sort': function(button) {
        var $this  = $(button);
        var field  = $this.parent().data("field");
        var icon   = $this.find('.fa').hasClass('fa-angle-up') ? '<i class="fa fa-angle-down">' : '<i class="fa fa-angle-up">';
        var order  = $this.find('.fa').hasClass('fa-angle-up') ? 'asc' : 'desc';
        var active = $this.parent().parent().hasClass('active') ? true : false;
        
        $(".filters-table-head > div").removeClass("active");
        $this.parent().parent().addClass("active");
        $(".filters-table-head > div a i").remove();
        
        var $divs = $(".filters-content.not-sponsored");
        
        var apbOrderDivs = $divs.sort(function (a, b) {
            if(field === 'bank') {
                if(order === 'asc') {
                    return $(a).find(".bank-title > a").text() < $(b).find(".bank-title > a").text();
                } else {
                    return $(a).find(".bank-title > a").text() > $(b).find(".bank-title > a").text();
                }
            }
            if(field === 'rate' || field === 'applicable' || field === 'loan' || field === 'month') {
                var reg = ".box__"+field; 
                if(order === 'asc') {
                    return $(a).find(reg+" > span").data('value') < $(b).find(reg+" > span").data('value');
                } else {
                    return $(a).find(reg+" > span").data('value') > $(b).find(reg+" > span").data('value');
                }
            }
        });
        
        var $sponsored = $('.filters-content.sponsored').clone();
        $(".filters-table-body").html(apbOrderDivs);
        $sponsored.prependTo($(".filters-table-body"));
        
        $this.find("i").remove();
        $this.append(icon);
    }
}
jQuery(document).ready(function() {    
    if (jQuery().datepicker) {
        $('.date-picker').datepicker({
            rtl: App.isRTL(),
            orientation: "left",
            autoclose: true
        });
    }
});