/**
 * Handles:
 * - Copy to Clipboard functionality
 * - Sort/Direction Dropdowns On Gallery Edit Screens
 *
 * @since 1.5.0
 */
jQuery(document).ready(function($) {
	$('.importfile').each(function() {
		var $input = $(this),
			$label = $input.next('label'),
			labelVal = $label.html();

		$input.on('change', function(e) {
			var fileName = '';

			if (this.files && this.files.length > 1) {
				fileName = (
					this.getAttribute('data-multiple-caption') || ''
				).replace('{count}', this.files.length);
			} else if (e.target.value) {
				fileName = e.target.value.split('\\').pop();
			}

			if (fileName) {
				$label.find('span').html(fileName);
			} else {
				$label.html(labelVal);
			}
		});

		// Firefox bug fix
		$input
			.on('focus', function() {
				$input.addClass('has-focus');
			})
			.on('blur', function() {
				$input.removeClass('has-focus');
			});
	});

	$('#screen-meta-links').prependTo('#envira-header-temp');
	$('#screen-meta').prependTo('#envira-header-temp');
	$('#screen-meta-links').css('display', 'block');

	/**
	 * Copy to Clipboard
	 */
	if (typeof Clipboard !== 'undefined') {
		$(document).on('click', '.envira-clipboard', function(e) {
			var envira_clipboard = new Clipboard('.envira-clipboard');
			e.preventDefault();
		});
	}

	/**
	 * Sort/Direction Dropdowns On Gallery Edit Screens
	 * - Uses choices JS
	 */

	if ($('#envira-config-image-sort').length > 0) {
		var envira_image_sort_choice = new Choices(
			'#envira-config-image-sort',
			{
				searchChoices: false,
				searchEnabled: false,
				itemSelectText: '',
				addItemText: '',
				shouldSort: false,
				shouldSortItems: false,
				classNames: {
					containerInner: 'choices__inner sort_inner',
					containerOuter: 'choices sort_inner',
				},
			},
		);
	}

	if ($('#envira-config-image-sort-dir').length > 0) {
		var envira_image_sort_choice = new Choices(
			'#envira-config-image-sort-dir',
			{
				searchChoices: false,
				searchEnabled: false,
				itemSelectText: '',
				addItemText: '',
				classNames: {
					containerInner: 'choices__inner sort_dir',
					containerOuter: 'choices sort_dir',
				},
			},
		);
	}

	/**
	 * Widget Dropdowns
	 * - Uses choices JS
	 */

	$('.widgets-sortables').on('click', 'div.widget-top', function(
		event,
		element,
	) {
		var the_id = $(this)
			.parent()
			.attr('id');

		if (the_id.indexOf('envira-album') !== -1) {
			var action = 'envira_widget_get_albums';
			var keyword = 'album';
		} else {
			var action = 'envira_widget_get_galleries';
			var keyword = 'gallery';
		}

		var previous_selection = $(
				'#' + the_id + ' select.form-control',
			).val(),
			previous_selection_text = $(
				'#' + the_id + ' select.form-control option:selected',
			).text();

		/* clear to prevent duplicates */

		$('#' + the_id + ' select.form-control')
			.find('option')
			.remove();

		if ($('#' + the_id + ' select.form-control').length > 0) {
			var singleFetch = new Choices(
				'#' + the_id + ' select.form-control',
				{
					searchPlaceholderValue: 'Search for an ' + keyword,
					loadingText: '',
					itemSelectText: '',
				},
			);

			singleFetch.ajax(function(callback) {
				fetch(ajaxurl, {
					method: 'POST',
					headers: {
						'Content-Type':
							'application/x-www-form-urlencoded; charset=utf-8',
					},
					body: 'action=' + action /* add &_wpnonce=123 */,
					credentials: 'same-origin',
				})
					.then(function(response) {
						response.json().then(function(data) {
							callback(
								data.galleries,
								'gallery_id',
								'gallery_title',
							);
							if (previous_selection !== undefined) {
								singleFetch.setValueByChoice(
									previous_selection,
								);
							}
						});
					})
					.catch(function(error) {
						console.log(error);
					});
			});
		}
	});

	$(document).on('widget-updated', function(event, widget) {
		var widget_id = $(widget).attr('id');

		if (widget_id.indexOf('album') !== -1) {
			var action = 'envira_widget_get_albums';
			var keyword = 'album';
		} else {
			var action = 'envira_widget_get_galleries';
			var keyword = 'gallery';
		}

		var previous_selection = $(
				'#' + widget_id + ' select.form-control',
			).val(),
			previous_selection_text = $(
				'#' + widget_id + ' select.form-control option:selected',
			).text();

		/* clear to prevent duplicates */

		$('#' + widget_id + ' select.form-control')
			.find('option')
			.remove();

		if ($('#' + widget_id + ' select.form-control').length > 0) {
			var singleFetch = new Choices(
				'#' + widget_id + ' select.form-control',
				{
					searchPlaceholderValue: 'Search for an ' + keyword,
					loadingText: '',
					itemSelectText: '',
				},
			);

			singleFetch.ajax(function(callback) {
				fetch(ajaxurl, {
					method: 'POST',
					headers: {
						'Content-Type':
							'application/x-www-form-urlencoded; charset=utf-8',
					},
					body: 'action=' + action /* add &_wpnonce=123 */,
					credentials: 'same-origin',
				})
					.then(function(response) {
						response.json().then(function(data) {
							callback(
								data.galleries,
								'gallery_id',
								'gallery_title',
							);
							if (previous_selection !== undefined) {
								singleFetch.setValueByChoice(
									previous_selection,
								);
							}
						});
					})
					.catch(function(error) {
						console.log(error);
					});
			});
		}

		// any code that needs to be run when a widget gets updated goes here
		// widget_id holds the ID of the actual widget that got updated
		// be sure to only run the code if one of your widgets got updated
		// otherwise the code will be run when any widget is updated
	});

	$(document).on('widget-added', function(event, widget) {
		var widget_id = $(widget).attr('id');
		// any code that needs to be run when a new widget gets added goes here
		// widget_id holds the ID of the actual widget that got added
		// be sure to only run the code if one of your widgets got added
		// otherwise the code will be run when any widget is added
	});
});
