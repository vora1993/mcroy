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
    var dataBankIdList = $(".banks-filter .col-md-6").not(".grayscale").map(function() {
      return $(this).data("bank-id");
    }).get();
    var dataProviderIdList = $(".card-providers-filter li").not(".grayscale").map(function() {
      return $(this).data("provider-id");
    }).get();
    var categoryId = $('select[name="select_category_credit_card"]').val();

    var l = Ladda.create(button);
    var data = {
      bank_ids: dataBankIdList,
      provider_ids: dataProviderIdList,
      category_id: categoryId
    }
    // toastr.error("Sorry you haven't selected right.");
    $.ajax({
      url: full_url + '/credit-cards/filter',
      type: 'post',
      data: data,
      dataType: 'html',
      beforeSend: function(xhr, settings) {
        l.start();
      },
      success: function(html) {
        l.stop();
        $('#results').html(html);
      },
      error : function(xhr, ajaxOptions, thrownError){
        l.stop();
        toastr.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      },
    });

  },
  'filterBank': function(element) {
    var bankId = $(element).data('bank-id');
    $(element).toggleClass('grayscale');
  },
  'apply' : function(button) {
    var dataUrl = $(button).data("url");
    if(dataUrl) {
      window.location.href = dataUrl;
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
  'add_item_compare': function(element) {
    var id = $(element).data("compare-add");  
    if(id > 0) {
      var data = 'id='+id;
      $.ajax({
        url: full_url + '/credit-cards/add-item-compare',
        type: 'post',
        data: data,
        dataType: 'json',
        beforeSend: function(xhr, settings) {
          App.blockUI({boxed: true});
        },
        success: function(json) {
          App.unblockUI();
          if(json['success'] === false){
            toastr.error(json['msg']);
          } else {
            CreditCard.list_compare();
            $('[data-compare-add='+id+']').hide();
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
  'list_compare': function(category='all') {
      $.ajax({
        url: full_url + '/credit-cards/get-list-compare',
        type: 'post',
        data: {category:category},
        dataType: 'html',
        async:false,
        beforeSend: function(xhr, settings) {
          App.blockUI({boxed: true});
        },
        success: function(html) {
          App.unblockUI();
          $('#compare_id').html(html);
        },
        error : function(xhr, ajaxOptions, thrownError){
          toastr.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        },
      });
  },

  'shortlist2': function(a) {
    $(".credit-card-shortlist .shortlist-container").slideToggle();
  },
  'shortlist3': function(a) {
    if(a === 'close') {
      $("#creditCardShortlistId").slideUp();
    } else {
      $("#creditCardShortlistId").slideDown();
    }
  },
  'load_select': function() {
    $.ajax({
      url: full_url + "/credit-cards/load-select",
      type: "post",
      dataType: "json",
      success: function(a) {
        $("#compare-holder").html(a["html"]);
        $("#compare-title > span").html(a["count"]);
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
  'remove_select': function(a) {
    var $this = $(a)
    var b = $(a).data("compare-remove") ? $(a).data("compare-remove") : 0;
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
          $this.closest('.compare-item').remove();
          CreditCard.list_compare();
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
        $("#results").on('click', '.btn-more-detail', function(){
          $(this).closest(".row-footer").find(".more-info").slideDown();
          $(this).hide();
          $(this).next().show();
        });

        $("#results").on('click', '.btn-less-detail', function(){
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

      // Handle number
      CreditCard.integer();
      CreditCard.sticky_footer();
      CreditCard.load_select();

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
      });

      var slider_monthly_income = $(".slider-monthly-income").slider({
        value: $(".slider-monthly-income").data("value"),
        min: $(".slider-monthly-income").data("min"),
        max: $(".slider-monthly-income").data("max"),
        orientation: "horizontal",
        animate: !0,
        range: "min",
        slide: function(event, ui) {
          $(".monthly-income-label").html(formatNumber(ui.value, "$"));
          $("input[name=monthly_income]").val(ui.value);
        },
        change: function(event, ui) {
          formatNumber(ui.value, "$");
        }
      })

      var $slider_loan_percent = $(".slider-loan-amount").slider({
        value: $(".slider-loan-amount").data("value"),
        min: $(".slider-loan-amount").data("min"),
        max: $(".slider-loan-amount").data("max"),
        orientation: "horizontal",
        animate: !0,
        range: "min",
        slide: function(event, ui) {
          $(".loan-percentage-label").html(ui.value);
          $("input[name=loan_percent]").val(ui.value);
        },
        change: function(event, ui) {
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
        slide: function(event, ui) {
          if ("" != $(".slider-loan-tenure").attr("data-soft-cap")) {
            var o = parseInt($(".slider-loan-tenure").attr("data-soft-cap"));
            if (ui.value > o) return !1
          }
        $(".loan-tenure-label").html(ui.value);
        $("input[name=loan_tenure]").val(ui.value);
      },
      change: function(event, ui) {
        updateSlider();
        updateLoanAmount();
      }
      });

      $(".btn-less-detail").hide();
      $("#results").on('click', '.btn-more-detail', function(){
        $(this).closest(".filters-content").find(".more-info").slideDown();
        $(this).hide();
        $(this).next().show();
      });

      $("#results").on('click', '.btn-less-detail', function(){
        $(this).closest(".filters-content").find(".more-info").slideUp();
        $(this).hide();
        $(this).prev().show();
      });

      $("#category-menu").owlCarousel({
        items: 5,
        navigation: true,
        navigationText: ['<i class="fa fa-chevron-left fa-5x"></i>', '<i class="fa fa-chevron-right fa-5x"></i>'],
        pagination: false
      });
    });

$('.news-content').owlCarousel({
  items: 4,
  autoPlay: 5000,
  center: true,
  navigation: true,
  navigationText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
  pagination: false
});

$('#slideshow').owlCarousel({
  items: 3,
  autoPlay: 5000,
  singleItem: true,
  navigation: true,
  navigationText: ['<i class="fa fa-chevron-left fa-5x"></i>', '<i class="fa fa-chevron-right fa-5x"></i>'],
  pagination: false
});