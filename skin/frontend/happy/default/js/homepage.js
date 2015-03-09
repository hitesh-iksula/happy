// Global Variable declaration
var current_filter = {};
var current_page = {};
var multiplier = 12;
var grid_size = multiplier - 1;
var initial = 1;
var activity = '';
var is_fpb_filter_applied = 0;
var convoluted_filter_string = '';
var visible_product_count = {};
var href_append_string = "type=modal";
var show_modal_of_product = '';

var product_modal_load_time = 0;
var product_modal_load_before = 0;
var product_modal_load_after = 0;

/*var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-44862411-1']);
_gaq.push(['_setAllowLinker', true]);
_gaq.push(['_trackPageview']);

(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();*/

var f1 = "5px";
var f2 = "-12px";

var selected_fpb_filters = {
	'gender'      : null,
	'occasion'    : null,
	'personality' : null
};
var filter_string = '';

var product_modal_width = 0;

var left_offset_of_footer_button = 0;

var window_width = 0;

var action = {};

var selected_option_label = '';

/**
 * 
 * 
 * Cufon Declaration
 * 
 * 
 */
/*Cufon.replace('body', { fontFamily: 'bentonsansf-book', hover: true });
function handleError(){
	return true;
}
window.onerror = handleError;*/
/* Cufon Declaration Ends */

jQuery(document).ready(function($) {

	/* Set height of banner */
	var master_width = $(".main").width();
	var slider_banner_height =Math.floor (master_width * 0.412326389);
	$(".slider-wrapper").css("height",slider_banner_height);
	
	/* modifications made for getting a better loading experience */
	var document_height = $(window).height();
	$('.master').css('min-height', document_height);
	$('.one_category').show();
	
	/* Set Height of category thumbs */
	var category_product_image_link_width = $(".products-grid li a").width();
	var category_product_image_width = $(".products-grid li a img").width();
	var category_product_image_link_padding = (category_product_image_link_width - category_product_image_width);
	var category_product_title_height = $(".products-grid li h2").outerHeight();
	var category_product_image_link_padding = ((category_product_image_link_padding-category_product_title_height)/2);
	var top_pos_color_swatches = category_product_image_link_width - (category_product_image_link_padding + 50);
	jQuery(".products-grid li a").height(category_product_image_width).css({'padding':category_product_image_link_padding+'px 0', 'margin':'0'});
	jQuery(".products-grid .item .color_attribute_bubble_container").css("top",top_pos_color_swatches);

	if($('.col-main > ul.messages').length > 0) {
		setTimeout(function () {
			$('.col-main > ul.messages').fadeOut('slow');
		}, 4000);
	}

	multiplier = $('#visible_products_per_grid').val();
	grid_size = multiplier - 1;

	// LazyLoader Initiation
	$(".item-image, .ecategory_thumbnail").lazyload({ 
		effect : "fadeIn",
		threshold : 200,
		skip_invisible : false
	});
	$(".item-image, .ecategory_thumbnail").lazyload({ 
		effect : "fadeIn",
		threshold : 200,
		skip_invisible : false,
		event : 'loadNext'
	});
	$(".active_filter_image_image, .active_filter_active_image").lazyload({ 
		effect : "fadeIn",
		threshold : 100,
		event : "open"
	});

	// $("html").niceScroll();

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
		product_modal_load_before = $.now();
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

				product_modal_load_after = $.now();
				product_modal_load_time = product_modal_load_after - product_modal_load_before;
				// alert(product_modal_load_time);
				if(typeof _gaq !== 'undefined') {
					_gaq.push(['_trackTiming', 'product_modal', 'open_time', product_modal_load_time, 'animation', 100]);
				}
			},
			onClosed : function() {
				$('#fancybox-overlay').show();
				setTimeout(activity_post_product_modal_close, 100);
			}

		});
		$(this).attr('href', og_href);

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

	/* opens product modal if url key is supplied in homepage url */
	if($('#show_modal_of_product').length > 0) {
		show_modal_of_product = $('#show_modal_of_product').val();
		$(document).scrollTop(0);
		$('#product_' + show_modal_of_product).first().trigger('click');
	}
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
					
				})

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

	// Footer Slide Up and Slide Down
	$('.slideup').click(function(){
		$(".footer_payment_options").trigger('open');
		$('.slideContent').slideToggle("1000");
		$('.up_arrow', $(this)).toggleClass('down_arrow')
	});

	// Hide border of last UL in Footer Links
	$('.facepileArea1 ul:last').css("border-right", "none");

	// Declare STICKYHEADER
	$('.category_head').filter(':first').css('border-top', 'none');
	$('.one_category').stickyHeader({stickyClass : 'category_head'});

	// Navigation Slider for the Category Floater
	var scroll_offset_on_label_click = 85;
	if(($(window).width()) <= 900) {
		scroll_offset_on_label_click = 126;
	}
	$('.left_categories_floater').onePageNav({
		scrollOffset: scroll_offset_on_label_click,
		easing: 'swing',
		scrollSpeed: 1200
	});

	// make first category on the left current category
	$('#floating_categories .categories').first().addClass('current');

	// Disable sticky header functionality temporarily when scrolling with left floater
	$('.left_categories_floater .categories a').click(function() {
		sticky_status = 0;
		setTimeout(function() {
			sticky_status = 1;
		}, 1400);
	});

	// Stop Animations (Clear Queue) on a burst of clicks on the Category Floater
	$('#floating_categories .categories').click(function() {
		$('html, body').clearQueue();
	});


	// Initially, count number of products in each category and show only 9 products. Show a "Show More" button
	$('.products-grid').each(paginate);
	initial = 0;

	// Show 9 more products on "Show More". Hide "Show More" if the last segment is visible.
	$('.show_more').click(show_more);

	// Hide last 9 products from the category. Hide "Show Less" if on the first segment.
	$('.show_less').click(show_less);

	// Subcategory Sorting
	$('.subcategory_label').click(apply_subcategory_filter);
	
	// Function called for Pagination
	function paginate(){

		var id = $(this).attr('id');
		current_page[id] = 1;

		var parent_element = $(this).closest('.one_category');
		if(initial == 1){
			var cat_id = id.replace('container', 'cat');
			current_filter[id] = cat_id;
		} else {
			var cat_id = current_filter[id];
		}

		convoluted_filter_string = convolute_and_apply_filters(current_filter[id]);
		
		// Show message when no products match the selection
		var no_of_products = $('.'+convoluted_filter_string, $(this)).length;
		visible_product_count[id] = no_of_products;
		if(no_of_products == 0) {
			if($('.no_matching_products', parent_element).length == 0) {
				parent_element.append($('.no_matching_products_envelope').html());
			}			
		} else {
			$('.no_matching_products', parent_element).remove();
		}

		if(no_of_products < 3) {
			container_height_threshold_crossed = 1;
		// } else {
		// 	container_height_threshold_crossed = 0;
		}
		
		// If a category has no visible products, Hide it and its Floater to the Left
		// var left_cat_id = id.replace('container', 'left');
		// if(no_of_products == 0 && activity != 'subcategory_filter') {
		// 	parent_element.hide();
		// 	$('#'+left_cat_id, '.left_categories_floater').hide();
		// } else {
		// 	parent_element.show();
		// 	$('#'+left_cat_id, '.left_categories_floater').show();
		// }

		if(activity == 'apply_explore_filters') {
			var left_cat_id = id.replace('container', 'left');
			var no_of_products_excl_subcategories = $(filter_string, $(this)).length;
			$('#'+left_cat_id+' .product_count', '.left_categories_floater').html(no_of_products_excl_subcategories);
			$('#'+left_cat_id+' .product_count', '.left_categories_floater').show();
		}
		
		// Add class 'middle' for the middle element in the Grid
		var middle_count = 0;
		$('.'+convoluted_filter_string, $(this)).each(function() {
			middle_count++;
			if(middle_count % 3 == 2){
				$(this).addClass('middle');
			} else {
				$(this).removeClass('middle');				
			}
		});
		
		// Show / Hide the Show More and Show Less buttons
		if(no_of_products > (grid_size + 1)) {

			$('.'+convoluted_filter_string+':gt('+grid_size+')', parent_element).hide();

			$('.show_more_and_less', parent_element).show();
			$('.show_more', parent_element).show();
			$('.show_less', parent_element).hide();
			$('.button_spacing', parent_element).hide();

		} else {

			$('.show_more_and_less', parent_element).hide();

		}

	}

	// Function called when Show More is clicked
	function show_more(){
		
		activity = 'show_more';
		var id = $(this).attr('id');
		id = id.replace('more', 'container');
		
		page_no = current_page[id];
		cat_id = current_filter[id];

		var start = page_no * multiplier;
		page_no += 1;
		current_page[id] = page_no;
		var end = page_no * multiplier;
		$('.'+cat_id, $('#'+id)).slice(start, end).fadeIn();

		var products_in_category = $('.'+cat_id, $('#'+id)).length;

		// Show / Hide the Show More and Show Less buttons
		if(end >= products_in_category) {

			$('.show_more', $('#'+id).parent()).hide();
			$('.show_less', $('#'+id).parent()).show();
			$('.button_spacing', $('#'+id).parent()).hide();

		} else {
			
			$('.show_more', $('#'+id).parent()).show();
			$('.show_less', $('#'+id).parent()).show();
			$('.button_spacing', $('#'+id).parent()).show();

		}

	}

	// Function called when Show Less is clicked
	function show_less(){

		activity = 'show_less';
		var previous_position = $(this).offset().top;
		var id = $(this).attr('id');
		id = id.replace('less', 'container');

		page_no = current_page[id];
		cat_id = current_filter[id];

		var end = page_no * multiplier;
		page_no -= 1;
		current_page[id] = page_no;
		var start = page_no * multiplier;

		var total_products_in_category = $('.'+cat_id, $('#'+id)).length;

		// Animate Scrolling Up
		var show_less_scroll_offset = 100;
		var scroll_by = $('.products-grid .item:first').height() * (Math.ceil((total_products_in_category - start) / 3)) + show_less_scroll_offset;
			
		$('html, body').animate({scrollTop: '-='+scroll_by}, 800);
		setTimeout(function() {
			$('.'+cat_id, $('#'+id)).slice(start, end).fadeOut();
		}, 800);
		
		// Show / Hide the Show More and Show Less buttons
		if(page_no == 1) {

			$('.show_more', $('#'+id).parent()).show();
			$('.show_less', $('#'+id).parent()).hide();
			$('.button_spacing', $('#'+id).parent()).hide();
			
		} else {
			
			$('.show_more', $('#'+id).parent()).show();
			$('.show_less', $('#'+id).parent()).show();
			$('.button_spacing', $('#'+id).parent()).show();

		}
		
	}

	// Apply Subcategory Filter when a Subcategory Label is clicked
	function apply_subcategory_filter(){

		activity = 'subcategory_filter';
		var cat_id = $(this).attr('id');
		var id = $(this).closest('.one_category').attr('id');
		id = id.replace('category', 'container');
		
		current_filter[id] = cat_id;
		current_page[id] = 1;

		convoluted_filter_string = convolute_and_apply_filters(cat_id);
				
		$('.item', '#'+id).hide();
		$('.'+convoluted_filter_string, '#'+id).show();

		var parent = $(this).parent();
		$('.subcategory_label', parent).removeClass('active_label');
		$(this).addClass('active_label');

		// Trigger loading of images in the next category group
		$(".item-image, .category_thumbnail", $(this).closest('.one_category').next()).trigger('loadNext');

		// Trigger loading of image in category product thumbnail clicking on subcategory
		// alert($(".category-products ."+cat_id+" .item-image", $(this).closest('.one_category')).length);
		$(".category-products ."+cat_id+" .item-image", $(this).closest('.one_category')).trigger('loadNext');



		// Paginate
		$('#'+id).each(paginate);

	}

	// Combines category/subcategory filters with find products by filters
	function convolute_and_apply_filters(cat_id) {

		convoluted_filter_string = cat_id;
		if(is_fpb_filter_applied == 1) {
			convoluted_filter_string = cat_id + filter_string;
		}
		return convoluted_filter_string;

	}

	// Font Resize variables
	// var productTilteFontSize = parseInt(jQuery('.products-grid .product-name span').css('font-size')); /*get font size of product title*/
	// var ogFontSizeValue =(productTilteFontSize/jQuery('.main').width()); /*generate value which will multiple with resizing width of .main class to get new font size */
	// var productPriceFontSize = parseInt(jQuery('.products-grid .price-box .regular-price').css('font-size')); /*get font size of product title*/
	// var ogFontSizePriceValue =(productPriceFontSize/jQuery('.main').width()); /*generate value which will multiple with resizing width of .main class to get new font size of product price */
	// var subcategoryTitleFontSize = parseInt(jQuery('.category_head .subacategory_labels li').css('font-size')); /*get font size of product title*/
	// var ogFontSizesubcategoryValue =(subcategoryTitleFontSize/jQuery('.main').width()); /*generate value which will multiple with resizing width of .main class to get new font size of product price */

	// Font Resize on Document Ready
	var newFontSize = Math.round(jQuery('.main').width()*0.014); // ogFontSizeValue
	var newFontSizeprice = Math.round(jQuery('.main').width()*0.0222); // ogFontSizePriceValue
	var newLeftNavSizeprice =Math.round(jQuery('.main').width()*0.02430); // ogFontSizePriceValue
	var categoryheadfontSize =Math.round(jQuery('.main').width()*0.0190); // ogFontSizePriceValue
	//var newsubcategoryTitleFontSize = Math.round(jQuery('.main').width()*0.01909); // ogFontSizesubcategoryValue

	jQuery('.products-grid .product-name .product_name_title').css('font-size',newFontSize);
	jQuery('.products-grid .price-box .regular-price').css('font-size',newFontSizeprice);
	jQuery('.left_categories_floater ul li.categories a ').css('font-size',newLeftNavSizeprice);
	jQuery('.left_categories_floater ul li.categories a ').css('line-height',newLeftNavSizeprice+"px");
	jQuery('.category_head .subacategory_labels li, .category_head .category_label').css('font-size',categoryheadfontSize+"px");
	//jQuery('.category_head .subacategory_labels li').css('font-size',newsubcategoryTitleFontSize);
	
	if((jQuery(window).width())<=900){
		jQuery('.block-cart').css("margin-left",jQuery('.main').width()-80+"px");
	}
	
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

			var current_category = '';
			if($('.categories.current').length > 0) {
				current_category = $('.categories.current');
				current_category = current_category.attr('id').replace('left', 'category');
			}

			$('.item', '.one_category').hide();
			$('.products-grid').each(function(){
				var id = $(this).attr('id');
				// current_page[id] = 1;
				convoluted_filter_string = convolute_and_apply_filters(current_filter[id]);
				$('.'+convoluted_filter_string, $(this)).show();
			});
			$('.products-grid').each(paginate);

			$('.category_head').removeClass('the_fixed_header');

			if(current_category != '') {
				var current_category_top = $('.one_category#' + current_category).offset().top - 82;
				$('html,body').scrollTop(current_category_top);
				
				setTimeout(function() {
					$('.categories', '.left_categories_floater').removeClass('current');
					$('.categories#' + current_category.replace('category', 'left')).addClass('current');
				}, 100);
				
			}

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

				var current_category = '';
				if($('.categories.current').length > 0) {
					current_category = $('.categories.current');
					current_category = current_category.attr('id').replace('left', 'category');
				}
				
				$('.item', '.one_category').hide();
				$('.products-grid').each(function(){
					var id = $(this).attr('id');
					// current_page[id] = 1;
					convoluted_filter_string = convolute_and_apply_filters(current_filter[id]);
					$('.'+convoluted_filter_string, $(this)).show();
				});
				$('.products-grid').each(paginate);
				
				$('.category_head').css('position', 'static');
				
				if(current_category != '') {
					var current_category_top = $('.one_category#' + current_category).offset().top - 82;
					
					$('html,body').scrollTop(current_category_top);
					
					if($('.the_fixed_header').length > 0) {
						$('.the_fixed_header').css('position', 'fixed');
						$('.the_fixed_header').removeClass('the_fixed_header');
					} else if($('.one_category#' + current_category + ' .category_head').css('top') != '82px' && $('.one_category#' + current_category + ' .category_head').css('top') != 'auto') {
						$('.one_category#' + current_category + ' .category_head').css('position', 'fixed');
						$('.one_category#' + current_category + ' .category_head').css('top', '82px');
					}
					setTimeout(function() {
						$('.categories', '.left_categories_floater').removeClass('current');
						$('.categories#' + current_category.replace('category', 'left')).addClass('current');
					}, 100);
				}

				initial = 0;
				filter_string = '';
				$('.result_tooltip_box').hide();
				$('.reset_tooltip_box').hide();
				// $('.subacategory_labels').show();
				$('.product_count', '.left_categories_floater').html('');
				$('.product_count', '.left_categories_floater').hide();

			}

		}

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

	/**
	 *
	 *
	 *Scroll Dowm button hide on scroll | START
	 *
	 *
	 */

	 $(window).scroll(scroll_down_hide);

	 function scroll_down_hide(){
		if ($(window).scrollTop() >= 50) {
			$('#scroll_down_btn').fadeOut("slow");
		}
	 }

	 $(document).keyup(function(){
	 	if($(window).scrollTop()==0){
	 		$(".left_categories_floater ul li").removeClass("current");
	 	}
	 });

	 



	 //alert(url_path);

	/* Scroll Dowm button hide on scroll | END */
	
	/* Left Category Script*/
	$('.left_categories_floater ul li.categories a').hover(function(){
		$('.left_category_hover_effect',$(this)).stop(true,true).fadeToggle(400);
	});

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

	//alert(navigator.platform);

	// /* Set height to category thumbs */
	// var category_product_image_height = $(".products-grid li a").height();
	// var category_product_title_height = $(".products-grid li h2").height();
	// var category_product_thumb_height = Math.floor( category_product_image_height + category_product_title_height + 18 );
	// $(".products-grid li").height(category_product_thumb_height);
	// $(window).scroll(function(){
	// 	$(".products-grid li").height(category_product_thumb_height);	
	// });
		



	/* Set default width to search input field on focusout */
	var in_val = $("#search").val();

	if(in_val != "")
	{
		//alert("hi");
		$("#search").width("108px");
	}

	$("#search").focusin(function(){
		$(this).width("108px");    	
	});

	$("#search").focusout(function(){
		var in_val = $(this).val();
    	if(in_val != "" )
    	{
    		$(this).width("108px");
    	}else{
    		$(this).width("38px");
    	}
  	});

	/**
	 * 
	 * 
	 * FAQ Functionality has been shifted back to the phtml file as accordion does not get loaded when AJAX call reloads the sidebar
	 * 
	 * 
	 */
	/*$('#faq_accordion').accordion();
	$(".loading").removeClass("loading");	
	var faq_status = 'closed';
	$("#faq_toggle").live('click', function(){

		if((jQuery(window).width()) <= 900){
			f1 = "18px";
			f2 = "1px";
		} else {
			f1 = "5px";
			f2 = "-12px";
		}
		if(faq_status == 'closed'){
			$('.faq_envelope').css('right', f1);
			$('.faq_envelope').animate({
				width: '212px'
			});
			
			$('.faq_toggle').toggleClass('faq_toggle_open');
			faq_status = 'opened';
		} else if(faq_status == 'opened') {
			$('.faq_envelope').animate({
				width: '17px'
			}, 400, 'swing', function(){
				$('.faq_toggle').toggleClass('faq_toggle_open');
				$('.faq_envelope').animate({
					right : f2
				}, 300);
				faq_status = 'closed';
			});
		}

	});*/

if(navigator.platform == 'iPad' || navigator.platform == 'iPhone' || navigator.platform == 'iPod'){
	$(".products-grid .item .color_attribute_bubble").css("height","15px");
	$(".products-grid .item .color_attribute_bubble").css("width","15px");
	$(".products-grid .item .color_attribute_bubble").css("margin-left","3px");
	$(".products-grid .item .color_attribute_bubble").css("margin-right","3px");
}
	
});

jQuery(window).resize(function(){

	/* Set height of banner */
	var master_width = jQuery(".main").width();
	var slider_banner_height =Math.floor (master_width * 0.412326389);
	jQuery(".slider-wrapper").css("height",slider_banner_height);

	jQuery('.block-cart').css("margin-left",jQuery('.main').width()-100+"px");

	// jQuery('.one_category').stickyHeader({stickyClass : 'category_head'});

	// Font Resize on Window Resize
	var newFontSize = Math.round(jQuery('.main').width()*0.014); // ogFontSizeValue
	var newFontSizeprice = Math.round(jQuery('.main').width()*0.0222); // ogFontSizePriceValue
	var newLeftNavSizeprice = Math.round(jQuery('.main').width()*0.02430); // ogFontSizePriceValue
	var categoryheadfontSize = Math.round(jQuery('.main').width()*0.0190); // ogFontSizePriceValue
	//var newsubcategoryTitleFontSize =Math.round(jQuery('.main').width()*0.01909); // ogFontSizesubcategoryValue

	jQuery('.products-grid .product-name .product_name_title').css('font-size',newFontSize);
	jQuery('.products-grid .price-box .regular-price').css('font-size',newFontSizeprice);
	jQuery('.left_categories_floater ul li.categories a ').css('font-size',newLeftNavSizeprice);
	jQuery('.left_categories_floater ul li.categories a ').css('line-height',newLeftNavSizeprice+"px");
	jQuery('.category_head .subacategory_labels li, .category_head .category_label').css('font-size',categoryheadfontSize+"px");
	//jQuery('.category_head .subacategory_labels li').css('font-size',newsubcategoryTitleFontSize);
	
	if((jQuery(window).width()) <= 900){
		jQuery('.block-cart').css("margin-left",jQuery('.main').width()-80+"px");	
	}

	// winWidh =  Math.round(jQuery('.main').width());
	// //alert(winWidh);

	// // Fancybox for the Product Modal
	// if(winWidh > 900)
	// {
	// 	product_modal_width = Math.round(jQuery('.main').width() * 0.809);
	// 	//alert(product_modal_width);
	// } else if(winWidh < 900 && winWidh > 780) {
	// 	product_modal_width = 780;
	// 	//alert(product_modal_width);
	// } else {
	// 	product_modal_width = 680;
	// 	//alert(product_modal_width);
	// }

	/*if((jQuery(window).width()) <= 900){
		jQuery('.block-cart').css("margin-left",jQuery('.main').width()-80+"px");	
		jQuery('.faq_envelope').css('right', '1px');		
	} else {
		jQuery('.faq_envelope').css('right', '-12px');
	}*/

	
	var header_width = jQuery('.one_category').css('width');
	jQuery('.the_fixed_header').css('width', header_width);

	// /* Set height to category thumbs */
	// var category_product_image_height = jQuery(".products-grid li a").height();
	// var category_product_title_height = jQuery(".products-grid li h2").height();
	// var category_product_thumb_height = Math.floor( category_product_image_height + category_product_title_height + 18 );
	// jQuery(".products-grid li").height(category_product_thumb_height);

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






/* Simple Image Panner and Zoomer (March 11th, 10)
* This notice must stay intact for usage 
* Author: Dynamic Drive at http://www.dynamicdrive.com/
* Visit http://www.dynamicdrive.com/ for full source code
*/

// v1.1 (March 25th, 10): Updated with ability to zoom in/out of image



/* Simple Image Panner and Zoomer (March 11th, 10)
* This notice must stay intact for usage 
* Author: Dynamic Drive at http://www.dynamicdrive.com/
* Visit http://www.dynamicdrive.com/ for full source code
*/

// v1.1 (March 25th, 10): Updated with ability to zoom in/out of image

jQuery.noConflict()

var ddimagepanner={

	magnifyicons: ['magnify.gif','magnify2.gif', 24,23], //set path to zoom in/out images, plus their dimensions
	maxzoom: 4, //set maximum zoom level (from 1x)

	init:function($, $img, options){
		var s=options
		s.imagesize=[$img.width(), $img.height()]
		s.oimagesize=[$img.width(), $img.height()] //always remember image's original size
		s.pos=(s.pos=="center")? [-(s.imagesize[0]/2-s.wrappersize[0]/2), -(s.imagesize[1]/2-s.wrappersize[1]/2)] : [0, 0] //initial coords of image
		s.pos=[Math.floor(s.pos[0]), Math.floor(s.pos[1])]
		$img.css({position:'absolute', left:s.pos[0], top:s.pos[1]})
		if (s.canzoom=="yes"){ //enable image zooming?
			s.dragcheck={h: (s.wrappersize[0]>s.imagesize[0])? false:true, v:(s.wrappersize[1]>s.imagesize[1])? false:true} //check if image should be draggable horizon and vertically
			s.$statusdiv=$('<div style="position:absolute;color:white;background:#353535;padding:2px 10px;font-size:12px;visibility:hidden">1x Magnify</div>').appendTo(s.$pancontainer) //create DIV to show current magnify level
			s.$statusdiv.css({left:0, top:s.wrappersize[1]-s.$statusdiv.outerHeight(), display:'none', visibility:'visible'})
			this.zoomfunct($, $img, s)
		}
		this.dragimage($, $img, s)
	},

	dragimage:function($, $img, s){
		$img.mousedown(function(e){
			s.pos=[parseInt($img.css('left')), parseInt($img.css('top'))]
			var xypos=[e.clientX, e.clientY]
			$img.bind('mousemove.dragstart', function(e){
				var pos=s.pos, imagesize=s.imagesize, wrappersize=s.wrappersize
				var dx=e.clientX-xypos[0] //distance to move horizontally
				var dy=e.clientY-xypos[1] //vertically
				s.dragcheck={h: (wrappersize[0]>imagesize[0])? false:true, v:(wrappersize[1]>imagesize[1])? false:true}
				if (s.dragcheck.h==true) //allow dragging horizontally?
					var newx=(dx>0)? Math.min(0, pos[0]+dx) : Math.max(-imagesize[0]+wrappersize[0], pos[0]+dx) //Set horizonal bonds. dx>0 indicates drag right versus left
				if (s.dragcheck.v==true) //allow dragging vertically?
					var newy=(dy>0)? Math.min(0, s.pos[1]+dy) : Math.max(-imagesize[1]+wrappersize[1], pos[1]+dy) //Set vertical bonds. dy>0 indicates drag downwards versus up
				$img.css({left:(typeof newx!="undefined")? newx : pos[0], top:(typeof newy!="undefined")? newy : pos[1]})
				return false //cancel default drag action
			})
			return false //cancel default drag action
		})
		$(document).bind('mouseup', function(e){
			$img.unbind('mousemove.dragstart')
		})
	},

	zoomfunct:function($, $img, s){
		var magnifyicons=this.magnifyicons
		var $zoomimages=$('<img src="'+magnifyicons[0]+'" /><img src="'+magnifyicons[1]+'" />')
			.css({width:magnifyicons[2], height:magnifyicons[3], cursor:'pointer', zIndex:1000, position:'absolute',
						top:s.wrappersize[1]-magnifyicons[3]-1, left:s.wrappersize[0]-magnifyicons[2]-3, opacity:0.7
			})
			.attr("title", "Zoom Out")
			.appendTo(s.$pancontainer)
		$zoomimages.eq(0).css({left:parseInt($zoomimages.eq(0).css('left'))-magnifyicons[2]-3, opacity:1}) //position "zoom in" image
			.attr("title", "Zoom In")
		$zoomimages.click(function(e){ //assign click behavior to zoom images
			var $zimg=$(this) //reference image clicked on
			var curzoom=s.curzoom //get current zoom level
			var zoomtype=($zimg.attr("title").indexOf("In")!=-1)? "in" : "out"
			if (zoomtype=="in" && s.curzoom==ddimagepanner.maxzoom || zoomtype=="out" && s.curzoom==1) //exit if user at either ends of magnify levels
				return
			var basepos=[s.pos[0]/curzoom, s.pos[1]/curzoom]
			var newzoom=(zoomtype=="out")? Math.max(1, curzoom-1) : Math.min(ddimagepanner.maxzoom, curzoom+1) //get new zoom level
			$zoomimages.css("opacity", 1)
			if (newzoom==1) //if zoom level is 1x, dim "zoom out" image
				$zoomimages.eq(1).css("opacity", 0.7)
			else if (newzoom==ddimagepanner.maxzoom) //if zoom level is max level, dim "zoom in" image
				$zoomimages.eq(0).css("opacity", 0.7)
			clearTimeout(s.statustimer)
			s.$statusdiv.html(newzoom+"x Magnify").show() //show current zoom status/level
			var nd=[s.oimagesize[0]*newzoom, s.oimagesize[1]*newzoom]
			var newpos=[basepos[0]*newzoom, basepos[1]*newzoom]
			newpos=[(zoomtype=="in" && s.wrappersize[0]>s.imagesize[0] || zoomtype=="out" && s.wrappersize[0]>nd[0])? s.wrappersize[0]/2-nd[0]/2 : Math.max(-nd[0]+s.wrappersize[0], newpos[0]),
				(zoomtype=="in" && s.wrappersize[1]>s.imagesize[1] || zoomtype=="out" && s.wrappersize[1]>nd[1])? s.wrappersize[1]/2-nd[1]/2 : Math.max(-nd[1]+s.wrappersize[1], newpos[1])]
			$img.animate({width:nd[0], height:nd[1], left:newpos[0], top:newpos[1]}, function(){
				s.statustimer=setTimeout(function(){s.$statusdiv.hide()}, 500)
			})
			s.imagesize=nd
			s.curzoom=newzoom
			s.pos=[newpos[0], newpos[1]]
		})
	}

}


jQuery.fn.imgmover=function(options){
	var $=jQuery
	return this.each(function(){ //return jQuery obj
		if (this.tagName!="IMG")
			return true //skip to next matched element 
		var $imgref=$(this)
		if (parseInt(this.style.width)>0 && parseInt(this.style.height)>0) //if image has explicit CSS width/height defined
			ddimagepanner.init($, $imgref, options)
		else if (this.complete){ //account for IE not firing image.onload
			ddimagepanner.init($, $imgref, options)
		}
		else{
			$imgref.bind('load', function(){
				ddimagepanner.init($, $imgref, options)
			})
		}
	})
}


