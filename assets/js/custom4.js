function cancel(a) {
    window.location.href = full_url + "/" + a;
}

function formatNumber(a, b) {
    return b + a.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
}

function formatIntNumber(a) {
    if (a) return a.replace(/,/g, "");
}

function isEnterNumber(a) {
    if (a >= 48 && a <= 57 || a >= 96 && a <= 105) return true;
    return false;
}

function resizeFacebookComments() {
    var a = $(".fb-comments iframe").attr("src").split("width="), b = $("#container").width();
    $(".fb-comments iframe").attr("src", a[0] + "width=" + b);
}

var Loan = {
    detail: function() {
        $(".btn-more-detail").on('click', function(){
            $(this).closest(".row-footer").find(".summary-details").slideDown();
            $(this).hide();
            $(this).next().show();
        });
    
        $(".btn-less-detail").on('click', function(){
            $("p").slideDown();
            $(this).closest(".row-footer").find(".summary-details").slideUp();
            $(this).hide();
            $(this).prev().show();
        });
        
        $(".btn-less-detail").trigger("click");
    },
    load: function() {
        var category_id = $("input[name=category_id]").val();
        $.ajax({
            type: "POST",
            url: full_url + "/loan-application/bank-account",
            data: {category_id: category_id},
            dataType: 'json',
            beforeSend: function() {
                App.blockUI({boxed: true});
            },
            success: function(itemJson) {
                App.unblockUI();
                $('#results').html(itemJson.html);
                Loan.detail();
            },

            error : function(xhr, status){
                console.log(status);
            },
        });
    },
    load_fixed_deposit: function() {
        var category_id = $("input[name=category_id]").val();
        var loan_amount_interes=$("input[name=loan_amount]").val();
        var month_interes=$("select[name=month_interes]").val();
        console.log(category_id+'/'+loan_amount_interes+'/'+month_interes);
        $.ajax({
            type: "POST",
            url: full_url + "/loan-application/bank-account",
            data: {category_id: category_id,loan_amount_interes: loan_amount_interes,month_interes: month_interes},
            dataType: 'json',
            beforeSend: function() {
                App.blockUI({boxed: true});
            },
            success: function(itemJson) {
                App.unblockUI();
                $('#results').html(itemJson.html);
                Loan.detail();
            },

            error : function(xhr, status){
                console.log(status);
            },
        });
    },
    compare: function(a) {
        var b = $(a).data("id");
        if (b > 0) {
            var e = "id=" + b;
            $.ajax({
                url: full_url + "/bank-account/compare",
                type: "post",
                data: e,
                dataType: "json",
                beforeSend: function(a, b) {
                    App.blockUI({
                        boxed: true
                    });
                },
                success: function(b) {
                    App.unblockUI();
                    if (false === b["success"]) toastr.error(b["msg"]);
                    $(a).removeClass(b["cr"]).addClass(b["ca"]);
                    Loan.load_compare();
                },
                error: function(a, b, c) {
                    toastr.error(c + "\r\n" + a.statusText + "\r\n" + a.responseText);
                }
            });
        } else toastr.error("Sorry you haven't selected right.");
    },
    popup: function() {
        $("#popup-compare").load(full_url + "/bank-account/compare");
    },
    apply: function(a) {
        var b = $(a).data("id");
        var category_id = $("input[name=category_id]").val();
        if (category_id > 0) {
            var e = Ladda.create(a);
            var f = "id=" + b + "&category_id=" + category_id;
            $.ajax({
                url: full_url + "/bank-account/apply",
                type: "post",
                data: f,
                dataType: "json",
                beforeSend: function(a, b) {
                    e.start();
                },
                success: function(a) {
                    if (a["success"]) {
                        e.stop();
                        window.location.href = a["redirect"];
                    }
                },
                error: function(a, b, c) {
                    toastr.error(c + "\r\n" + a.statusText + "\r\n" + a.responseText);
                }
            });
        } else toastr.error("Sorry you haven't selected right.");
    },
    shortlist: function(a) {
        if (a > 0) {
            $(".shortlist").slideDown();
            var b = $("#compare-tab").offset().top;
            var c = $("html,body").offset().top;
            $(window).scroll(function() {
                var a = b - c;
                if ($(this).scrollTop() > a) $(".shortlist").slideDown(); else $(".shortlist").slideUp();
            });
        } else $(".shortlist").slideUp();
    },
    shortlist2: function(a) {
        if($(a).find(".fa").hasClass("fa-angle-double-down")) {
            $(a).find(".fa").removeClass("fa-angle-double-down").addClass("fa-angle-double-up");
            $(".compare-row").slideUp();
        } else {
            $(a).find(".fa").removeClass("fa-angle-double-up").addClass("fa-angle-double-down");
            $(".compare-row").slideDown();
        }
    },
    shortlist3: function(a) {
        if(a === 'close') {
            $('.compare-arrow').find(".fa").removeClass("fa-angle-double-down").addClass("fa-angle-double-up");
            $(".compare-row").slideUp();
        } else {
            $('.compare-arrow').find(".fa").removeClass("fa-angle-double-up").addClass("fa-angle-double-down");
            $(".compare-row").slideDown();
        }
    },
    load_compare: function() {
        $.ajax({
            url: full_url + "/bank-account/load-compare",
            type: "post",
            dataType: "json",
            success: function(a) {
                $("#compare-holder").html(a["html"]);
                $("#compare-title > span").html(a["count"]);
                $("input[name=count_compare]").val(a["count"]);
                $(".drawercard-container").hover(function() {
                    if ("hidden" == $(this).children(".fa.fa-times").css("visibility")) $(this).children(".fa.fa-times").css("visibility", "visible");
                });
                if (a["count"] > 0) {
                    Loan.shortlist3('open');
                } else {
                    Loan.shortlist3('close');
                } 
                Loan.sticky_header();
                Loan.popup();
                $(".drawercard-container").mouseleave(function() {
                    if ("visible" == $(this).children(".fa.fa-times").css("visibility")) $(this).children(".fa.fa-times").css("visibility", "hidden");
                });
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
            url: full_url + "/bank-account/clear-compare",
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
                        Loan.load_compare();
                    }
                }
            },
            error: function(a, b, c) {
                toastr.error(c + "\r\n" + a.statusText + "\r\n" + a.responseText);
            }
        });
    },
    sticky_header: function() {
        if ($(".filters-table-container").length > 0) $(window).on("scroll", function() {
            var a = $(window).scrollTop(), b = $(".filters-table-container").offset().top, c = $(".filters-table-container").innerHeight();
            elementWidth = $(".filters-table-body").innerWidth();
            elementScroll = $(".filters-table-head").length > 0 ? $(".filters-table-head")[0].scrollHeight : 0;
            distance = c + b - elementScroll;
            headHeight = $(".filters-table-head").innerHeight();
            if (a > b) {
                $(".filters-table-head").css("max-width", elementWidth);
                if (a >= distance) {
                    $(".filters-table-head").removeClass("fixed");
                    $(".filters-table-body").css("margin-top", "0");
                } else {
                    $(".filters-table-head").addClass("fixed");
                    $(".filters-table-body").css("margin-top", headHeight);
                }
            } else {
                $(".filters-table-head").removeClass("fixed");
                $(".filters-table-body").css("margin-top", "0");
            }
        });
    },
    sort: function(a) {
        var b = $(a);
        var c = b.parent().data("field");
        var d = b.find(".fa").hasClass("fa-long-arrow-up") ? '<i class="fa fa-long-arrow-down">' : '<i class="fa fa-long-arrow-up">';
        var e = b.find(".fa").hasClass("fa-long-arrow-up") ? "asc" : "desc";
        var f = b.parent().parent().hasClass("active") ? true : false;
        $(".filters-table-head > div").removeClass("active");
        b.parent().parent().addClass("active");
        $(".filters-table-head > div a i").remove();
        var g = $(".filters-content.not-sponsored");
        var h = g.sort(function(a, b) {
            if ("bank" === c) if ("asc" === e) return $(a).find(".bank-title > a").text() < $(b).find(".bank-title > a").text(); else return $(a).find(".bank-title > a").text() > $(b).find(".bank-title > a").text();
            if ("initial_deposit_amount" === c || "minimum_balance" === c || "interest_rates" === c || "tenor" === c || "interest_earned" === c) {
                var d = ".box__" + c;
                if ("asc" === e) return $(a).find(d + " > span").data("value") < $(b).find(d + " > span").data("value"); else return $(a).find(d + " > span").data("value") > $(b).find(d + " > span").data("value");
            }
        });
        var i = $(".filters-content.sponsored").clone();
        $(".filters-table-body").html(h);
        i.prependTo($(".filters-table-body"));
        b.find("i").remove();
        b.append(d);
        Loan.detail();
    }
};

jQuery(document).ready(function() {
    if (jQuery().datepicker) $(".date-picker").datepicker({
        rtl: App.isRTL(),
        orientation: "left",
        autoclose: true
    });
});