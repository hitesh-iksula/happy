var activity = '';
var action = {};
var is_fpb_filter_applied = 0;
var selected_fpb_filters = {
	'gender'      : null,
	'occasion'    : null,
	'personality' : null
};
var filter_string = '.item';
var selected_option_label = '';
var href_append_string = "type=modal";

jQuery(document).ready(function($) {

	// Font Resize on Window Resize
	var newFontSize = Math.round(jQuery('.main').width()*0.01215); // ogFontSizeValue
	var newFontSizeprice = Math.round(jQuery('.main').width()*0.02604); // ogFontSizePriceValue
	var newLeftNavSizeprice = Math.round(jQuery('.main').width()*0.02730); // ogFontSizePriceValue
	var categoryheadfontSize = Math.round(jQuery('.main').width()*0.0190); // ogFontSizePriceValue
	//var newsubcategoryTitleFontSize =Math.round(jQuery('.main').width()*0.01909); // ogFontSizesubcategoryValue
	
	jQuery('.products-grid .product-name span').css('font-size',newFontSize);
	jQuery('.products-grid .price-box .regular-price').css('font-size',newFontSizeprice);
	jQuery('.cms-subcategory-area ul li a ').css('font-size',newLeftNavSizeprice);
	jQuery('.cms-subcategory-area ul li a ').css('line-height',newLeftNavSizeprice+"px");
	// jQuery('.category_head .subacategory_labels li, .category_head .category_label').css('font-size',categoryheadfontSize+"px");
	//jQuery('.category_head .subacategory_labels li').css('font-size',newsubcategoryTitleFontSize);

	/* Set Height of category thumbs */
	var category_product_image_link_width = jQuery(".products-grid li a").width();
	var category_product_image_width = jQuery(".products-grid li a img").width();
	var category_product_image_link_padding = (category_product_image_link_width - category_product_image_width);
	var category_product_title_height = jQuery(".products-grid li h2").outerHeight();
	var category_product_image_link_padding = ((category_product_image_link_padding-category_product_title_height)/2);
	var top_pos_color_swatches = category_product_image_link_width - (category_product_image_link_padding + 50);
	jQuery(".products-grid li a").height(category_product_image_width).css({'padding':category_product_image_link_padding+'px 0', 'margin':'0'});
	jQuery(".products-grid .item .color_attribute_bubble_container").css("top",top_pos_color_swatches);

	
	apply_middle_class();

	/**
	 * 
	 * 
	 * AJAX Remove From Cart
	 * 
	 * 
	 */
	$('.product-details .btn-remove').live('click', function(event) {

		if(event.handled !== true) {

			event.preventDefault();
			var parent = $(this).parent();
			var product_name = $('.product-name', parent).text();

			var confirmation = confirm('Are you sure you want to remove ' + product_name + ' from your carton?');
			
			if(confirmation == true) {

				var url = $(this).attr('href');
				var data = {};
				data.isAjax = 1;
				data.refererLocation = 'homepage';
				data.productName = product_name;
				$('#fancybox-loading').show();
				$('#fancybox-loading-overlay').show();

				try {
					$.ajax({
						url: url,
						dataType: 'json',
						type : 'post',
						data: data,
						success: function(data){					
							$('#fancybox-loading').hide();
							$('#fancybox-loading-overlay').hide();
						}
					});
				} catch (e) {
				}

			}

			event.handled = true;

		}
		return false;

	});


	/**
	 * 
	 * 
	 * Fancybox for the Product Modal
	 * 
	 * 
	 */
	winWidh =  Math.round($('.main').width());
	//alert(winWidh);

	if(winWidh > 900)
	{
		product_modal_width = 930; //Math.round($('.main').width() * 0.809)
		//alert(product_modal_width);
	} else if(winWidh < 900 && winWidh > 780) {
		product_modal_width = 780;
		//alert(product_modal_width);
	} else {
		product_modal_width = 680;
		//alert(product_modal_width);
	}

	function fancybox_footer() {
		var tailbar_content = $('.modal_tailbar_container').html();
		return tailbar_content;
	}
	
	$('.product-image').live('click', function() {

		var og_href = $(this).attr('href');
		var delimiter = "?";
		if(og_href.indexOf("?") > 0) {
			delimiter = "&";
		}
		$(this).attr('href', og_href+delimiter+href_append_string);
		$.fancybox(this, {

			"width" : product_modal_width,
			"height" : 545,
			"padding" : 0,
			"margin" : 10,
			"autoDimensions" : false,
			"autoScale" : false,
			"transitionIn" : "fade",
			"transitionOut" : "none",
			"speedIn" : 300,
			"speedOut" : 300,
			"titleFormat" : fancybox_footer,
			"overlayOpacity" : 0.6,
			"overlayColor" : "#000",
			onComplete : function(){
				$(".prod-img, .p_image_hover").trigger('loadOnComplete');

				if(selected_option_label != '') {
					change_image(selected_option_label);
				}
			},
			onClosed : function() {
				$('#fancybox-overlay').show();
				setTimeout(activity_post_product_modal_close, 100);
			}

		});

		return false;

	});

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

	function check_position_of_current_gallery_image(image_object) {
		var current_gallery_image = image_object.parent();
		var cgi_height = current_gallery_image.height();
		var cgi_index = current_gallery_image.index() + 1;
		var cgi_container = $('.image_gallery_content_holder');
		var cgi_container_height = cgi_container.height();
		var scrollbar_element = $('.mCSB_container', cgi_container);
		var offset = $('.mCSB_container', cgi_container).css('top');

		if((cgi_height * cgi_index) > cgi_container_height) {
			offset = (-1) * ((cgi_height * cgi_index) - cgi_container_height + 30);
		}
		else if(parseInt(scrollbar_element.css('top')) < (-(cgi_height * cgi_index))) {
			offset = -(cgi_height * cgi_index) + cgi_height + 0;
		}
		scrollbar_element.animate({
			top : offset
		}, 400);
	}

	$('.keep_shopping_button').live('click', function(e){
		e.preventDefault();
		$.fancybox.close();
	});
	/* Fancybox for the Product Modal ENDS */

	/**
	 * 
	 * 
	 * Fancybox for the Cart Page Modal
	 * 
	 * 
	 */
	function activity_post_product_modal_close() {
		if($('#add_cart_activity').val() == '1') {
			$('.cart_page').trigger('click');
		}
		if(action.command == 'open_new') {
			$('.product-image#product_' + action.data).trigger('click');
			action.command = 'idle';
		}
	}

	$('.cart_page').live('click', function() {

		$.fancybox(this, {
			"width" : product_modal_width,
			"height" : 540,
			"padding" : 0,
			"margin" : 0,
			"autoDimensions" : false,
			"autoScale" : false,
			"transitionIn" : "fade",
			"transitionOut" : "none",
			"speedIn" : 300,
			"speedOut" : 300,
			"titleFormat" : fancybox_footer,
			"overlayOpacity" : 0.6,
			"overlayColor" : "#000",
			onComplete: function() {
				$('#add_cart_activity').val('0');

				$('.product-name-and-options').each(function() {
					if($('.item-options', $(this)).length > 0) {
						var this_height = $(this).height();
						var options_height = $('.item-options', $(this)).height();
						var offset = (75 - parseInt(this_height)) / 2;
						$('.product-name', $(this)).css('margin-top', offset);
					}
				});

				var upsell_slider_list_width = Math.floor($("#fancybox-wrap").width()/4);				
				$('.upsell_items').bxSlider({
					pager: false,
					prev_image: 'images/general/sprite_with_vodka.png',
					next_image: 'images/general/sprite_with_vodka.png',
					wrapper_class: 'global_upsells_container',
					slideWidth : upsell_slider_list_width,
					// margin: 20,
					auto: false,
					auto_controls: false,
					minSlides: 4,
					maxSlides: 4,
					moveSlides: 1
					
				});
			},
			onClosed: function() {
				$('#fancybox-overlay').show();
				setTimeout(activity_post_product_modal_close, 100);
			}
		});

		return false;
	});
	/* Fancybox for the Cart Page Modal ENDS */

	/**
	 * 
	 * 
	 * Solidswatches | Change Product Image When Clicking On A Swatch
	 * 
	 * 
	 */
	$('.color_attribute_bubble').click(function(){
		
		if(!$(this).hasClass('active_bubble')) {
			var parent = $(this).closest('.item');
			var option_label = $(this).attr('title');
			var product_id = parent.attr('id');
			// selected_option_label = option_label;

			$('.color_attribute_bubble', parent).removeClass('active_bubble');
			$(this).addClass('active_bubble');
			
			$('.item-image', parent).fadeTo('fast', 0.33);
			$.ajax({
				url : $('#url_for_swatches').val(),
				type : 'post',
				data : { 'option_label' : option_label, 'product_id' : product_id },
				success : function(data){
					$('.item-image', parent).attr('src', data);
					$('.item-image', parent).fadeTo('fast', 1);
					var quick_link = $('.product-image', parent).attr('href');
					arr_quick_link = quick_link.split("?");
					og_quick_link = arr_quick_link[0];
					var updated_quick_link = og_quick_link + "?option=" + encodeURIComponent(option_label);
					$('.product-image', parent).attr('href', updated_quick_link);
				}
			});
		}

	});

	/**
	 * 
	 * 
	 * Find Product By Functionality | START
	 * 
	 *
	 */

	// Click anywhere on the body to close the filter drop-downs
	$('body').click(function(evt){
		if(evt.target.className == "filter-selected-textfield" || evt.target.className == "active_filter_image_image") {
			return false;
		}

		$('.filter-dropdown-area').removeClass('active-dropdown');
		$('.filter-dropdown-area').slideUp("fast");
	});

	$('.filter-selected-textfield').click(collapse_filter_options); 	// Event on clicking a Filter's Label
	$('.active_filter_image').click(collapse_filter_options); 			// Event on clicking a Filter's Image
	// $('.filter-dropdown-imgArea').hover(show_option_title); 			// Event on Hovering over a filter's Drop-down Options
	$('.filter-dropdown-imgArea').click(select_explore_filter); 		// Event on Clicking a filter's Drop-down Option
	$('.findproductbysearchi').click(apply_explore_filters);			// Event on Applying Filter
	$('.findproductbyrefreshi').click(reset_explore_filters);			// Event on Resetting Filter
	
	// Function that collapses filter options for the selected filter
	function collapse_filter_options(){
		
		$(".active_filter_image_image, .active_filter_active_image").trigger('open');
		var parent = $(this).parent();
		var newMargdropdown = (parseInt($('.filterArea').width()) - 48 + parseInt($('.active_filter_image', parent).width()))/2;
		if(!$('.filter-dropdown-area', parent).hasClass('active-dropdown')) {
			$('.active-dropdown').slideUp("fast");
		}
		$(this).css({'position':'relative'});
		$('.filter-dropdown-area').removeClass('active-dropdown');
		$('.filter-dropdown-area', parent).addClass('active-dropdown');
		$('.filter-dropdown-area', parent).slideToggle("fast");
		$('.filter-dropdown-area', parent).css({'margin-left':newMargdropdown})
		// $('.filter-dropdown-imgArea .dropdownimg-title', parent).css("display","none");

	}
	
	// Function that shows titles of drop-down options when hovering over their images
	function show_option_title(){
		$('.dropdownimg-title', $(this)).stop(true, true).fadeToggle(400);
	}
	
	// Function that adds a filter (without applying it) when clicking on it
	function select_explore_filter(){

		var fetchNewData = $('.active_option_image', $(this)).html();
		var parent = $(this).parent().parent();
		
		setTimeout(function(){
			$('.active_filter_image', parent).html(fetchNewData);
			$('.active_filter_image', parent).fadeIn();
		}, 300);
		
		$('.filter-selected-textfield', parent).css({'position':'inherit'});
		
		$('.filter-dropdown-area', parent).slideToggle("fast");
		$('.filter-selected-textfield').css({'position':'inherit'});

		filter_id = parent.attr('id');
		selected_fpb_filters[filter_id] = $(this).attr('id');

		$('.result_tooltip_box').fadeIn();
		setTimeout(function() {
			$('.result_tooltip_box').fadeOut();
		}, 5000);

	}

	// Function that applies all selected filters in 'AND' fashion
	function apply_explore_filters(){

		if(!(selected_fpb_filters.gender == null && selected_fpb_filters.occasion == null && selected_fpb_filters.personality == null)) {
			activity = 'apply_explore_filters';
			is_fpb_filter_applied = 1;
			filter_string = '';
			$.each(selected_fpb_filters, function(index, value) {
				if(value != null) {
					filter_string += '.' + value;
				}
			});

			$('.item', '.one_category').hide();
			$(filter_string).show();

			apply_middle_class();

			$('.result_tooltip_box').hide();
			$('.reset_tooltip_box').fadeIn();
			setTimeout(function() {
				$('.reset_tooltip_box').fadeOut();
			}, 10000);
		}

	}

	// Function that Resets all filters and brings page state back to default
	function reset_explore_filters(){

		if(!(selected_fpb_filters.gender == null && selected_fpb_filters.occasion == null && selected_fpb_filters.personality == null)) {

			activity = 'reset_explore_filters';
			$('.active_filter_image').fadeToggle();
			setTimeout(function(){
				$('.active_filter_image').html('');
			}, 400);

			selected_fpb_filters = {
				'gender'      : null,
				'occasion'    : null,
				'personality' : null
			};

			if(is_fpb_filter_applied == 1) {

				is_fpb_filter_applied = 0;
				$('.item', '.one_category').show();
				filter_string = '.item';
				apply_middle_class();

			}

		}

	}

	function apply_middle_class() {

		$('.item', '.one_category').removeClass('middle');
		// Add class 'middle' for the middle element in the Grid
		var middle_count = 0;
		$(filter_string, '.one_category').each(function() {
			middle_count++;
			if(middle_count % 3 == 2){
				$(this).addClass('middle');
			} else {
				$(this).removeClass('middle');
			}
		});

	}
	/* Find Product By Functionality | END */


	/**
	 * 
	 * 
	 * Scroll To Top Button | START
	 * 
	 *
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

	/* Scroll To Top Button | END */



});

jQuery(window).resize(function(){	 

	// Font Resize on Window Resize
	var newFontSize = Math.round(jQuery('.main').width()*0.01215); // ogFontSizeValue
	var newFontSizeprice = Math.round(jQuery('.main').width()*0.02604); // ogFontSizePriceValue
	var newLeftNavSizeprice = Math.round(jQuery('.main').width()*0.02730); // ogFontSizePriceValue
	var categoryheadfontSize = Math.round(jQuery('.main').width()*0.0190); // ogFontSizePriceValue
	//var newsubcategoryTitleFontSize =Math.round(jQuery('.main').width()*0.01909); // ogFontSizesubcategoryValue

	jQuery('.products-grid .product-name span').css('font-size',newFontSize);
	jQuery('.products-grid .price-box .regular-price').css('font-size',newFontSizeprice);
	jQuery('.cms-subcategory-area ul li a ').css('font-size',newLeftNavSizeprice);
	jQuery('.cms-subcategory-area ul li a ').css('line-height',newLeftNavSizeprice+"px");
	// jQuery('.category_head .subacategory_labels li, .category_head .category_label').css('font-size',categoryheadfontSize+"px");
	//jQuery('.category_head .subacategory_labels li').css('font-size',newsubcategoryTitleFontSize);

	/* Set Height of category thumbs */
	var category_product_image_link_width = jQuery(".products-grid li a").width();
	var category_product_image_width = jQuery(".products-grid li a img").width();
	var category_product_image_link_padding = (category_product_image_link_width - category_product_image_width);
	var category_product_title_height = jQuery(".products-grid li h2").outerHeight();
	var category_product_image_link_padding = ((category_product_image_link_padding-category_product_title_height)/2);
	var top_pos_color_swatches = category_product_image_link_width - (category_product_image_link_padding + 50);
	jQuery(".products-grid li a").height(category_product_image_width).css({'padding':category_product_image_link_padding+'px 0', 'margin':'0'});
	jQuery(".products-grid .item .color_attribute_bubble_container").css("top",top_pos_color_swatches);

});