(function($) {

	$.fn.autoSuggest = function(userSettings) {
		var settings = {
			container: '#search_autocomplete',
			row: 'li',
			rowText: 'a',
			activeClass: 'active',
			form: '#search_mini_form',
			closestMatchIdentifier: 'search-closest-match',
			noMatchIdentifier: 'search-no-match',
			correction: '.correction'
		};
		settings = $.extend(settings, userSettings);

		var searchBox = this;
		var searchSuggestions = $(settings.container);
		var searchSuggestionRow = settings.row;
		var activeClass = settings.activeClass;
		var activeClassDot = '.' + activeClass;
		var suggestionActiveElement;
		var suggestionActiveIndex = 0;
		var searchTextEnteredByUser;
		var searchSuggestionsLoaded = false;
		var form = $(settings.form);

		var deadKeys = [37, 38, 39, 40, 16, 17, 18, 20];

		var getSearchSuggestions = function (hasCallback, callback) {
			$.ajax({
				url: settings.searchUrl,
				method: "GET",
				data: {
					'q': searchBox.val()
				},
				success: function(response) {
					if(hasCallback) callback(response);
				}
			});
		}

		var populateSuggestions = function (html) {
			searchSuggestions.html(html);
			searchSuggestions.css('right', $(window).outerWidth() - searchBox.offset().left - searchBox.outerWidth());
			searchSuggestions.css('top', searchBox.offset().top + searchBox.outerHeight() - $(document).scrollTop());
			searchSuggestions.css('min-width', searchBox.outerWidth());
			searchBox.addClass(activeClass);
			searchSuggestionsLoaded = true;
		}

		searchBox.focus(function(event) {
			if(searchSuggestionsLoaded) {
				searchSuggestions.show();
				$(this).addClass(activeClass);
			}
			$(searchSuggestionRow + activeClassDot, searchSuggestions).removeClass(activeClass);
			suggestionActiveIndex = 0;
		});

		form.submit(function(event) {
			if(suggestionActiveElement.length > 0) {
				event.preventDefault();
				window.location = $('a', suggestionActiveElement).attr('href');
			}
		});

		$(document).live('click', function(event) {
			if(event.target.id != searchBox.attr('id') && searchSuggestionsLoaded) {
				searchBox.removeClass(activeClass);
				searchSuggestions.hide();
			}
		});

		searchBox.keydown(function(event) {
			if(event.which != 40 && event.which != 38) {
				$(searchSuggestionRow + activeClassDot, searchSuggestions).removeClass(activeClass);
				suggestionActiveIndex = 0;
			}
		});

		searchBox.keyup(function(event) {
			if($.inArray(event.which, deadKeys) == -1) {
				if($(this).val().length >= 3) {
					suggestionActiveElement = null;
					getSearchSuggestions(true, populateSuggestions);
				}
			}
		});

		$(document).keydown(function(event) {
			if(event.which == 40) {
				if(event.target.id == searchBox.attr('id') && suggestionActiveIndex == 0) {
					searchTextEnteredByUser = searchBox.val();
					event.preventDefault();

					$(searchSuggestionRow, searchSuggestions).first().addClass(activeClass);
					suggestionActiveIndex++;

					suggestionActiveElement = $(searchSuggestionRow + activeClassDot, searchSuggestions);
					if(suggestionActiveElement.attr('id') == settings.closestMatchIdentifier) {
						searchBox.val($(settings.rowText + ' ' + settings.correction, suggestionActiveElement).text()).focus();
						getSearchSuggestions(true, populateSuggestions);
					} else if(suggestionActiveElement.attr('id') == settings.noMatchIdentifier) {
						searchBox.focus();
					} else {
						searchBox.val($(settings.rowText, suggestionActiveElement).text());
					}
				}
				else if(suggestionActiveIndex >= 1) {
					event.preventDefault();
					if($(searchSuggestionRow + activeClassDot, searchSuggestions).next().length > 0) {
						$(searchSuggestionRow + activeClassDot, searchSuggestions).removeClass(activeClass).next().addClass(activeClass);
						suggestionActiveIndex++;

						suggestionActiveElement = $(searchSuggestionRow + activeClassDot, searchSuggestions);
						searchBox.val($(settings.rowText, suggestionActiveElement).text());
					}
				}
			}

			if(event.which == 38) {
				if(suggestionActiveIndex > 1) {
					event.preventDefault();
					if($(searchSuggestionRow + activeClassDot, searchSuggestions).prev().length > 0) {
						$(searchSuggestionRow + activeClassDot, searchSuggestions).removeClass(activeClass).prev().addClass(activeClass);
						suggestionActiveIndex--;

						suggestionActiveElement = $(searchSuggestionRow + activeClassDot, searchSuggestions);
						searchBox.val($(settings.rowText, suggestionActiveElement).text());
					}
				}
				else if(suggestionActiveIndex == 1) {
					event.preventDefault();
					$(searchSuggestionRow + activeClassDot, searchSuggestions).removeClass(activeClass);
					suggestionActiveIndex = 0;

					searchBox.val(searchTextEnteredByUser).removeClass(activeClass);
				}
			}
		});

	};

})(jQuery);
