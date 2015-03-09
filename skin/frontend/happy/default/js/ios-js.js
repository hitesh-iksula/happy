/* Js for IOS */

var footer = "closed";

jQuery(document).ready(function($){
	 if(navigator.platform == 'iPad' || navigator.platform == 'iPhone' || navigator.platform == 'iPod'){
		$(".left_categories_floater, .home-cart-sidebar, .header-container").css("position","absolute");
		function changeFooterPosition() {
			$(".footer-container").css("bottom","inherit");
			var x=window.scrollY;
			var y=window.scrollY+60;
			var windowHt = window.innerHeight;
			var footHt = $(".footer-container").height();
			var winScrollHt = x+ (windowHt - footHt);
			$(".header-container").animate({top:x});
			$(".left_categories_floater,  .home-cart-sidebar").animate({top:y});
			$(".footer-container").animate({top:winScrollHt});
		}

		 $(document).bind('scroll', function() {
        	changeFooterPosition();
    	 });
		
	 }

});
