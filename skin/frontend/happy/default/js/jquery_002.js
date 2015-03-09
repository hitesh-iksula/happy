var sticky_status = 1;
var container_height_threshold_crossed = 0;
var container_id = '';

(function ($) {
	var winWidthScroll = $(window).width();
	var padValue = (winWidthScroll > 900 ? 90 : 90);

    $.fn.stickyHeader = function (options) {

        var settings = $.extend({
            stickyClass: 'header',
            padding: padValue
        }, options);

		var contentMarg = 200;

        return $(this).each(function () {

            var container = $(this);
            var header = $('.' + settings.stickyClass, container);

            var originalCss = {
                position: header.css('position'),
                top: header.css('top'),
                width: header.css('width')
            };

            var placeholder = undefined;
            var originalWidth = header.outerWidth();

			//var categoryProductItemAreaHeightNew = $('.test').;
            $(window).scroll(function () {

                if(sticky_status == 1) {

                    var containerTop = container.offset().top;
                    var headerOrigin = header.offset().top;
                    var headerHeight = header.outerHeight();

                    /* custom code to check if the threshold is crossed */
                    if(header.hasClass('the_fixed_header')) {
                        container_id = container.attr('id');
                        container_id = container_id.replace('category', 'container');
                        if(visible_product_count[container_id] > 3) {
                            container_height_threshold_crossed = 0;
                        } else {
                            container_height_threshold_crossed = 1;
                        }
                    }

                    if(container_height_threshold_crossed == 0) {
                        var containerHeight = container.outerHeight() - ($(".one_category:first .category-products ul li:first").outerHeight() + 75);
                    } else {
                        var containerHeight = container.outerHeight();
                    }

                    var containerTop = container.offset().top;
                    var containerSize = container.outerHeight();
                    var pageOffset = $(window).scrollTop() + settings.padding;
                    var containerBottom = containerHeight + containerTop;

                    if (pageOffset < containerTop && placeholder != undefined) {
                        if (placeholder != undefined) {
                            placeholder.remove();
                            placeholder = undefined;
                            header.css(originalCss);
                            header.removeClass('the_fixed_header');
                        }
                    }
                    else if (pageOffset > containerTop && pageOffset < (containerBottom - headerHeight)) {
                        if (placeholder == undefined) {
                            placeholder = $('<div/>')
                            .css('height', header.outerHeight() + 'px')
                            .css('width', header.width() + 'px');
                            header.before(placeholder);
                            header.css('position', 'fixed');
                            header.css('width', originalWidth + 'px');
                            $('.' + settings.stickyClass).removeClass('the_fixed_header');
                            header.addClass('the_fixed_header');
                        }
                        /*header.css('top', settings.padding + 'px');*/
                        header.addClass('the_fixed_header');
                    }
                    else if (pageOffset > (containerBottom - headerHeight)) {
                        //header.css('top', (containerBottom - headerHeight) - pageOffset + settings.padding + 'px');
                        header.removeClass('the_fixed_header');
                    }

                } else {

                    if (placeholder != undefined) {
                        placeholder.remove();
                        placeholder = undefined;
                        header.css(originalCss);
                        header.removeClass('the_fixed_header');
                    }

                }

            });

        });
    }

})(jQuery);

