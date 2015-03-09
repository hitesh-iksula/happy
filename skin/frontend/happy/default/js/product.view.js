jQuery(document).ready(function($){

	var minSlides = 4;
	var slider_settings = {
		mode: 'vertical',
		slideWidth: 94,
		minSlides: minSlides,
		slideMargin: 10,
		moveSlides: 1,
		adaptiveHeight: false,
		infiniteLoop: false,
		hideControlOnEnd: true,
		pager: false
	};
	var gallery_slider = $('.pagination').bxSlider(slider_settings);

	if(selected_option_label != '') {
		$('.cloud-zoom').hide();
		$('.cloud-zoom.the-active-image-cloud-zoom').show();
	}

	$('.super-attribute-select').change(get_label);

	function get_label() {
		if($(this).find(':selected').attr('rel') != undefined) {
			var option_label = $(this).find(':selected').attr('rel');
			change_image(option_label);
		}
	}

	function change_image(option_label) {
		$('.image_gallery_content .pagination_img').each(function() {
			var image_title = $(this).attr('title');
			if(image_title == option_label) {
				$(this).trigger("click");
				selected_option_label = option_label;
				check_position_of_current_gallery_image($(this));
			}
		});
	}

	$('.prev, .next', '#product_img_slider').live('click', function(event) {
		if(event.handled !== true) {
			event.preventDefault();
			check_position_of_current_gallery_image($('.image_gallery_content .current .pagination_img'));
			event.handled = true;
		}
		return false;
	});

	function check_position_of_current_gallery_image(image_object) {
		var current_gallery_image = image_object.parent();
		var gi_count = gallery_slider.getSlideCount();
		var cgi_index = current_gallery_image.index() + 1;
		if(cgi_index == 1) {
			gallery_slider.goToSlide(0);
		} else if(cgi_index > slider_settings.minSlides) {
			var jump = cgi_index - slider_settings.minSlides;
			gallery_slider.goToSlide(jump);
		} else if(cgi_index <= gi_count - slider_settings.minSlides) {
			gallery_slider.goToPrevSlide();
		}

	}

	var select_box = $('select').selectBoxIt({ theme: 'default' });

	$('.upsell_product_button').click(function(e) {
		if(mode_of_view == 'modal') {
			e.preventDefault();
			action.command = 'open_new';
			action.data = $(this).attr('id');
			parent.jQuery.fancybox.close();
		}
	});

	var window_width =  Math.round(jQuery('.main').width());
	var resized_image_width = 0;
	var original_img_box_width = '54.5%';

	if(window_width > 900)
	{
		jQuery('#product_img_slider .slides_container').css({width:"420px", height:"400px"});
		jQuery('img.prod-img').css({width:"400px"});
	} else if(window_width < 900 && window_width > 780) {
		jQuery('#product_img_slider .slides_container').css({width:"338px", height:"338px"});
		jQuery('img.prod-img').css({width:"338px"});
	} else {
		jQuery('#product_img_slider .slides_container').css({width:"284px", height:"284px"});
		jQuery('img.prod-img').css({width:"284px"});
	}

	if(window_width > 900) {
		resized_image_width = "400px";
		resized_image_after_zoom_width = "700px";
	} else if(window_width < 900 && window_width > 780) {
		resized_image_width = "338px";
		resized_image_after_zoom_width = "638px";
	} else {
		resized_image_width = "284px";
		resized_image_after_zoom_width = "557px";
	}

	$('.zoom-button').click(function() {
		if(zoom_status == 0) {
			slider_settings.minSlides = 6;
			gallery_slider.reloadSlider(slider_settings);
			$('.image_gallery_content .bx-wrapper .bx-next').css('top', '592px');
			$('.product-view .product-shop').hide();
			$('.product-view .product-img-box').css('width', '99%');
			$('#product_img_slider .slides_container').addClass('slides_container_expanded');
			$('.social-like-thumbnails-area').hide();
			$('.zoom-button').css('right', '30px');
			var available_height = parseInt($('.image_gallery_content_holder').height());
			var excess_top = (available_height - 700) * 0.5;
			$('img.prod-img').css('width', resized_image_after_zoom_width);
			$('#product_img_slider .next, #product_img_slider .prev').show();
			$(this).removeClass('zoom-in-action');
			$(this).addClass('zoom-out-action');
			zoom_status = 1;
			$(".slides_container_expanded").mCustomScrollbar({
				autoHideScrollbar:false,
				advanced:{
					updateOnBrowserResize:true,
					updateOnContentResize:true
				}
			});
		} else {
			slider_settings.minSlides = 4;
			gallery_slider.reloadSlider(slider_settings);
			$('.image_gallery_content .bx-wrapper .bx-next').css('top', '392px');
			$(this).hide();
			$('#product_img_slider .next, #product_img_slider .prev').hide();
			$('img.prod-img').css('width', resized_image_width);
			$(".slides_container").mCustomScrollbar("destroy");
			$('#product_img_slider .slides_container').removeClass('slides_container_expanded');
			$('.product-view .product-img-box').css('width', original_img_box_width);
			$('.product-view .product-shop').show();
			$('.social-like-thumbnails-area').show();
			$('.zoom-button').css('right', '15px');
			$('.zoom-button').removeClass('zoom-out-action');
			$('.zoom-button').addClass('zoom-in-action');
			$('.zoom-button').show();
			zoom_status = 0;
		}
	});

	var social_like_thumbnails_area = $('.social-like-thumbnails-area');

	social_like_thumbnails_area.mouseenter(function() {
		highlight_active_image_element();
		change_pinit_image_link();
	});

	function highlight_active_image_element() {
		$('.cloud-zoom').removeClass('the-active-image-cloud-zoom');
		$('.cloud-zoom').each(function() {
			if($(this).css('display') != 'none') {
				$(this).addClass('the-active-image-cloud-zoom');
			}
		});
	}

	function change_pinit_image_link() {
		var active_image_src = $('.prod-img', '.the-active-image-cloud-zoom').attr('src');
		pinit_image_url = active_image_src;
		var pinit_complete_url = pinit_base_url + pinit_image_url + pinit_trailing_url;
		$('.pinit_pinit_button a').attr('href', pinit_complete_url);
	}

	$(".slides_container_expanded").mCustomScrollbar({
		autoHideScrollbar:false,
		advanced:{
			updateOnBrowserResize:true,
			updateOnContentResize:true
		}
	});

	$("#product-shop-new").mCustomScrollbar({
		advanced:{
			updateOnBrowserResize:true,
			updateOnContentResize:true
		}
	});
	$(".upsell_product_buttons_container").mCustomScrollbar({
		horizontalScroll:true,
		advanced:{
			updateOnBrowserResize:true,
			updateOnContentResize:true,
			autoExpandHorizontalScroll: true
		}
	});

	// Store price box element and original base price initially
	var priceBox = $('.price-box .regular-price .price');
	var basePrice = parseInt(priceBox.rawText().replace(",", ""));

	// When an option is selected, iterate spConfig object for the additional price
	// Update the product display price accordingly
	$('.super-attribute-select').change(function() {
		var additionalPrice = 0;
		$('.super-attribute-select').each(function() {
			var select_id = $(this).attr('id').replace('attribute', '');
			var select_value = $(this).val();
			$.each(spConfig.attributes, function(data, attribute) {
				$.each(attribute.options, function(option, info) {
					if(info.id == select_value) {
						additionalPrice = additionalPrice + parseInt(info.price);
					}
				});
			});
		});
		var currency = priceBox.children();
		priceBox.text(basePrice + additionalPrice).prepend(currency);
	});

});
