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

});
