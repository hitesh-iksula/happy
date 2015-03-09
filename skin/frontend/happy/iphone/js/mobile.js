var activity_message_timer;

jQuery(document).ready(function($) {

	var categories_loaded = false;

	$('#category_toggle_btn').click(function() {
		if(!$(this).hasClass('open') && !categories_loaded) {
			retrieve_header_categories();
			categories_loaded = true;
		}
	});

	function retrieve_header_categories() {
		$.ajax({
			method: 'POST',
			url: category_block_url,
			data: {
				'color' : 'white'
			},
			success: function(data) {
				$('#main_category_links').html(data);
				var toggle_arrow_html = "<div class='toggle_wrap'><div class='toggleArrow'>&nbsp;</div></div> <!-- Dynamically append from mobile.js -->";
				$("#nav ul li.parent").prepend(toggle_arrow_html);
			}
		});
	}

	if($('.item-image').length > 0) {
		$(".item-image").lazyload({
			effect : "fadeIn",
			threshold : 600,
			skip_invisible : false
		});
	}

	$('#back_to_top').click(function() {
		$('html, body').animate({
			scrollTop: 0
		}, 800);
	});

	rest_footer();

	function rest_footer() {
		var header_height = $('.header').height();
		var footer_height = $('.footer-container').height();
		var screen_height = $(window).height();
		var minimum_middle_container_height = screen_height - (header_height + footer_height);
		$('.middle-container').css('min-height', minimum_middle_container_height);
	}

	$('body').append('<div id="popup_jacket"></div>');
	$('body').append('<div id="loading_gif"></div>');

	if($('.how_shipping').length > 0) {
		$('.how_shipping').popup($('#shipping-tooltip-content').html());
	}

	if($('.messages').length > 0) {
		hide_activity_messages();
	}

	function hide_activity_messages() {
		activity_message_timer = setTimeout(function() {
			$('.messages').slideUp();
		}, 5000);
	}

	$(window).on('beforeunload', function() {
		$("#popup_jacket").show();
		$('#loading_gif').show();
	});
	$("#popup_jacket").hide();
	$('#loading_gif').hide();

});

(function ($) {

	$.fn.popup = function(text) {

		$('body').append('<div id="popup_envelope"><div id="popup_content"></div></div>');
		$('#popup_envelope').hide();

		$(this).on('click', function() {
			show_popup();
		});

		/*$('#question_tablerate_bestway').live('click', function(){
			show_popup();
		});

		$('#question_flatrate_flatrate').live('click', function(){
			show_popup();
		});*/

		$(this).live('click', function() {
			show_popup();
		});

		function show_popup() {
			$('#popup_jacket').show();
			$('#popup_envelope').show();
			$('#popup_content').html(text);
		}

		$('.close_popup').live('click', function() {
			hide_popup();
		});
		$('.close_popup').live('click', function(){
			hide_popup();
		});

		
		$('#popup_jacket').on('click', function() {
			hide_popup();
		});

		function hide_popup() {
			$('#popup_jacket').hide();
			$('#popup_envelope').hide();
		}

	}

})(jQuery);

(function($) {
	$.fn.placeholder = function() {
		if(typeof document.createElement("input").placeholder == 'undefined') {
			$('[placeholder]').focus(function() {
				var input = $(this);
				if (input.val() == input.attr('placeholder')) {
					input.val('');
					input.removeClass('placeholder');
				}
			}).blur(function() {
				var input = $(this);
				if (input.val() == '' || input.val() == input.attr('placeholder')) {
					input.addClass('placeholder');
					input.val(input.attr('placeholder'));
				}
			}).blur().parents('form').submit(function() {
				$(this).find('[placeholder]').each(function() {
					var input = $(this);
					if (input.val() == input.attr('placeholder')) {
						input.val('');
					}
				})
			});
		}
	}
})(jQuery);

jQuery(document).ready(function($) {
	$.fn.placeholder();
});

jQuery(document).ready(function($){
	/* --- Left Category Toggle --- */

	function left_category_toggle(){
		if($("#category_toggle_btn").hasClass('open')) {
			$("#category_toggle_btn").removeClass('open');
		} else {
			$("#category_toggle_btn").addClass('open');
		}
		$(".main_category_area").stop(true,true).slideToggle("normal");
		$(".toggle_overlay").css("display","block");
	}

	function reset_properties_left_nav(){
		$(".category_toggle_area #nav ul li.parent .toggleArrow").stop(true,true).removeClass("toggleupArrow");
		$(".category_toggle_area #nav ul li ul").stop(true,true).slideUp("normal");
	}

	$("#category_toggle_btn").click(function(){
		left_category_toggle();
		reset_properties_left_nav();
	});

	$(".close_left_nav_btn").click(function(){ /* --- Left nav Category Toggle on clicking close btn --- */
		reset_properties_left_nav();
		left_category_toggle();
		$(".toggle_overlay").css("display","none");
		$('html,body').animate({ scrollTop: 0 }, 1000);

	});

	/* --- Overlay toggle --- */
	$(".toggle_overlay").click(function(){
		var state_of_cms_toggle_area = $(".main_cms_area").css("display");/* -- Checking display state of right cms links area -- */
		var state_of_category_toggle_area = $(".main_category_area").css("display");/* -- Checking display state of left category links area -- */
		var state_of_category_toggle_area_page = $(".category-option-area").css("display");
		if( state_of_cms_toggle_area=="block" )
		{
			right_cms_toggle();
			$(".toggle_overlay").css("display","none");
			$('html,body').animate({ scrollTop: 0 }, 1000);
		}

		if( state_of_category_toggle_area=="block" ){
			reset_properties_left_nav();
			left_category_toggle();
			$(".toggle_overlay").css("display","none");
			$('html,body').animate({ scrollTop: 0 }, 1000);
		}

		if($('#account_dropdown_button').length > 0) {
			var state_of_account_dropdown = $('.account_dropdown').css("display");
			if(state_of_account_dropdown == 'block') {
				$(".account_dropdown").stop(true,true).slideToggle("normal");
				$(".toggle_overlay").css("display","none");
			}
		}
		if( state_of_category_toggle_area_page=="block" ){
			$(".category-option-area").slideUp("normal");
			$(".toggle_overlay").css("display","none");
			$('html,body').animate({ scrollTop: 0 }, 1000);
		}

	})


	/* --- Left Category Accordion --- */
	var toggle_arrow_html = "<div class='toggle_wrap'><div class='toggleArrow'>&nbsp;</div></div> <!-- Dynamically append from style_rk.js -->";
	$("#nav ul li.parent").prepend(toggle_arrow_html);

	$(document).on('click', "#nav ul li.parent .toggle_wrap", function(){
		var parent_node = $(this).closest(".parent");
		var grand_parent_node = $(this).parents(".nav-container").parent();
		var get_id_parent_node= grand_parent_node.attr("id");

		if($("ul",parent_node).css("display")=="none"){
			$("#" + get_id_parent_node + " #nav ul li.parent .toggleArrow").stop(true,true).removeClass("toggleupArrow");
			$("#" + get_id_parent_node + " #nav ul li ul").stop(true,true).slideUp("normal");
			$(".toggleArrow", this).stop(true,true).toggleClass("toggleupArrow");
			$("ul",parent_node).stop(true,true).slideToggle("normal");


		}else{
			$(".toggleArrow", this).stop(true,true).toggleClass("toggleupArrow");
			$("ul",parent_node).stop(true,true).slideToggle("normal");


		}

	})


	/* --- right Category Toggle --- */

	function right_cms_toggle(){
		$(".main_cms_area").stop(true,true).slideToggle("normal");
		$(".main_cms_area .cms_links_list li").removeClass("last");
		$(".main_cms_area .cms_links_list li:last").addClass("last");
		$(".toggle_overlay").css("display","block");
	}

	$(".cms_page_toggle_btn").on('click',function(){
		right_cms_toggle();
	});

	$('#account_dropdown_button').live('click', function() {
		$(".account_dropdown").stop(true,true).slideToggle("normal");
		$(".toggle_overlay").css("display","block");
	});


	$('.label-reveal .door, .label-reveal .checkbox_status.unchecked').live('click', function() {
		var parent = $(this).parent();
		if(!parent.hasClass('read-only')) {
			$('.door', parent).hide();
			$('.reveal', parent).show();
			$('.checkbox_status').toggleClass('unchecked').toggleClass('checked');
		}
	});

	$('.label-reveal .checkbox_status.checked').live('click', function() {
		var parent = $(this).parent();
		if(!parent.hasClass('read-only')) {
			$('.door', parent).show();
			$('.reveal', parent).hide();
			$('.checkbox_status').toggleClass('unchecked').toggleClass('checked');
		}
	});

	$.fn.ajaxSubmit = function(userSettings) {
		var defaultSettings = {
			validate: true,
			focusOnFirstElement: true
		}
		var form = this;
		var formFields = {};

		var settings = $.extend(defaultSettings, userSettings);

		var inputFields = 'input, textarea, select, button, submit';
		var defaultSuccessMessage = 'Your request was submitted successfully.';

		var varienFormValidation;
		if(settings.validate) {
			varienFormValidation = new VarienForm(form.attr('id'), settings.focusOnFirstElement);
		}

		var createFormFields = function() {
			$(inputFields, form).each(function() {
				if($(this).attr('name')) {
					formFields[$(this).attr('name')] = $(this).val();
				}
			});
		};

		var ajaxCall = function() {
			$.ajax({
				url: form.attr('action'),
				type: form.attr('method'),
				data: formFields,
				success: function(response) {
					if(settings.success) {
						settings.success(response);
					} else {
						ajaxResponse(response);
					}
				}
			});
		};

		var ajaxResponse = function(response) {
			var responseText = (response) ? response : defaultSuccessMessage;
			var responseMessageInner = '<ul><li><span>' + responseText + '</span></li></ul>';
			var responseMessage = '<ul class="messages" id="ajax-response-message"><li class="success-msg">' + responseMessageInner + '</li></ul>';
			if($('#ajax-response-message', form).length > 0) {
				$('#ajax-response-message', form).empty().replaceWith(responseMessage);
			} else {
				form.prepend(responseMessage);
			}
		};

		var submitForm = function() {
			if(settings.beforeAjax) {
				settings.beforeAjax();
			}
			createFormFields();
			ajaxCall();
		};

		form.submit(function(e) {
			e.preventDefault();
			if(!varienFormValidation) {
				submitForm();
			}
			else if(varienFormValidation.validator.validate()) {
				submitForm();
			}
		});
		return false;
	};


	var initiateDiscountAjax = function() {

		$('#discount-coupon-form').ajaxSubmit({
			beforeAjax: function() {
				$('#loading_gif').show();
				$('#popup_jacket').show();
			},
			success: function(response) {
				response = JSON.parse(response);
				// $('.cart-coupon').html(response.discountblock);
				//console.log(response);
				if(response.actionCode == 1) {

					$('#shopping-cart-totals-table-container').empty().replaceWith(response.carttotals);
					$('.label-reveal .door').hide();
					$('.label-reveal .reveal').hide();
					$("#not-valid").hide();
					$('.label-reveal .result#valid-coupon').show();
					$('#coupon_remove').val(1);
					$('.label-reveal').addClass('read-only');

				} else if(response.actionCode == 2) {
					$('#shopping-cart-totals-table-container').empty().replaceWith(response.carttotals);
					$('#custom-coupon-form').html(response.discountblock);
					/*$(".reveal").show();
					$("#valid-coupon").hide();*/
					/*$('.label-reveal .result').show();*/
				}
				else if(response.actionCode == 3){
					/*$("#not-valid").show();*/
					alert('Coupon code is not valid');
				}
					// $('.label-reveal .result').show();
				initiateDiscountAjax();

				$('#loading_gif').hide();
				$('#popup_jacket').hide();
			}
		});

	}

	initiateDiscountAjax();

	$('.upsell_add_to_cart').live('click', function(event) {

		if(event.handled !== true) {

			if(typeof this_is_checkout_page == 'undefined') {
				event.preventDefault();
				add_to_cart($(this).attr('href'));

				event.handled = true;
			}
		}
		return false;
	});

	$('.remove_from_cart_icon').live('click', function(event) {

		if(event.handled !== true) {

			if(typeof this_is_checkout_page == 'undefined') {

				event.preventDefault();

				var product_object = $(this).parents('.cart-product-entry');
				var product_name = $.trim($('.product-name', product_object).text());
				var product_id = product_object.attr('id');
				product_id = parseInt(product_id.replace('product-entry-', ''));

				var confirmation = confirm('Are you sure you want to remove ' + product_name + ' from your carton?');

				if(confirmation == true) {

					var url = $(this).attr('href');
					var data = {};
					data.isAjax = 1;
					data.refererLocation = 'cart_modal';
					data.productName = product_name;
					$('#loading_gif').show();
					$('#popup_jacket').show();

					try {
						$.ajax({
							url: url,
							dataType: 'json',
							type : 'post',
							data: data,
							success: function(data){
								$('#loading_gif').hide();
								$('#popup_jacket').hide();
								$('.modal_tailbar .carton_logo_and_amount span').html(data.cartitemqty);
								if(data.cartstatus == 'empty') {
									$('.cart-modal-main').html(data.emptycart);
									$('.cart-flag').html(data.activitymessage);
									$('#fancybox-title .modal_tailbar .proceed_to_payment_box').html('');
								} else {
									$('.cart-flag').html(data.activitymessage);
									$('#shopping-cart-totals-table-container').empty().replaceWith(data.carttotals);
									$(product_object).fadeOut();
									setTimeout(function() {
										$(product_object).remove();
									}, 700);
								}
								fadeout_activity_message();

								if(data.header_cart) {
									$('#cart-action').html(data.header_cart);
								}

								show_free_shipping_text();
							}
						});
					} catch (e) {
					}

				}

			}

			event.handled = true;

		}
		return false;

	});


	$('#question_tablerate_bestway').popup($('.popup-dialog').html());	
	$('#question_flatrate_flatrate').popup($('.popup-dialog').html());

	$('.close_popup').live('click', function(){
		$('.ui-dialog').hide();
	});

	$('#popup_jacket').live('click', function(){
		$('.ui-dialog').hide();
	});

	$('.ui-button-icon-primary.ui-icon.ui-icon-closethick').live('click', function() {
		$('#fancybox-loading-overlay').hide();
		return false;
	});

	$(".toggle_arrow_wrap").click(function(){
		$(this).prev(".refund-case-bottom").stop(true,true).slideToggle();
		$(this).find(".toggle_arrow").stop(true,true).toggleClass("toggle_arrow_up");
	});

	function category_toggle(){
		$(".category-option-area").stop(true,true).slideToggle("normal");
		$("#category_toggle").css("display","block");
	}

	$('.category-option').click(function(){
		category_toggle();
	});
});

function product_click(product_sku,product_name,category_name,brand,variant,position,list){
	ga('require', 'ec');

	ga('ec:addProduct', {
	    'id': product_sku,	 // Add product SKU.(Required)
	    'name': product_name,// Required
	    'category': category_name,
	    'brand': brand,
	    'variant': variant,
	    'position': position
	 });
	
	 ga('ec:setAction', 'click', {
	 	list: list
	 });
}
