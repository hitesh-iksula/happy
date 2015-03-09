 /*
 *
 *
 *
 Default Js for all pages
 *
 *
 *
 */

var cartActivityMessage;

/* Document on ready function */
jQuery(document).ready(function($){

	/*
	 * Adjust main div's height on load and resize
	 */
	function main_height_adjustment() {
	    var window_height = ($(window).height() - 50);
	   /* $("div.main").css('min-height', window_height + "px");*/
	}

	//homepage slider starts
	$('#homepage-slider .image').first().show();
	$('.tab .menu').first().addClass('active');

	$('.tab .menu').mouseover(function() {
		var N = $(this).attr('id').replace('menu', '');
		$('#image' + N).show().siblings().hide();
		$(this).addClass('active').siblings().removeClass('active');
	});
	//homepage slider Ends

	//Dialog box starts
	/*$(function() {
		jQuery('#stndrd').live('click', function() {
			$('#fancybox-loading-overlay').show();
			$( "#stndrd-dialog" ).dialog({ position: { my: "left center", at: "right bottom", of: "#stndrd" } });
			$( "#nxt-day-dialog" ).dialog( "close" );
			return false;
		});

		jQuery('#nxt-day').live('click', function() {
			$('#fancybox-loading-overlay').show();
			$("#nxt-day-dialog").dialog({ position: { my: "left center", at: "right bottom", of: "#nxt-day" } });
			$("#stndrd-dialog").dialog("close");
			return false;
		});
	});*/

	/*$(function() {
		jQuery('.ui-button-icon-primary.ui-icon.ui-icon-closethick').live('click', function() {
			$('#fancybox-loading-overlay').hide();
			return false;
		});
	});*/
	//Dialog box Ends

	/*$('.continue').live('click', function(){
		$('#s_method_tablerate_bestway').click();
	});*/


	//Fedex Starts
    /*$('#billing\\:country_id').on('change', function() {
    	$('#fancybox-loading').show();
		$('#fancybox-loading-overlay').show();
		if($(this).val()!='IN'){
		// console.log($(this).val());
			$('.fedex-content').show();
			$('.opc_title').hide();
			$('#checkout-shipping-method-load').hide();
			$('.fedex-button').show();
			$('#address-step-continue').hide();
		}
		else{
			$('.fedex-content').hide();
			$('.opc_title').show();
			$('#checkout-shipping-method-load').show();
			$('.fedex-button').hide();
			$('#address-step-continue').show();
		}
	});*/

   	/*$('.fedex-button').click(function(){
    	if(validate_form()){
    		loading2.show();
    	}
    })*/
	//Fedex Ends

	/*$('.happy_button.green.validation-passed').live('click', function(event){
		var url = "<?php echo $this->getUrl('checkout/cart/ajaxUpdate') ?>";
		var data = {};

		data.isAjax = 1;
		$('#fancybox-loading').show();
		$('#fancybox-loading-overlay').show();
		try {
			$.ajax({
				url: url,
				dataType: 'json',
				type : 'post',
				data: data,
				success: function(data) {
					$('#fancybox-loading').hide();
					$('#fancybox-loading-overlay').hide();
				}
			});
		}catch (e) {
		}
	});*/


	/* payment click */
	/*$("#label-6").click(function(){
		alert("click call");
	});*/

	main_height_adjustment();
	$(window).resize(main_height_adjustment);

    $('.slides_container').show();
    // Load FAQ toolbar asynchronously
 //    if(!$('body').hasClass('mage-iphone')) {
	// 	$.ajax({
	// 		url: faqHtmlUrl,
	// 		method: "POST",
	// 		success: function(html) {
	// 			$('.col-main').append(html);
	// 		}
	// 	});
	// }

	// Hide border of last UL in Footer Links
	$('.facepileArea1 ul:last').css("border-right", "none");

	// LazyLoader Initiation
	$(".item-image, .ecategory_thumbnail").lazyload({
		effect : "fadeIn",
		threshold : 600,
		skip_invisible : false
	});
	$(".active_filter_image_image, .active_filter_active_image").lazyload({
		effect : "fadeIn",
		threshold : 100,
		event : "open"
	});

	//$('.form_fields').contents().find('.logged_in').addClass('log-in');

  	if($('.breadcrumbs').length > 0 && $('.breadcrumb_container').length > 0) {
  		$('.breadcrumb_container').append($('.breadcrumbs'));
  	}

	/*
	 * Eager load cart modal on page load
	 */
	window.loadCartModal = function loadCartModal(callback) {
		$.ajax({
			url: baseUrl + 'explore/cart/cartModal',
			method: "GET",
			success: function(html) {
				if($('#cart-modal-html').length > 0) {
					$('#cart-modal-html').html(html);
					if(callback) callback();
				}
			}
		});
	}

	window.showCartModal = function showCartModal() {
		$('.cart_page').trigger('click');
		$('.cart-flag').html(cartActivityMessage);
		fadeout_activity_message();
	}

	loadCartModal();

	/*
	 * The function returns direct text extracted from an element
	 * where it has other child elements present
	 */
	$.fn.rawText = function() {
		var str = '';

		this.contents().each(function() {
			if (this.nodeType == 3) {
				str += this.textContent || this.innerText || '';
			}
		});

		return str;
	};

	/*
	 * Header is fixed. Hence a ghost element is placed behind the header
	 * to compensate for its height
	 * Evaluated on load and resize
	 */
	function compensateHeader() {
		var compensation = $('.header').outerHeight();
		$('.header-compensation').height('91px');
	}

	compensateHeader();
	$(window).resize(compensateHeader);

	/*
	 * Following events capture hover event on the cart button in header
	 */
	$('.header-action').mouseenter(function() {
		var me = $(this);
		var cartDropExists = $('.header-drop', me).length > 0 ? true : false;
		if(cartDropExists) {
			$('.header-drop', me).show();
			$('.header-drop', me).stop(true, true).animate({
				opacity: 1
				/*top: 70*/
			}, 150);
		}
		var logoImgSize = $('.header .logo img').width();
		//console.log(logoImgSize);
		if(logoImgSize == 77) {
			$('.header-drop', me).show();
			$('.header-drop', me).stop(true, true).animate({
				opacity: 1,
				top: 43
			}, 0);
		}
	});

	$('.header-action').mouseleave(function() {
		var me = $(this);
		var cartDropExists = $('.header-drop', me).length > 0 ? true : false;
		if(cartDropExists) {
			$('.header-drop', me).stop(true, true).animate({
				opacity: 0,
				top: 64
			}, 150, function() {
				$('.header-drop', me).hide();
			});
		}
	});

	// Decision to do so was because the '.cart_page' element becomes dynamic
	// and hence fancybox will not work anymore on its click
	// Creating a pseudo element and triggering will do the job
	$('.cart_page_trigger').live('click', function() {
		$('.cart_page').click();
	});

	/*
	 * Global Fancybox settings
	 */

	// Design settings
	window.fancyboxSettings = {
		"overlayOpacity" : 0.6,
		"overlayColor"   : "#000",
		"padding"        : 0,
		"margin"         : 0
	};

	// Set ID of modal for CSS purpose
	window.setIdToModal = function(id) {
		if(!id) id = 'generic';
		$('.modal', $('#fancybox-content')).attr('id', id + '-contents');
	}

	/* Contact Us fancybox modal popup */
	$('#contact-us-top-link a').click(function(e) {
		var modal_id = $(this).parent().attr('id');
		var settings = $.extend(
			fancyboxSettings, {
				onComplete: function() {
					setIdToModal(modal_id);
					$('#contactForm').ajaxSubmit();
				}
			}
		);
		$.fancybox(this, settings);
		return false;
	});
	$('#track-order-top-link a').click(function(e) {
		var modal_id = $(this).parent().attr('id');
		var settings = $.extend(
			fancyboxSettings, {
				onComplete: function() {
					setIdToModal(modal_id);
					$('#contactForm').ajaxSubmit();
				}
			}
		);
		$.fancybox(this, settings);
		return false;
	});
	$('#trackorderform_top_link').live('submit', function(event) {
		var value = $('#trackorderform_top_link #orderid').val();
		var url = $('#trackorderform_top_link #ajaxtrackurl').val()+"?orderid="+value;// $(this).attr('ajaxurl')+"?orderid="+value;
		// console.log(url);
		// event.preventDefault();
		jQuery.get(url, function(response){
			if(response=='false') {
				// alert(typeof response);
				$('#trackorderform_top_link #errormsg').show().html('Please enter a valid Order ID');
				// console.log(response);
			} else {
				return true;
			}
		});
		/*event.preventDefault();*/

		/*jQuery.ajax({
			url: $(this).attr('action'),
			type : 'get',
			data: $(this).serialize(),
			success: function(response){
				// $('.social-like-thumbnails-area').html(response['html']);
				console.log(response);
			}
		});*/

	});

	/*
	 * ajaxSubmit - submit a validated form using AJAX
	 *
	 * Expects a text message reponse from AJAX request.
	 * Appends reponse to form using default success message styling
	 *
	 * If defined, 'success' is called on AJAX success event
	 * Define and use this if you need to make custom modifications
	 * with the AJAX response
	 *
	 * Complete example: (all parameters are optional)

		$('#form').ajaxSubmit({
			validate: true,
			focusOnFirstElement: true,
			success: function(response) {
				console.log(response);
			}
		});

	 *
	 */
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

	$('#fancybox-loading-overlay').live('click', function(){
		$('.ui-dialog.ui-widget.ui-widget-content.ui-corner-all.ui-front.ui-draggable.ui-resizable').hide();
		$('#fancybox-loading-overlay').hide();
	})

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

	//IYC frame
 	var isEmbed = window != window.parent;
	if(isEmbed && window.name == 'iyc_iframe') {
		$('.header_cms_links').css('visibility', 'hidden');
	}
});

