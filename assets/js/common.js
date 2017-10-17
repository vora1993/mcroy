function quick_view_product(id) {
    $('#product-pop-up').load(full_url+'/product/quick-view/'+id, function(){
        var source = $(this);
        $.fancybox({
            content: source,
            afterShow : function(){
    			$('.product-main-image').zoom({url: $('.product-main-image img').attr('data-BigImgSrc')});
                Layout.initTouchspin();
    		}
        });
    });
}

function getURLVar(key) {
	var value = [];

	var query = String(document.location).split('/');
    var count = query.length;
    var ctrl = query[count-2];
    var actn = query[count-1]; 
    if (ctrl && actn) {
		var route = ctrl+'/'+actn;
        if(route == key) return true;
	}
    return false;
}

// App functions 
var app = {
    'load' : function(options) {
        options = $.extend(true, {}, options);
        var html = '';
        if (options.animate) {
            html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '">' + '<div class="block-spinner-bar"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>' + '</div>';
        } else if (options.iconOnly) {
            html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><img src="' + full_url + '/assets/img/loading-spinner-grey.gif" align=""></div>';
        } else if (options.textOnly) {
            html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><span>&nbsp;&nbsp;' + (options.message ? options.message : 'LOADING...') + '</span></div>';
        } else {
            html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><img src="' + full_url + '/assets/img/loading-spinner-grey.gif" align=""><span>&nbsp;&nbsp;' + (options.message ? options.message : 'LOADING...') + '</span></div>';
        }

        if (options.target) { // element blocking
            var el = $(options.target);
            if (el.height() <= ($(window).height())) {
                options.cenrerY = true;
            }
            el.block({
                message: html,
                baseZ: options.zIndex ? options.zIndex : 1000,
                centerY: options.cenrerY !== undefined ? options.cenrerY : false,
                css: {
                    top: '10%',
                    border: '0',
                    padding: '0',
                    backgroundColor: 'none'
                },
                overlayCSS: {
                    backgroundColor: options.overlayColor ? options.overlayColor : '#555',
                    opacity: options.boxed ? 0.05 : 0.1,
                    cursor: 'wait'
                }
            });
        } else { // page blocking
            $.blockUI({
                message: html,
                baseZ: options.zIndex ? options.zIndex : 1000,
                css: {
                    border: '0',
                    padding: '0',
                    backgroundColor: 'none'
                },
                overlayCSS: {
                    backgroundColor: options.overlayColor ? options.overlayColor : '#555',
                    opacity: options.boxed ? 0.05 : 0.1,
                    cursor: 'wait'
                }
            });
        }
    },
    'unload': function(target) {
        if (target) {
            $(target).unblock({
                onUnblock: function() {
                    $(target).css('position', '');
                    $(target).css('zoom', '');
                }
            });
        } else {
            $.unblockUI();
        }
    }
}

// Cart functions
var cart = {
    'add' : function(button, product_id) {
        var l = Ladda.create(button);
        var data = $('div[data-id='+product_id+'] .product-page-options input[type=\'text\'],div[data-id='+product_id+'] .product-page-options input[type=\'hidden\'],div[data-id='+product_id+'] .product-page-options input[type=\'radio\']:checked,div[data-id='+product_id+'] .product-page-options input[type=\'checkbox\']:checked,div[data-id='+product_id+'] .product-page-options select,div[data-id='+product_id+'] .product-page-options textarea');
        var quantity = $(button).parent().find('.product-quantity input[name=product_quantity]').val();
        $.ajax({
            url: full_url+"/checkout/cart-add",
            type: 'post',
            data: data,
            dataType: 'json',
            beforeSend: function(xhr, settings) {
                l.start();
                settings.data += '&product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1);
			},
            complete: function() {
			},
            success: function(json) {
                $('.alert, .text-danger').remove();
                if (json['redirect']) {
					location = json['redirect'];
				}
                if (json['success']) {
				    l.stop();
                	toastr.success(json['success']);

					// Need to set timeout otherwise it wont update the total
					setTimeout(function () {
						$('.top-cart-info > .top-cart-info-count').html(json['count']);
                        $('.top-cart-content ul').html(json['products']);
                        $('.cart-action').show();
					}, 100);
                                 
	   				info_cart();
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
				}
            },
            error : function(xhr, ajaxOptions, thrownError){
                toastr.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            },
        });
    },
    'update': function(id) {
        var quantity = $('input[name=product_quantity]').val();
        $.ajax({
			url: full_url+"/checkout/cart-update",
			type: 'post',
			data: 'id=' + id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				toastr.info("Đang xử lý...");
			},
			complete: function() {
				
			},
			success: function(json) {
                if (json['success']) {
                    toastr.success(json['msg']);
                } else {
                    toastr.error(json['msg']);
                }
				if (getURLVar('checkout/cart')) {
					location.reload();
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
    'remove': function(id) {
		$.ajax({
			url: full_url+"/checkout/cart-remove",
            type: 'post',
			data: 'id=' + id,
			dataType: 'json',
			beforeSend: function() {
				toastr.info("Đang xử lý...");
			},
			complete: function() {
				
			},
			success: function(json) {
                if (json['success']) {
                    toastr.success(json['msg']);
                    $('.top-cart-info > .top-cart-info-count').html(json['count']);
                    info_cart();
                } else {
                    toastr.error(json['msg']);
                }
                if (getURLVar('checkout/cart')) {
					location.reload();
				} 
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

function cancel(route) {
    window.location.href = full_url + '/' + route;
}

function load_cart() {
    $.ajax({
        url: full_url+"/product/load-cart",
        dataType: 'json',
        success: function(json) {
            if(json['cart']) {
                $('.top-cart-info > .top-cart-info-count').html(json['count']+' sản phẩm');
                info_cart();
                $('.cart-action').show();
            } else {
                $('.top-cart-info > .top-cart-info-count').html('0 sản phẩm');
                $('.top-cart-content ul').html('Giỏ hàng rỗng');
                $('.cart-action').hide(); 
            }
        },
    });
}

function info_cart() {
    $.ajax({
        url: full_url+"/product/info-cart",
        dataType: 'html',
        success: function(html) {
            $('.top-cart-content ul').html(html);
        },
    });
}

function load_order() {
    $.ajax({
        url: full_url+"/checkout/load-order",
        dataType: 'json',
        beforeSend: function() {
            $('#order-information .portlet-body').html('<div class="uil-reload-css" style="-webkit-transform:scale(0.32)"><div></div></div>');
  		},
        complete: function() {
            $('#order-information .portlet-body .uil-reload-css').remove();
  		},
        success: function(json) {
            $('#order-information').html(json['html']);
        },
    });
}

function load_district() {
    var province_id = $('select[name=province_id]').val();
    if(province_id > 0) {
        $.ajax({
            url: full_url+"/checkout/load-district",
            type: 'POST',
            data: 'province_id='+province_id,
            dataType: 'json',
            beforeSend: function() {
    			$('select[name=\'province_id\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
    		},
            complete: function() {
    			$('.fa-spin').remove();
    		},
            success: function(json) {
                if (json['html']) {
                    $('select[name=district_id]').html(json['html']);
                }
            },
        });    
    }
}

function load_ward() {
    var district_id = $('select[name=district_id]').val();
    if(district_id > 0) {
        $.ajax({
            url: full_url+"/checkout/load-ward",
            type: 'POST',
            data: 'district_id='+district_id,
            dataType: 'json',
            beforeSend: function() {
    			$('select[name=\'ward_id\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
    		},
            complete: function() {
    			$('.fa-spin').remove();
    		},
            success: function(json) {
                if (json['html']) {
                    $('select[name=ward_id]').html(json['html']);
                }
            },
        });    
    }
}

function load_payment(id) {
    if(id > 0) {
        $.ajax({
            url: full_url+"/checkout/load-payment",
            type: 'POST',
            dataType: 'json',
            data: 'id='+id,
            beforeSend: function() {
    			app.load();
    		},
            complete: function() {
    			app.unload();
    		},
            success: function(json) {
                $('#payment-note').html(json['html']);
            },
        });    
    }
}

function user_checkout(button) {
    var l = Ladda.create(button);
    $.ajax({
        url: full_url+"/checkout/user",
        type: 'POST',
        data: 'checkout=user',
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            l.start();
        },
        success: function(json) {
            if (json['success']) {
                l.stop();
            }
            window.location.href = full_url + json['redirect']; 
        },
    });
}