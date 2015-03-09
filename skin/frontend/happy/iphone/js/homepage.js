jQuery(document).ready(function($) {

	retrieve_harpreet_slider();

	function retrieve_harpreet_slider() {
		$.ajax({
			method: 'POST',
			url: harpreet_slider_block_url,
			data: {},
			success: function(data) {
				$('.harpreet-slider-wrapper').html(data);
			}
		});
	}

	// retrieve_main_categories();

	function retrieve_main_categories() {
		$.ajax({
			method: 'POST',
			url: category_block_url,
			data: {
				'color' : 'grey'
			},
			success: function(data) {
				$('.shop_by_category').show();
				$('#home_category_wrapper').append(data);
				var toggle_arrow_html = "<div class='toggle_wrap'><div class='toggleArrow'>&nbsp;</div></div> <!-- Dynamically append from mobile.js -->";
				$("#nav ul li.parent").prepend(toggle_arrow_html);
				$(".home_category_wrapper #nav ul>li:odd, .home_category_wrapper #nav ul>li:odd ul li").addClass("even_sty");
				$(".home_category_wrapper #nav ul>li:even ul li").removeClass("even_sty");
			}
		});
	}

	retrieve_category_slider();

	function retrieve_category_slider() {
		$.ajax({
			method: 'POST',
			url: category_slider_block_url,
			data: {},
			success: function(data) {
				$('.category-slider-wrapper').html(data);
			}
		});
	}

	retrieve_homepage_products();

	function retrieve_homepage_products() {
		$.ajax({
			method: 'POST',
			url: homepage_products_block_url,
			data: {},
			success: function(data) {
				$('#homepage_grid').html(data);
				$(".item-image").lazyload({
					effect : "fadeIn",
					threshold : 600,
					skip_invisible : false
				});
			}
		});
	}

	retrieve_recently_viewed_products();

	function retrieve_recently_viewed_products() {
		$.ajax({
			method: 'POST',
			url: recently_viewed_block_url,
			data: {},
			success: function(data) {
				$('#homepage_grid').after(data);
			}
		});
	}

});
