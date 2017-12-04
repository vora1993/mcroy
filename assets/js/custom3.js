var RESIDENCY_RATE = {
    'Singaporean': {
        '0': 3,
        '1': 7,
        '2+': 10
    },
    'Singapore PR': {
        '0': 5,
        '1': 10,
        '2+': 10
    },
    'Foreigner': {
        '0': 15,
        '1': 15,
        '2+': 15
    }
};

var DEFAULT_FEE = 5400;
var mortgageStampDuty = 500;
var valuationFee = 300;
var legalFee = 2000;
var fireInsurance = 200;

function cancel(route) {
    window.location.href = full_url + '/' + route;
}

function formatNumber(num, strDollar) {
    return strDollar + num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
}

function formatIntNumber(str) {
    if(str) return str.replace(/,/g, '');
}

function isEnterNumber(keyCode) {
    if ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 96 && keyCode <= 105)) {
        return true;
    }
    return false;
}

function getActualAmount(e) {
    return e ? (e = e.replace(/,/g, ""), multiplier = e.toLowerCase().match(/m/) ? 1e6 : e.toLowerCase().match(/k/) ? 1e3 : 1, e = parseFloat(e), e * multiplier) : void 0
}

function updatePieChart(percent) {
    $(".easy-pie-chart .number.loans").data("easyPieChart").update(percent);
    $(".easy-pie-chart .number.loans").find("span").html(percent+'%');
}

function updateLoanAmount() {
    var e = $("input[name=loan_percent]").val(),
        t = getActualAmount($("input[name=purchase_price]").val()),
        n = parseInt(t * (e / 100)),
        i = parseInt(t * ((100 - e) / 100));

    var uppayment = formatNumber(n, '');
    var downpayment = formatNumber(i, '');
    $("input[name=loan_amount]").length > 0 && $("input[name=loan_amount]").val(n),
    $("span#loan-amount-label").length > 0 && (isNaN(n) ? $("span#loan-amount-label").html("-") : $("span#loan-amount-label").html(uppayment)),
    $("span#downpayment-label").length > 0 && (isNaN(n) ? $("span#downpayment-label").html("-") : $("span#downpayment-label").html(downpayment)),
    $("span.main-percentage").length > 0 && $("span.main-percentage").html("(" + e + "%)"),
    $("span.minor-percentage").length > 0 && $("span.minor-percentage").html("(" + (100 - e) + "%)")
}

function maxTenure() {
    return "HDB Flat" == $("input[name=property_type]").val() ? 25 : 30
}

function maxLoanTenureValue() {
    var e = parseInt($("input[name=existing_home_loans]").val()),
        t = parseInt($("input[name=loan_tenure]").val()),
        n = 80;
    switch (e) {
        case 0:
            n = 80;
            break;
        case 1:
            n = 50;
            break;
        case 2:
            n = 40
    }
    return t > maxTenure() ? n - 20 : n
}

function updateSlider() {
    $(".slider-loan-amount").slider("value", maxLoanTenureValue());
    $(".slider-loan-amount").attr("data-soft-cap", maxLoanTenureValue());
    $("#loan_amount-label").text(maxLoanTenureValue());
}

function getNumber(value){
  var number = ( typeof( value ) !== "undefined" && value != "" ? value : 0 ).toString().replace(/[^\d.-]/ig, '');
  return parseFloat(number)
}

// Loan functions
var Loan = {
    'filter' : function(button) {
        var loan_amount  = $("input[name=loan_amount]").val();
        var loan_tenure  = $("input[name=loan_tenure]").val();
        var loan_percent = $("input[name=loan_percent]").val();
        var total_interest_for_years = $("select[name=total_interest_for_years]").val();
        var preferred_rate_package = $("select[name=preferred_rate_package]").val();
        var no_lock_in_only = $("input[name=no_lock_in_only]:checked").val() ? $("input[name=no_lock_in_only]:checked").val() : 0;

        if(loan_amount > 0 && loan_tenure > 0) {
            var l = Ladda.create(button);
            var data = 'loan_amount='+loan_amount+'&loan_tenure='+loan_tenure+'&loan_percent='+loan_percent+'&preferred_rate_package='+preferred_rate_package+'&total_interest_for_years='+total_interest_for_years+'&no_lock_in_only='+no_lock_in_only;
            $.ajax({
                url: full_url + '/loan-application/property-loan/commercial-industrial-loan/step/3',
                type: 'post',
                data: data,
                dataType: 'json',
                beforeSend: function(xhr, settings) {
                    l.start();
    			},
                success: function(json) {
                    if (json['success']) {
    				    l.stop();
                    	window.location.href = json['redirect'];
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
    'apply' : function(button) {
        var loan_amount = $("input[name=loan_amount]").val();
        var loan_tenure = $("input[name=loan_tenure]").val();
        var seo         = $("input[name=seo]").val();
        var id          = $(button).closest("#selected-holder").find(".fa.fa-times").data("id");

        if(id > 0 && loan_amount > 0 && loan_tenure > 0) {
            var data = 'seo='+seo+'&loan_amount='+loan_amount+'&loan_tenure='+loan_tenure+'&id='+id;
            $.ajax({
                url: full_url + '/home-loan/step/4',
                type: 'post',
                data: data,
                dataType: 'json',
                beforeSend: function(xhr, settings) {
                    App.blockUI({boxed: true});
    			},
                success: function(json) {
                    if (json['success']) {
    				    window.location.href = json['redirect'];
    				} else {
    				    toastr.warning(json['msg']);
    				}
                    App.unblockUI();
                },
                error : function(xhr, ajaxOptions, thrownError){
                    toastr.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                },
            });
        } else {
            toastr.error("Sorry you haven't selected right");
        }
    },
    'select' : function(button) {
        var id = $(button).data("id");
        if(id > 0) {
            var data = 'id='+id;
            $.ajax({
                url: full_url + '/home-loan/select',
                type: 'post',
                data: data,
                dataType: 'json',
                beforeSend: function(xhr, settings) {
                    App.blockUI({boxed: true});
    			},
                success: function(json) {
                    App.unblockUI();
                    if(json['success'] === false) toastr.error(json['msg']);
                    $(button).parent().removeClass(json['cr']).addClass(json['ca']);
                    Loan.load_select();
                },
                error : function(xhr, ajaxOptions, thrownError){
                    toastr.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                },
            });
        } else {
            toastr.error("Sorry you haven't selected right.");
        }
    },
    'shortlist2': function(a) {
        if($(a).find(".fa").hasClass("fa-angle-double-down")) {
            $(a).find(".fa").removeClass("fa-angle-double-down").addClass("fa-angle-double-up");
            $(".compare-row").slideUp();
        } else {
            $(a).find(".fa").removeClass("fa-angle-double-up").addClass("fa-angle-double-down");
            $(".compare-row").slideDown();
        }
    },
    'shortlist3': function(a) {
        if(a === 'close') {
            $('.compare-arrow').find(".fa").removeClass("fa-angle-double-down").addClass("fa-angle-double-up");
            $(".compare-row").slideUp();
        } else {
            $('.compare-arrow').find(".fa").removeClass("fa-angle-double-up").addClass("fa-angle-double-down");
            $(".compare-row").slideDown();
        }
    },
    'load_select': function() {
        $.ajax({
            url: full_url + "/home-loan/load-select",
            type: "post",
            dataType: "json",
            success: function(a) {
                $("#select-holder").html(a["html"]);
                $("#select-title > span").html(a["count"]);
                $(".drawercard-container").hover(function() {
                    if ("hidden" == $(this).children(".fa.fa-times").css("visibility")) $(this).children(".fa.fa-times").css("visibility", "visible");
                });
                $(".drawercard-container").mouseleave(function() {
                    if ("visible" == $(this).children(".fa.fa-times").css("visibility")) $(this).children(".fa.fa-times").css("visibility", "hidden");
                });
                if (a["count"] > 0) {
                    Loan.shortlist3('open');
                } else {
                    Loan.shortlist3('close');
                }
                Loan.popup();
            },
            error: function(a, b, c) {
                toastr.error(c + "\r\n" + a.statusText + "\r\n" + a.responseText);
            }
        });
    },
    'clear_select': function(a) {
        var b = $(a).data("id") ? $(a).data("id") : 0;
        $.ajax({
            url: full_url + "/home-loan/clear-select",
            type: "post",
            data: "id=" + b,
            dataType: "json",
            beforeSend: function(a, b) {
                App.blockUI({
                    boxed: true
                });
            },
            success: function(a) {
                App.unblockUI();
                if (a["success"]) {
                    toastr.success(a["msg"]);
                    Loan.load_select();
                }
            },
            error: function(a, b, c) {
                toastr.error(c + "\r\n" + a.statusText + "\r\n" + a.responseText);
            }
        });
    },
    'sticky_footer': function() {
        if ($('.selectlist').length > 0){
            $(window).on('scroll', function () {
                var scrollTop = $(window).scrollTop();
                var pointOne  = $(".page-header").innerHeight();
                var window_h  = window.innerHeight;
                var elementOffset = window_h - 52;

                if(scrollTop > elementOffset ) {
                    $('.selectlist').removeClass('fixed');
                } else {
                    $('.selectlist').addClass('fixed');
        	    }
            });
        }
    },
    'set_type': function(button) {
        var $this  = $(button);
        var value  = $this.data("value");
        var active = $this.hasClass('active') ? true : false;

        $this.closest(".property-type").find("a").removeClass("active");
        $this.addClass("active");
        $('input[name=property_type]').val(value);

        if(value === 'Executive Condo' || value === 'Condo / Apartment') {
            $(".project_name").removeClass("hide");
        } else {
            $(".project_name").addClass("hide");
        }
    },
    'set_exist': function(button) {
        var $this  = $(button);
        var value  = $this.data("value");
        var disabled = $this.hasClass('disabled') ? true : false;
        if(disabled) {
            $this.removeClass("active");
        } else {
            $this.closest(".property-type").find("a").removeClass("active");
            $this.addClass("active");
            $('input[name=existing_home_loans]').val(value);
            updateSlider();
        }
    },
    'set_property': function(button) {
        var $this  = $(button);
        var value  = $this.data("value");
        var disabled = $this.hasClass('disabled') ? true : false;
        if(disabled) {
            $this.removeClass("active");
        } else {
            $this.closest(".property-type").find("a").removeClass("active");
            $this.addClass("active");
            $('input[name=existing_property]').val(value);
        }
        this.totalCostOutlay();
    },
    'set_residency': function(button) {
        var $this  = $(button);
        var value  = $this.data("value");
        var disabled = $this.hasClass('disabled') ? true : false;
        if(disabled) {
            $this.removeClass("active");
        } else {
            $this.closest(".property-type").find("a").removeClass("active");
            $this.addClass("active");
            $('input[name=residency]').val(value);
        }
        this.totalCostOutlay();
    },
    'integer': function() {
        $(".integer").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                // Allow: Ctrl+A, Command+A
                (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
                // Allow: home, end, left, right, down, up
                (e.keyCode >= 35 && e.keyCode <= 40)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
    },
    'initEasyPieCharts': function() {
        if (!jQuery().easyPieChart) {
            return;
        }
        $('.easy-pie-chart .number.loans').easyPieChart({
            barColor: "#193d5a",
            trackColor: "#ededed",
            scaleColor: !1,
            lineWidth: 8,
            lineCap: "round",
            size: 90
        });
    },
    popup: function() {
        $("#popup-compare").load(full_url + "/loan-application/popup-property-loan");
    },
    detail: function() {
        $(".btn-more-detail").on('click', function(){
            $(this).closest(".row-footer").find(".more-info").slideDown();
            $(this).hide();
            $(this).next().show();
        });

        $(".btn-less-detail").on('click', function(){
            $("p").slideDown();
            $(this).closest(".row-footer").find(".more-info").slideUp();
            $(this).hide();
            $(this).prev().show();
        });

        $(".btn-less-detail").trigger("click");
    },
    sort: function(a) {
        var b = $(a);
        var c = b.parent().data("field");
        var d = b.find(".fa").hasClass("fa-long-arrow-up") ? '<i class="fa fa-long-arrow-down">' : '<i class="fa fa-long-arrow-up">';
        var e = b.find(".fa").hasClass("fa-long-arrow-up") ? "asc" : "desc";
        var f = b.parent().parent().hasClass("active") ? true : false;
        $(".filter-table-head > ul > li").removeClass("active");
        b.parent().parent().addClass("active");
        $(".filter-table-head > ul > li > span > a > i").remove();
        var g = $(".filters-content.not-sponsored");
        var h = g.sort(function(a, b) {
            if ("bank" === c) if ("asc" === e) return $(a).find(".head-title > a").text() < $(b).find(".head-title > a").text(); else return $(a).find(".head-title > a").text() > $(b).find(".head-title > a").text();
            if ("interest" === c || "monthly_repayment" === c || "total_interest_payable" === c) {
                var d = ".box__" + c;
                if ("asc" === e) return $(a).find(d + " > span").data("value") < $(b).find(d + " > span").data("value"); else return $(a).find(d + " > span").data("value") > $(b).find(d + " > span").data("value");
            }
            if ("type" === c) if ("asc" === e) return $(a).find(".head-title > a").text() < $(b).find(".head-title > a").text(); else return $(a).find(".head-title > a").text() > $(b).find(".head-title > a").text();
        });
        var i = $(".filters-content.sponsored").clone();
        $(".filters-table-body").html(h);
        i.prependTo($(".filters-table-body"));
        b.find("i").remove();
        b.append(d);
        Loan.detail();
    },
    caculateStampDutyFee: function() {
        var purchasePrice = getNumber($('input[name="purchase_price"]').val());
        var residency = $('input[name="residency"]').val();
        var existingProperty = $('input[name="existing_property"]').val();
        var stampDutyFee = ( purchasePrice * RESIDENCY_RATE[residency][existingProperty] / 100 ) - DEFAULT_FEE;
        stampDutyFeeValue = Number(stampDutyFee.toFixed(2));
        if (stampDutyFeeValue >= 0){
            stampDutyFee = formatNumber(stampDutyFeeValue, '$');
        } else{
            stampDutyFee = formatNumber(stampDutyFeeValue * -1, '-$');
        }

        $('#stamp-duty-fee').text(stampDutyFee);
        return stampDutyFeeValue;
    },
    totalCostOutlay: function() {
        var purchasePrice = getNumber($('input[name="purchase_price"]').val());
        var stampDutyFee = this.caculateStampDutyFee();
        var totalCostOutlay = stampDutyFee + mortgageStampDuty + valuationFee + legalFee + fireInsurance + ( purchasePrice * 20 / 100 );
        totalCostOutlayValue = Number(totalCostOutlay.toFixed(2));
        if (totalCostOutlayValue >= 0){
            totalCostOutlay = formatNumber(totalCostOutlayValue, '$');
        } else{
            totalCostOutlay = formatNumber(totalCostOutlayValue * -1, '-$');
        }
        $('#total-costs-outlay-amount').text(totalCostOutlay);
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

    $('#purchase_price').on('change', function(){

    });
});