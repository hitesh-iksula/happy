// Global Variables
var steps = ["login-step", "address-step", "review-step", "payment-step"];
var current_step;
var next_step;
var hide_flag = 0;
var index = 0;
var show_shipping_address_info_step = "review-step";
var shipping_address = '';
var chosen_address_option = 0;
var validation_status = [0, 0, 0, 0];
var validate_steps = true;
var bypass_current_validation = false;
var target_payment_element = '';
var other_payment_elements = '';
var selected_payment_label = '';
var fresh_load = 1;
var product_modal_width = 0;
var this_is_checkout_page = 1;
var timer;
var referer;
var default_selected_payment_label;
var default_target_payment_element;
var payu_valid = true;
var couponHtml = '';
var mobile_theme = false;
var step_button_id;
var persistent_loader = false;
var pincodes_fetched = false;
var pincodes = {};
var customer_postcode;
var find_my_shipping_cost = false;
var first_land_on_address_step = true;

jQuery(document).ready(function($) {

	selected_payment_label = $('.payment-method-label.selected-method').attr('id');
	target_payment_element = $('label', $('.payment-method-label.selected-method')).prop('for') + '_data';

	default_selected_payment_label = $('.payment-method-label.selected-method').attr('id');
	default_target_payment_element = $('label', $('.payment-method-label.selected-method')).prop('for') + '_data';

	// For Mobile
	if($('body').hasClass('mage-iphone')) {
		mobile_theme = true;
	}

	function validate_this_step() {
		if(validate_steps == false) {
			return true;
		}
		var this_step = $('#'+current_step);
		if(current_step == 'review-step') {
			bypass_current_validation = true;
		}
		if(current_step == 'address-step') {
			if(!$('#billing\\:region_id').val()) {
				var validation_advice = '<div class="validation-advice" id="validation-advice-state-dropdown">Please select a state.</div>';
				$('.selectboxit.state-select-button').addClass('validation-failed');
				if($('#validation-advice-state-dropdown').length == 0) {
					$('.selectboxit.state-select-button').parent().after(validation_advice);
				}
				return false;
			} else {
				if($('#validation-advice-state-dropdown').length > 0) {
					$('#validation-advice-state-dropdown').remove();
				}
				$('.selectboxit.state-select-button').removeClass('validation-failed');
			}
		}
		if(validation_status[index] == 1) {
			return true;
		}
		return false;
	}

	function show_next_step(jump_to_last) {
		$('.step#'+current_step).hide();
		index = $.inArray(current_step, steps);
		index++;
		$('.step#'+steps[index]).show();
		$('.step_bubble#'+steps[index]).removeClass('not_clickable');
		$('body').removeClass(current_step);
		current_step = steps[index];
		$('body').addClass(current_step);
		step_specific_operations();
		if(jump_to_last) {
			next_step();
		}
	}

	function next_step(event) {
		if(event) {
			step_button_id = event.target.id;
		}

		var validate = validate_this_step();
		if(validate == true || bypass_current_validation == true) {
			if($(this) && $(this).hasClass('jump_to_last')) {
				show_next_step(true);
			} else {
				show_next_step(false);
			}
			$('.step, .step_bubble').removeClass('active_step');
			$('.step_bubble#'+current_step).addClass('active_step');
			bypass_current_validation = false;
			if(mobile_theme) {
				$('html, body').scrollTop(0);
			}
		}
	}

	function navigate_to_step() {
		var step_id = $(this).attr('id');
		this_index = $.inArray(step_id, steps);
		if(this_index < index && (!$(this).hasClass('not_clickable'))) {
			$('body').removeClass(current_step);
			$('.step#'+current_step).hide();
			current_step = step_id;
			index = this_index;
			$('body').addClass(current_step);
			step_specific_operations();
			var i = 0;
			for(i = index + 1; i < steps.length; i++) {
				$('.step_bubble#'+steps[i]).addClass('not_clickable');
			}
			$('.step, .step_bubble').removeClass('active_step');
			$('.step_bubble#'+current_step).addClass('active_step');
			$('.step#'+steps[index]).show();
			validation_status[index] = 0;
		}
	}

	function put_shipping_address_html(target_element, source) {
		if(source == 'from-form') {
			var form = $('#onepagecheckout_orderform');
			var name = $(document.getElementById('billing:firstname')).val() + ' ' + $(document.getElementById('billing:lastname')).val();
			var company = '';
			if($(document.getElementById('billing:company')).val()) {
				company = '<p>' + $(document.getElementById('billing:company')).val() + '</p>';
			}
			var address_1 = $(document.getElementById('billing:street1')).val();
			var city = $(document.getElementById('billing:city')).val();
			var state = $(document.getElementById('billing:region_id')).find("option:selected").text();
			var country = $(document.getElementById('billing:country_id')).val();
			var postcode = $(document.getElementById('billing:postcode')).val();
			var telephone = $(document.getElementById('billing:telephone')).val();
			shipping_address = '<p>' + name + '</p>' + company + '<p>' + address_1 + '</p>' + '<p>' + city + '</p>' + '<p>' + state + ', ' + country + ' ' + postcode + '</p>' + '<p>T: ' + telephone + '</p>';

		} else {
			shipping_address = source.html();
		}
		$(target_element).html(shipping_address);
	}

	function step_specific_operations() {
		if(current_step == 'review-step') {
			$('.checkout_cart_sidebar').hide();
		} else {
			$('.checkout_cart_sidebar').show();
		}
		if(current_step == 'payment-step') {
			if(is_production && ga) {
				var event_label;
				if(step_button_id == 'address-step-jump') {
					event_label = 'Jump to payment';
				} else if(step_button_id == 'review-step-continue') {
					event_label = 'Proceed to payment';
				}
				// _gaq.push(['_trackEvent', 'Payment Step', 'click', event_label]);
			}
			var target_element = '#' + $('label', $('.payment-method-label.selected-method')).prop('for') + '_data';
			$(target_element).show();
			selected_payment_label = $('.payment-method-label.selected-method').attr('id');
			target_payment_element = $('label', $('.payment-method-label.selected-method')).prop('for') + '_data';

			if(!payment.currentMethod) {
				$('#p_method_cashondelivery').prop('checked', false);
				$('#p_method_wallet').prop('checked', false);
				$('#p_method_payucheckout_shared').prop('checked', 'checked');

				payment.switchMethod('payucheckout_shared');

				checkout.update({
					'shipping-method': 1,
					'payment-method': 1,
					'review': 1
				});
			}

			$('.col-main').addClass('on-payment-step');
			$('#title-my-carton').text('Final Order');
		} else {
			$('.col-main').removeClass('on-payment-step');
			$('#title-my-carton').text('My Carton');
		}

		if(current_step == 'address-step') {
			if(first_land_on_address_step) {
				first_land_on_address_step = false;
				$('#s_method_tablerate_bestway').click();
			}
			$('#bill_form').show();
			if($('#has_account_no').prop('checked') == true) {
				var new_user_form = $('#new-user-first-step');
				var temp = '';
				temp = $('#emailid', new_user_form).val();
				$(document.getElementById('billing:email')).val(temp);
			}
		}

		/*if(!mobile_theme){
			if(current_step != 'review-step') {
				var list;
				if(typeof productsInCart != 'undefined') {
					$.each(productsInCart, function(id, product) {
						ga('ec:addProduct', product);
						list = product.category;
					});
				}
				var gaStep;
				if(current_step == 'login-step') {
					gaStep = 1;
				}
				else if(current_step == 'address-step') {
					gaStep = 2;
				}
				else {
					gaStep = 3;
				}

				ga('ec:setAction','checkout', {
					'step': gaStep,
					'list': list
				});

				ga('send', 'pageview');
			}
		}

		if(mobile_theme){
			var list;
			if(typeof productsInCart != 'undefined') {
				$.each(productsInCart, function(id, product) {
					ga('ec:addProduct', product);
					list = product.category;
				});
			}

			var gaStep;
			if(current_step == 'login-step') {
				gaStep = 1;
			}
			else if(current_step == 'address-step') {
				gaStep = 2;
			}
			else if(current_step == 'review-step'){
				gaStep = 3;
			}
			else {
				gaStep = 4;
			}

			ga('ec:setAction','checkout', {
				'step': gaStep,
				'list': list
			});
			ga('send', 'pageview');
		}*/
	}

	function select_existing_address() {
		var address_box = $(this).parents('.address-box');
		var address_id = address_box.attr('id').replace('box-', '');
		$('#billing_customer_address').val(address_id);
		$('#shipping_customer_address').val(address_id);
		$('#billing\\:address_id').val(address_id);
		$('#shipping\\:address_id').val(address_id);
		$('#billing\\:save_in_address_book').attr('checked', false);
		$('#shipping\\:save_in_address_book').attr('checked', false);

		$.ajax({
			url: address_fetch_url,
			dataType: 'json',
			type : 'get',
			data: { 'address_id': address_id },
			success: function(address_array) {
				$.each(address_array, function(key, value) {
					if($("#billing\\:" + key)) {
						$("#billing\\:" + key).val(value);
					}
					if(key == 'street') {
						street_value = value.split(/\r\n|\r|\n/g);
						$("#billing\\:street1").val(street_value[0]);
						$("#billing\\:street2").val(street_value[1]);
					}
					if(key == 'region_id') {
						$("#billing\\:region_id").trigger('change');
					}
				});
				persistent_loader = true;
				checkout.update({
					'shipping-method': 1,
					'review': 1
				});
				// For Mobile
				if(mobile_theme) {
					$('.form_logged_in .opc_title').text('Confirm / Modify Address');
					$("html, body").animate({
						scrollTop: $('#bill_form').offset().top
					}, 600);
				}
			}
		});
	}

	function select_address_from_form() {
		put_shipping_address_html('.shipping_info', 'from-form');
		// $('#billing_customer_address').val('');
		chosen_address_option = 0;
	}

	function show_payment_data() {
		fresh_load = 0;
		selected_payment_label = $(this).attr('id');
		target_payment_element = $('label', $(this)).prop('for') + '_data';
		other_payment_elements = $('.payment-data-box');
		/*alert('asdasd');*/

		if($('label', $(this)).prop('for') == 'p_method_cashondelivery') {

			$('#p_method_wallet').prop('checked', false);
			$('#p_method_payucheckout_shared').prop('checked', false);
			$('#p_method_cashondelivery').prop('checked', 'checked');
			$('#p_method_payzippy').prop('checked', false);
			$('#p_method_paytm_cc').prop('checked', false);

			payment.switchMethod('cashondelivery');

		} else if($('label', $(this)).prop('for') == 'p_method_wallet') {

			$('#p_method_cashondelivery').prop('checked', false);
			$('#p_method_payucheckout_shared').prop('checked', false);
			$('#p_method_wallet').prop('checked', 'checked');
			$('#p_method_payzippy').prop('checked', false);
			$('#p_method_paytm_cc').prop('checked', false);

			payment.switchMethod('wallet');

		} else if($('label', $(this)).prop('for').indexOf('payucheckout') > 0) {

			$('#p_method_cashondelivery').prop('checked', false);
			$('#p_method_wallet').prop('checked', false);
			$('#p_method_payucheckout_shared').prop('checked', 'checked');
			$('#p_method_payzippy').prop('checked', false);
			$('#p_method_paytm_cc').prop('checked', false);
			/*alert('payu');*/

			payment.switchMethod('payucheckout_shared');

		} else if($('label', $(this)).prop('for') == 'p_method_payzippy') {

			$('#p_method_cashondelivery').prop('checked', false);
			$('#p_method_wallet').prop('checked', false);
			$('#p_method_payucheckout_shared').prop('checked', false);
			$('#p_method_payzippy').prop('checked', 'checked');
			$('#p_method_paytm_cc').prop('checked', false);

			payment.switchMethod('payzippy');

		} else if($('label', $(this)).prop('for') == 'p_method_paytm_cc') {

			$('#p_method_cashondelivery').prop('checked', false);
			$('#p_method_wallet').prop('checked', false);
			$('#p_method_payucheckout_shared').prop('checked', false);
			$('#p_method_payzippy').prop('checked', false);
			$('#p_method_paytm_cc').prop('checked', 'checked');
			/*alert('paytm');*/

			payment.switchMethod('paytm_cc');

		} else {

			$('#p_method_cashondelivery').prop('checked', false);
			$('#p_method_wallet').prop('checked', false);
			$('#p_method_payucheckout_shared').prop('checked', 'checked');
			$('#p_method_payzippy').prop('checked', false);
			$('#p_method_paytm_cc').prop('checked', false);

			payment.switchMethod('payucheckout_shared');

		}

		$('.address-final', '#' + target_payment_element).html(shipping_address);

		/* Migrated post update code to here since a reload is not required anymore */
		/* We used to reload payment methods because we had different shipping logic for COD */
		$('.payment-method-label').each(function() {
			$(this).removeClass('selected-method');
		});

		if(selected_payment_label != '' && $('#' + selected_payment_label).length > 0) {
			$('#' + selected_payment_label).addClass('selected-method');
		}

		if(selected_payment_label != '') {
			var payu_card_option = $('#'+selected_payment_label+' label').first().attr('for');
			payu_card_option_value = $('#' + payu_card_option).val();
			$('#payu_payment_method').val(payu_card_option_value);
		}

		$('.payment-data-box').each(function() {
			$(this).hide();
		});

		if(target_payment_element != '') {
			$('#' + target_payment_element).show();
		}
	}

	function show_payment_loading() {
		if(payu_valid == true) {
			$('#review-please-wait').show();
		}
	}

	function initialize() {
		if($('.step_bubble.active_step').length > 0) {
			current_step = $('.step_bubble.active_step').attr('id');

			index = steps.indexOf(current_step);
		} else {
			current_step = steps[0];
			$('.step_bubble#'+current_step).addClass('active_step');
		}
		$('.step#'+current_step).show();
		$('#title-my-carton').text('My Carton');
		$('body').addClass(current_step);
		step_specific_operations();
	}

	initialize();

	$('.continue').live('click', next_step);


	$('.step_bubble').click(navigate_to_step);

	$('.payment-method-label').live('click', show_payment_data);

	$('#place-order').live('click', show_payment_loading);

	$('.payu-payment-option').live('change', function(event) {
		if(event.handled !== true) {
			event.preventDefault();
			var li = $(this).parents('.payu-payment-option-box');
			var payu_payment_method = $(this).val();
			var payu_method_option = $('.payu-payment-dropdowns', li).val();
			$('#payu_payment_method').val(payu_payment_method);
			$('#payu_method_option').val(payu_method_option);
		}
		return false;
	});

	$('.payu-payment-dropdowns').live('change', function(event) {
		if(event.handled !== true) {
			event.preventDefault();
			var id = $(this).attr('id');
			var payu_method_option = $('#' + id).val();
			$('#payu_method_option').val(payu_method_option);
			event.handled = true;
		}
		return false;
	});

	$('.net_banking_featured').live('click', function() {
		$('.net_banking_featured').removeClass('selected');
		$(this).addClass('selected');
		var target_nb_element = $(this).prop('for');
		var nb_value = $('#'+target_nb_element).val();
		$('#payu_method_option').val(nb_value);
		$('#payu_net_banking_cards').val('');
		$('#payu_net_banking_cards').removeClass('selected');
	});

	$('#payu_net_banking_cards').live('click', function() {
		$('.net_banking_featured').removeClass('selected');
	});

	$('#payu_net_banking_cards').live('change', function() {
		$('.net_banking_featured').removeClass('selected');
		$('.payu_featured_banks').prop('checked', false);
		$(this).addClass('selected');
		if(!$(this).val()) {
			$(this).removeClass('selected');
		}
	});

	$('.ship_to_this_address').click(select_existing_address);
	$('#address-step-continue').click(select_address_from_form);
	$('#bill_form.form_logged_in input, #bill_form.form_logged_in textarea').keyup(function() {
		$('#billing_customer_address').val('');
		$('#billing\\:save_in_address_book').attr('checked', true);
		$('#shipping\\:save_in_address_book').attr('checked', true);
	});

	$('.remove_from_cart_button').live('click', function (event) {
		if(event.handled !== true) {
			event.preventDefault();

			if($(this).parents('#checkout-review-table').length > 0) {

				var parent = $(this).parents('.cart-product-entry');
				var product_name = $('.product-name div', parent).text();
				var confirmation = confirm('Are you sure you want to remove ' + product_name + ' from your carton?');
				if(confirmation == true) {
					var this_href = $(this).attr('href');
					var data = {};
					data.isAjax = 1;
					$.ajax({
						url: this_href,
						dataType: 'json',
						type : 'get',
						data: data,
						success: function(data){
							checkout.update({
								'review': 1
							});
						}
					});
				}

			} else {

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
								$('.modal_tailbar .carton_logo_and_amount span').html(data.cartitemqty);
								if(data.cartstatus == 'empty') {
									$('.cart-modal-main').html(data.emptycart);
									$('.cart-flag').html(data.activitymessage);
									$('#fancybox-title .modal_tailbar .proceed_to_payment_box').html('');
								} else {
									$('.cart-flag').html(data.activitymessage);
									$('.shopping-cart-totals-table').empty().replaceWith(data.carttotals);
									$(product_object).fadeOut();
									setTimeout(function() {
										$(product_object).remove();
									}, 700);
									checkout.update({
										'review': 1
									});
								}
								fadeout_activity_message();
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

	function fadeout_activity_message() {
		clearTimeout(timer);
		timer = setTimeout(function() {
			$('.cart-activity-bar').fadeOut();
		}, 5000);
	}

	$('.change-quantity-of-product').live('click', function(event) {

		if(event.handled !== true) {

			var product_object = $(this).parents('.cart-product-entry');
			var product_name = $.trim($('.product-name', product_object).text());
			var product_id = product_object.attr('rel');
			product_id = parseInt(product_id.replace('cart-item-', ''));

			var data = {};

			data.isAjax = 1;
			data.productid = product_id;
			data.productName = product_name;

			if($(this).parents('#checkout-review-table').length > 0) {
				referer = 'checkout';
			} else {
				referer = 'cart-inside-checkout';
			}
			data.referer = referer;

			if($(this).attr('id') == 'increment-quantity') {
				data.activity = 'increment';
			} else if($(this).attr('id') == 'decrement-quantity') {
				data.activity = 'decrement';
				var current_quantity = parseInt($.trim($('.product-quantity-amount', product_object).text()));
				if(current_quantity == 1) {
					return;
				}
			}

			try {
				$.ajax({
					url: update_qty_url,
					dataType: 'json',
					type : 'post',
					data: data,
					success: function(data) {
						if(data.errorsign == 0) {
							checkout.update({
								'review': 1
							});
							if(referer == 'cart-inside-checkout') {
								$('.product-quantity-amount', product_object).html(data.updatedqty);
								$('.cart-flag').html(data.activitymessage);
								$('.shopping-cart-totals-table').empty().replaceWith(data.carttotals);
								$('.cart-items').html(data.cartitems);
								fadeout_activity_message();
							}
						}
					}
				});
			} catch (e) {
			}

			event.handled = true;
		}
		return false;

	});

	// For Mobile
	$('.cart_qty_update').live('change', function(event) {
		$("#popup_jacket").show();
		$('#loading_gif').show();
		if(event.handled !== true) {

			var product_object = $(this).parents('.cart-product-entry');
			var product_name = $.trim($('.product-name', product_object).text());
			var product_id = product_object.attr('rel');
			product_id = parseInt(product_id.replace('cart-item-', ''));

			var data = {};

			data.isAjax = 1;
			data.productid = product_id;
			data.productName = product_name;

			if($(this).parents('#checkout-review-table').length > 0) {
				referer = 'checkout';
			} else {
				referer = 'cart-inside-checkout';
			}
			data.referer = referer;

			data.quantity = $(this).val();

			try {
				$.ajax({
					url: update_qty_url,
					dataType: 'json',
					type : 'post',
					data: data,
					success: function(data) {
						$("#popup_jacket").hide();
						$('#loading_gif').hide();
						if(data.errorsign == 0) {
							checkout.update({
								'review': 1
							});
							if(referer == 'cart-inside-checkout') {
								$('.product-quantity-amount', product_object).html(data.updatedqty);
								$('.cart-flag').html(data.activitymessage);
								$('.shopping-cart-totals-table').empty().replaceWith(data.carttotals);
								$('.cart-items').html(data.cartitems);
								fadeout_activity_message();
							}
						}
					}
				});
			} catch (e) {
			}

			event.handled = true;
		}
		return false;

	});

	$('input[name="has_account"]').click(function() {
		var envelope = $(this).parents('.envelope');
		$('.input-container').hide();
		$('.input-container', envelope).show();
		$('input[name="has_account"]').prop('checked', false);
		$(this).prop('checked', 'checked');
		if(envelope.attr('id') == 'returning_user') {
			$('#login-step-continue').parent().hide();
		} else {
			$('#login-step-continue').parent().show();
		}
	});

	if(!mobile_theme) {
		/*var select_box = $('select').selectBoxIt({ theme: 'default' });*/
	}

	/* Enter Key Events */
	$(window).keypress(function(event){
		if (event.which == 10 || event.which == 13) {
			if(event.target.id != "billing:street1" && event.target.id != "shipping:street1") {
				event.preventDefault();
			}
			if(event.target.id == "emailid") {
				$('#login-step-continue').click();
			}
			if(event.target.id == "email" || event.target.id == "pass") {
				$('#send2').click();
			}
		}
	});

	$('input[name="billing[day]"], input[name="billing[month]"], input[name="billing[year]"]').focusin(function() {
		var dobValue = $(this).val();
		if(dobValue == 'DD' || dobValue == 'MM' || dobValue == 'YYYY') {
			$(this).val('');
		}
	});

	$('input[name="billing[day]"]').focusout(function() {
		if(!mobile_theme && !$(this).val()) {
			$(this).val('DD');
		}
	});

	$('input[name="billing[month]"]').focusout(function() {
		if(!mobile_theme && !$(this).val()) {
			$(this).val('MM');
		}
	});

	$('input[name="billing[year]"]').focusout(function() {
		if(!mobile_theme && !$(this).val()) {
			$(this).val('YYYY');
		}
	});

	/**
	 *
	 *
	 * Fancybox for the Cart Modal
	 *
	 *
	 */
	winWidh =  Math.round($('.main').width());

	if(winWidh > 900)
	{
		product_modal_width = 930;
	} else if(winWidh < 900 && winWidh > 780) {
		product_modal_width = 780;
	} else {
		product_modal_width = 680;
	}

	$('.cart_page').live('click', function() {

		var original_href = $(this).attr('href');
		var append_referer_string = "?referer=onepagecheckout";
		var referer_href = original_href + append_referer_string;
		$(this).attr('href', referer_href);
		$.fancybox(this, {
			"width" : product_modal_width,
			"height" : 602,
			"padding" : 0,
			"margin" : 0,
			"autoDimensions" : false,
			"autoScale" : false,
			"transitionIn" : "fade",
			"transitionOut" : "none",
			"speedIn" : 300,
			"speedOut" : 300,
			"titleShow" : false,
			"titleFormat" : fancybox_footer,
			"overlayOpacity" : 0.6,
			"overlayColor" : "#000",
			onComplete: function() {
				$('.product-name-and-options').each(function() {
					if($('.item-options', $(this)).length > 0) {
						var this_height = $(this).height();
						var options_height = $('.item-options', $(this)).height();
						var offset = (75 - parseInt(this_height)) / 2;
						$('.product-name', $(this)).css('margin-top', offset);
					}
				});
			}
		});
		$(this).attr('href', original_href);

		return false;
	});

	function fancybox_footer() {
		var tailbar_content = $('.modal_tailbar_container').html();
		return tailbar_content;
	}

	$('.proceed_to_payment_button').live('click', function(e){
		e.preventDefault();
		$.fancybox.close();
	});

	// Footer Slide Up and Slide Down
	$('.slideup').click(function(){
		$(".footer_payment_options").trigger('open');
		if($(this).hasClass('slidedown')) {
			setTimeout(function() {
				$('.slideup').removeClass('slidedown');
			}, 800);
		} else {
			$(this).addClass('slidedown');
		}
		$('.slideContent').slideToggle("1000");
		$('.up_arrow', $(this)).toggleClass('down_arrow');
	});

	// Hide border of last UL in Footer Links
	$('.facepileArea1 ul:last').css("border-right", "none");

	// LazyLoader Initiation
	$(".item-image, .ecategory_thumbnail").lazyload({
		effect : "fadeIn",
		threshold : 600,
		skip_invisible : false
	});
	$(".active_filter_image_image, .active_filter_active_image, .footer_payment_options").lazyload({
		effect : "fadeIn",
		threshold : 100,
		event : "open"
	});

	// If Messages are present on the page, move them below checkout_progress div
	if($('ul.messages').length > 0) {
		$('ul.messages').insertAfter('.checkout_progress');
	}

	/* Pincode - autofill State and City */
	$('#billing\\:postcode').on('change', function() {
		customer_postcode = parseInt($(this).val());
		if($(this).val().length > 4) {
			if(!pincodes_fetched) {
				fetchPincodes(true, autofillRegion);
			} else {
				autofillRegion();
			}
		}
	});

	$('.fedex-button').live('click', function() {
		find_my_shipping_cost = true;
		$('#loading2').show();
		$('#fancybox-loading-overlay').show();
	});

	window.fetchPincodes = function fetchPincodes(hasCallback, callback) {
		$.ajax({
			url: baseUrl + "js/pincode_muster.json",
			method: "GET",
			success: function(json_object) {
				if(typeof json_object == "string") {
					json_object = JSON.parse(json_object);
				}
				pincodes = json_object;
				pincodes_fetched = true;
				if(hasCallback) {
					callback();
				}
			}
		});
	};

	fetchPincodes();

	window.autofillRegion = function autofillRegion() {
		$.each(pincodes, function(id, area){
		//pincodes.forEach(function(area) {
			if(area.postcode == customer_postcode) {
				$('#billing\\:city').val(area.city);
				$('#billing\\:region_id').val(area.region_id);
				$("#billing\\:region_id").trigger('change');
			}
		});
	};

	// $('#question_tablerate_bestway').live('click', function() {
	// 	$('#fancybox-loading-overlay').show();
	// 	$("#stndrd-dialog").dialog({ position: { my: "left center", at: "right bottom", of: "#question_tablerate_bestway" } });
	// 	$("#nxt-day-dialog").dialog("close");
	// 	return false;
	// });

	 // $('#question_flatrate_flatrate').live('click', function() {
	 // 	$('#fancybox-loading-overlay').show();
	 // 	$("#nxt-day-dialog").dialog({ position: { my: "left center", at: "right bottom", of: "#question_flatrate_flatrate" } });
	 // 	$("#stndrd-dialog").dialog("close");
	 // 	return false;
	 // });

	$('#question_tablerate_bestway').live('click', function() {
	    $.fancybox({
	        type: 'inline',
	        content: '#stndrd-dialog'
	    });
	});

	$('#question_flatrate_flatrate').live('click', function() {
	    $.fancybox({
	        type: 'inline',
	        content: '#nxt-day-dialog'
	    });
	});





	$('.ui-button-icon-primary.ui-icon.ui-icon-closethick').live('click', function() {
		$('#fancybox-loading-overlay').hide();
		return false;
	});


});
