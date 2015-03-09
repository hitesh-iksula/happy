var visible_product_count = {};
var product_modal_width = 0;

jQuery(document).ready(function($) {

	/*
	 * Adjust image dimensions
	 */
	var adjustImageDimensions = function() {

		var main_width = jQuery('.main').width();

		if(main_width < 900) {
			var newFontSize = Math.round(main_width * 0.02);
			var priceFontSize = Math.round(main_width * 0.03);
		} else {
			var newFontSize = Math.round(main_width * 0.01215);
		}

		var newFontSizeprice = Math.round(main_width * 0.02604);
		var newLeftNavSizeprice = Math.round(main_width * 0.02730);
		var categoryheadfontSize = Math.round(main_width * 0.0190);

		$('.products-grid .product_name_title').css('font-size',newFontSize);
		$('.products-grid .price-box span').css('font-size',priceFontSize);

		$('.cms-subcategory-area ul li a ').css('font-size',newLeftNavSizeprice);
		$('.cms-subcategory-area ul li a ').css('line-height',newLeftNavSizeprice+"px");

		var category_product_image_link_width = $(".products-grid li .product-image").width();
		var category_product_image_width = $(".products-grid li .product-image img").width();
		var category_product_image_link_padding = (category_product_image_link_width - category_product_image_width);
		var category_product_title_height = $(".products-grid li h2").outerHeight();
		var category_product_image_link_padding = ((category_product_image_link_padding-category_product_title_height)/2);
		/*$(".products-grid li .product-image").height(category_product_image_width).css({'padding':category_product_image_link_padding+'px 0', 'margin':'0'});*/

	}

	adjustImageDimensions();
	$(window).resize(adjustImageDimensions);

	if($('.one_category').length > 0 && !$('.one_category').hasClass('homepage_category') && $('.one_category').attr('id') != 'folofo_cms_page_container') {
		$('.one_category').stickyHeader({stickyClass : 'category_head'});
	}

	/*
	 * Cart modal related
	 */
	winWidh =  Math.round($('.main').width());
	var autoDimensions = true;

	if(winWidh > 900) {
		product_modal_width = 930;
		autoDimensions = false;
	} else if(winWidh < 900 && winWidh > 780) {
		product_modal_width = 780;
	} else {
		product_modal_width = 680;
	}

	function fancybox_footer() {
		var tailbar_content = $('.modal_tailbar_container').html();
		return tailbar_content;
	}

	function show_free_shipping_text() {
		var cart_total = parseInt($('.totals_table_amounts .price').text().replace(",", ""));
		var cart_item_count = parseInt($('.cart-item-count').html());
		if(cart_item_count == 1) {
			$('.free_shipping_note').addClass('minimal');
		} else {
			$('.free_shipping_note').removeClass('minimal');
		}
		if(cart_total >= 2000 && cart_total < 2500) {
			$('.free_shipping_note').fadeIn();
			var free_shipping_timeout = window.setTimeout(function() {
				$('.free_shipping_note').fadeOut();
			}, 6000);
		}
	}

	$('.keep_shopping_button').live('click', function(e){
		e.preventDefault();
		$.fancybox.close();
	});

	$('.cart_page').fancybox({
		"width" : product_modal_width,
		"height" : 602,
		"padding" : 0,
		"margin" : 40,
		"autoDimensions" : autoDimensions,
		"autoScale" : autoDimensions,
		"transitionIn" : "fade",
		"transitionOut" : "none",
		"speedIn" : 300,
		"speedOut" : 300,
		"titleShow" : false,
		"titleFormat" : fancybox_footer,
		"overlayOpacity" : 0.6,
		"overlayColor" : "#000",
		onComplete: function() {
			$('#add_cart_activity').val('0');
			show_free_shipping_text();	
			/*window._fbq = window._fbq || [];
			window._fbq.push(['track', '6021299179307', {'value':'0.00','currency':'INR'}]);*/
		},
		onClosed: function() {
			$('#fancybox-overlay').show();
		}
	});

	/*
	 * Used for category sidebar positioning and events
	 */
	var category_id = '';
	var left_category_id = '';
	var inside_subcategories = false;
	var fadeout;

	$('#floating_categories .categories a').mouseenter(function() {
		category_id = $(this).parent().attr('id');
		category_id = category_id.replace('left', 'subcategory_of_');
		$('.subcategories').hide();
		var offset_left = $(this).outerWidth() + $(this).offset().left;
		var offset_top = $(this).offset().top - $(window).scrollTop();
		var margin = 12;
		var subcategory_nav = $('#' + category_id);
		subcategory_nav.css('left', offset_left + margin);
		subcategory_nav.css('top', offset_top);
		$('#' + category_id).fadeIn(200);
		inside_subcategories = true;

		var subcategory_height = subcategory_nav.height();
		if(($(window).height() - offset_top) - 30 < subcategory_height) {
			var marginTop = -subcategory_height + $(this).height() + 14;
			subcategory_nav.css('margin-top', marginTop + 'px');
			$('#' + category_id + ' .pointer').css('margin-top', -marginTop + 8 + 'px');
		}

	}).mouseleave(function() {
		fadeout = setTimeout(function() {
			if(!inside_subcategories) {
				$('#' + category_id).fadeOut(200);
			}
		}, 300);
		inside_subcategories = false;
	});

	$('.subcategories').mouseenter(function() {
		left_category_id = $(this).attr('id').replace('subcategory_of_', 'left');
		$('#' + left_category_id).addClass('current');
		inside_subcategories = true;
	}).mouseleave(function() {
		if(!$('#' + left_category_id).hasClass('persistent')) {
			$('#' + left_category_id).removeClass('current');
		}
		$(this).hide();
		inside_subcategories = false;
	});

	$('#floating_categories .categories a').click(function() {
		$('#floating_categories .categories').removeClass('current');
		$(this).parent().addClass('current');
	});

	/*
	 * Scroll up button on the right
	 */
	$('.back_to_top').click(scroll_up);
	$(window).scroll(scroll_observe);

	function scroll_up(){
		$('html,body').animate({scrollTop: '0px'}, 1000);
		$('.back_to_top').hide();
	}

	function scroll_observe(){
		if ($(window).scrollTop() >= ($(window).height())) {
			$('.back_to_top').show();
		} if ($(window).scrollTop() == 0) {
			$('.back_to_top').hide();
		}
	}

	/*
	 * iOS Safari hover -- touch fix
	 */
	$('.sidebar_category_label').live('click touchend', function(e) {
		var el = $(this);
		var link = el.attr('href');
		window.location = link;
	});

});
