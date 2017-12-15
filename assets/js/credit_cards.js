allowedCategories = [{code: "promotions"}, {code: "cash-back"}, {code: "air-miles"}, {code: "rewards"}, {code: "promo"}, {code: "things-to-look-out"}, {code: "eligibility"}, {code: "annual-fees-rates"}, {code: "interest-rate-fees"}, {code: "other-information"}]


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

function matchAllCompareSectionHeight() {
  if ($(".compare-section").length) {
    var e = ["product", "promotions", "things-to-look-out", "eligibility", "annual-fees-rates", "interest-rate-fees", "other-information"];
    $.each(allowedCategories, function(e, t) {
      console.log(t.code);
      $(".compare-section." + t.code).matchHeight({byRow: true})
    })
  }
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
  $(".slider-loan-amount").slider("value", maxLoanTenureValue()),
  $(".slider-loan-amount").attr("data-soft-cap", maxLoanTenureValue());
}

// CreditCard functions
var CreditCard = {
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
    var id          = $(button).data("id");
    if(id > 0 && loan_amount > 0 && loan_tenure > 0) {
      var data = 'seo='+seo+'&loan_amount='+loan_amount+'&loan_tenure='+loan_tenure+'&id='+id;
      $.ajax({
        url: full_url + '/credit-cards/apply',
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
        url: full_url + '/credit-cards/select',
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
          CreditCard.load_select();
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
    $(".credit-card-shortlist .shortlist-container").slideToggle();
  },
  'shortlist3': function(a) {
    if(a === 'close') {
      // $('.compare-arrow').find(".fa").removeClass("fa-angle-double-down").addClass("fa-angle-double-up");
      $("#creditCardShortlistId").slideUp();
    } else {
      // $('.compare-arrow').find(".fa").removeClass("fa-angle-double-up").addClass("fa-angle-double-down");
      $("#creditCardShortlistId").slideDown();
    }
  },
  'load_select': function() {
    $.ajax({
      url: full_url + "/credit-cards/load-select",
      type: "post",
      dataType: "json",
      success: function(a) {
        console.log(a);
        console.log($("#compare-holder"));
        $("#compare-holder").html(a["html"]);
        $("#compare-title > span").html(a["count"]);
        // $(".drawercard-container").hover(function() {
        //   if ("hidden" == $(this).children(".fa.fa-times").css("visibility")) $(this).children(".fa.fa-times").css("visibility", "visible");
        // });
        // $(".drawercard-container").mouseleave(function() {
        //   if ("visible" == $(this).children(".fa.fa-times").css("visibility")) $(this).children(".fa.fa-times").css("visibility", "hidden");
        // });
        if (a["count"] > 0) {
          CreditCard.shortlist3('open');
        } else {
          CreditCard.shortlist3('close');
        }
        CreditCard.popup();
      },
      error: function(a, b, c) {
        toastr.error(c + "\r\n" + a.statusText + "\r\n" + a.responseText);
      }
    });
  },
  'clear_select': function(a) {
    var b = $(a).data("id") ? $(a).data("id") : 0;
    $.ajax({
      url: full_url + "/credit-cards/clear-select",
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
          CreditCard.load_select();
        }
      },
      error: function(a, b, c) {
        toastr.error(c + "\r\n" + a.statusText + "\r\n" + a.responseText);
      }
    });
  },
  clear_compare: function(a) {
        var b = $(a).data("id") ? $(a).data("id") : 0;
        var c = $(a).data("page") ? true : false;
        $.ajax({
            url: full_url + "/credit-cards/clear-compare",
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
                    if (b > 0) {
                        if ($(".filters-content .box__compare > a[data-id=" + b + "]").hasClass("active")) $(".filters-content .box__compare > a[data-id=" + b + "]").removeClass("active");
                    } else if ($(".filters-content .box__compare > a").hasClass("active")) $(".filters-content .box__compare > a").removeClass("active");
                    if (c) setTimeout(function() {
                        window.location.reload();
                    }, 1500); else {
                        CreditCard.load_select();
                    }
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
        /*var active = $this.hasClass('active') ? true : false;

        $(".property-type > a").removeClass("active");
        $this.addClass("active");*/
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
          $(".property-type > a").removeClass("active");
          $this.addClass("active");
          $('input[name=existing_home_loans]').val(value);
        }
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
        $("#popup-compare").load(full_url + "/credit-cards/popup-credit-card");
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
        CreditCard.detail();
      }
    }
    jQuery(document).ready(function() {
      if (jQuery().select2) {
        $('.select2').select2();
      }
      matchAllCompareSectionHeight();
      if (jQuery().datepicker) {
        $('.date-picker').datepicker({
          rtl: App.isRTL(),
          orientation: "left",
          autoclose: true
        });
      }
    });