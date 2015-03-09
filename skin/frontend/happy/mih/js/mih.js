jQuery(document).ready(function($) {

	var kids_option_id = '#select_1';
	var kids_option_parent = $('#option-row-1');
	var price_element = '.amount .price';
	var webRupee = '<span class="WebRupee">Rs.</span>';
	var tax_rate = 12.36;

	function showOptionPrice(selected_option) {
		var option_price = selected_option.attr('price') || '';
		var parent = selected_option.parents('.row');
		var prepend = webRupee;
		if(option_price == '') {
			prepend = '';
		}
		$(price_element, parent).html(prepend + addCommas(option_price));
	}

	function updateTax() {
		var price_excl = $('.price', '.price-excluding-tax').first().text();
		price_excl = price_excl.replace('Rs. ', '');
		price_excl = price_excl.replace(',', '');
		price_excl = parseFloat(price_excl);

		var tax = (price_excl * tax_rate) / 100;
		var price_incl = price_excl + tax;
		
		$('.price', '.just-the-tax').html('Rs. ' + addCommas(tax.toFixed(2)));
		$('.price', '.price-including-tax').text('Rs. ' + addCommas(price_incl.toFixed(2)));
		
		/* var price_incl = $('.price', '.price-including-tax').first().text();
		price_incl = price_incl.replace('Rs. ', '');
		price_incl = price_incl.replace(',', '');
		price_incl = parseFloat(price_incl);

		var tax = (price_incl - price_excl).toFixed(2);
		$('.price', '.just-the-tax').html(addCommas(tax)); */
	}

	function onOptionChange() {
		var selected_option = $('option:selected', $(this));
		decideDisabled();
		showOptionPrice(selected_option);
		updateTax();		
	}

	$('.product-custom-option').change(onOptionChange);

	initialize();

	function initialize() {
		$('.product-options .row').not('.heading').each(function() {
			var height_of_row = $('.packages', $(this)).outerHeight();
			$('.column', $(this)).css('height', height_of_row + 'px');
		});
		makeDisabled(kids_option_id);
		$('.align-center', kids_option_parent).text('No. of kids');
		// convertToRupees();
	}

	function decideDisabled() {
		var custom_check = 0;
		$('.product-custom-option').each(function() {
			if($(this).val() == '' && $(this).attr('id') != 'select_1') {
				custom_check += 1;
			}
		});
		if(custom_check == 2) {
			clearTotals();
			makeDisabled(kids_option_id);
		} else {
			removeDisabled(kids_option_id);
		}
	}

	function clearTotals() {
		$(kids_option_id).val('');
		opConfig.reloadPrice();
		$(price_element, kids_option_parent).text('');
	}

	function makeDisabled(id) {
		$(id).attr('disabled', 'disabled');
		$(id).addClass('disabled_input');
	}

	function removeDisabled(id) {
		$(id).removeAttr('disabled');
		$(id).removeClass('disabled_input');
	}

	function addCommas(nStr) {
		nStr += '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	}

	/* function convertToRupees() {
		$('.price-box .price').each(function() {
			var price_html = $(this).html();
			var price_html_webrupee = price_html.replace('Rs. ', webRupee);
			$(this).html(price_html_webrupee);
		});
	} */

});