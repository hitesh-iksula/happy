// Global Variables

// Selected Options object
var selected_options = {
	gender: '',
	occasion: '',
	personality: '',
};

// Currently selected attribute
var selected_attribute = '';

var not_selected_image = '';
var get_product_collection_url = '';

var small_slide_duration = 200;
var long_slide_duration = 300;

var default_tooltips = {
	gender: 'Click To Choose A Gender',
	occasion: 'Click To Choose An Occassion',
	personality: 'Click To Choose A Personality',
};

var nothing_selected = 'Nothing Selected';

jQuery(document).ready(function($) {

	// Store URL of the default icon in a variable
	not_selected_image = $('#not_selected_image').prop('src');
	get_product_collection_url = $('#get_product_collection_url').val();

	$('#find-a-gift a').addClass('active');

	// Set default tooltips
	$('.attribute').each(function() {
		$(this).prop('title', default_tooltips[$(this).attr('id').replace('_label', '')]);
	});

	// Event triggered when clicking on an attribute label
	$('.attribute').click(function() {

		// If the attribute is already active- close it
		if($(this).hasClass('open')) {

			$('.gift_attribute').slideUp(long_slide_duration);
			$('.gift_attribute').removeClass('open');
			$(this).removeClass('open');

			$('.gift_initial_note').show();
			$('.close_and_reset').hide();

			$('.gift_finder_head').removeClass('expanded');

			if($(this).prop('title') == nothing_selected) {
				$(this).prop('title', default_tooltips[selected_attribute]);
			}

			if(selected_options[selected_attribute] != '') {
				$('#reset').show();
			}

			selected_attribute = '';

		// If the attribute is closed- open it
		} else {

			var attribute_id = $(this).attr('id');
			selected_attribute = $(this).attr('id').replace('_label', '');

			if($('.gift_attribute.open').length > 0) {

				$('.gift_attribute.open').slideUp(long_slide_duration, 'swing', function() {
					$('.attribute').removeClass('open');
					$('.gift_attribute').removeClass('open');
					$('#' + selected_attribute + '_options').slideDown(small_slide_duration);
					$('#' + selected_attribute + '_options').addClass('open');
					$('#' + attribute_id).addClass('open');
				});

			} else {

				$('#' + selected_attribute + '_options').slideDown(small_slide_duration, 'swing', function() {
					$('.gift_finder_head').addClass('expanded');
				});
				$('#' + selected_attribute + '_options').addClass('open');
				$(this).addClass('open');

			}

			$('.gift_initial_note').hide();
			$('.close_and_reset').show();

			if(selected_options[selected_attribute] == '') {
				$('#' + selected_attribute + '_label').prop('title', nothing_selected);
			}

		}

	});

	// Event triggered when clicking on an option of the active attribute
	$('.attribute_option').on('click touchend', function() {

		// Select it when clicking on it and update the selected options
		if(!$(this).hasClass('selected')) {

			var option_value = $(this).attr('id');
			selected_options[selected_attribute] = option_value;
			$('.attribute_option', $(this).parent()).removeClass('selected');
			$(this).addClass('selected');
			var selected_option_image = $('.yellow_option_image', $(this)).prop('src');
			$('#' + selected_attribute + '_image').prop('src', selected_option_image);
			var option_text = $('.attribute_option_title', $(this)).text();
			$('#' + selected_attribute + '_label').prop('title', option_text);

		// If already selected- unselect it and update the selected options
		} else {

			$(this).removeClass('selected');
			selected_options[selected_attribute] = '';
			$('#' + selected_attribute + '_image').prop('src', not_selected_image);
			$('#' + selected_attribute + '_label').prop('title', nothing_selected);

		}

		$('.found_products').getProductCollection();

	});

	// Event triggered when clicking on reset button
	$('#reset').click(function() {

		$('.gift_attribute').slideUp(long_slide_duration, 'swing', function() {

			$('.gift_initial_note').show();
			$('.close_and_reset').hide();
			$('.attribute_option').removeClass('selected');
			$('.attribute').removeClass('open');
			$('.gift_attribute').removeClass('open');
			$('.attribute_selected_image').prop('src', not_selected_image);
			$('.gift_finder_head').removeClass('expanded');

			selected_options = {
				gender: '',
				occasion: '',
				personality: '',
			};
			selected_attribute = '';

			$('.attribute').each(function() {
				$(this).prop('title', default_tooltips[$(this).attr('id').replace('_label', '')]);
			});

		});

		$('.found_products').emptyProductCollection();

	});

	// Event triggered when clicking on close button
	$('#close').click(function() {

		$('.gift_attribute').slideUp(long_slide_duration, 'swing', function() {

			$('.gift_initial_note').show();
			$('#close').hide();
			$('.attribute').removeClass('open');
			$('.gift_attribute').removeClass('open');
			$('.gift_finder_head').removeClass('expanded');

		});

	});

	// Check if URL parameters are present and make necessary calls
	$('.url_parameters').each(function() {

		selected_attribute = $(this).attr('id');
		var attribute_option = $(this).val();

		selected_options[selected_attribute] = attribute_option;
		$('.attribute_option', $('#' + attribute_option).parent()).removeClass('selected');
		$('#' + attribute_option).addClass('selected');
		var selected_option_image = $('.yellow_option_image', $('#' + attribute_option)).prop('src');
		$('#' + selected_attribute + '_image').prop('src', selected_option_image);
		var option_text = $('.attribute_option_title', $('#' + attribute_option)).text();
		$('#' + selected_attribute + '_label').prop('title', option_text);

	});

	if($('.url_parameters').length > 0) {
		$('.found_products').getProductCollection();
	}

});

(function ($) {

	$.fn.getProductCollection = function() {

		var container = $(this);

		container.emptyProductCollection();
		$('#fancybox-loading').show();
		$('#fancybox-loading-overlay').show();

		$.ajax({
			url: get_product_collection_url,
			dataType: 'json',
			type : 'get',
			data: selected_options,
			success: function(response){

				if(response.html && response.code == 1) {
					container.html(response.html);
					$(".item-image").lazyload({
						effect : "fadeIn",
						threshold : 600,
						skip_invisible : false
					});
				} else {
					container.emptyProductCollection();
				}

				$('#fancybox-loading').hide();
				$('#fancybox-loading-overlay').hide();

			}
		});

	}

	$.fn.emptyProductCollection = function() {

		$(this).empty();

	}

})(jQuery);
