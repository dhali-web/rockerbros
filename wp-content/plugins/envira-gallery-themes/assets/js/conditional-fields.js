/**
 * Handles showing and hiding fields conditionally
 */
jQuery( document ).ready(
	function( $ ) {

			// Show/hide elements as necessary when a conditional field is changed
			$( '#envira-gallery-settings input:not([type=hidden]), #envira-gallery-settings select' ).conditions(
				[

				{
					conditions: {
						element: $( '[name="_envira_gallery[lightbox_theme]"]' ),
						type: 'value',
						operator: 'array',
						condition: [ 'base_light', 'infinity_dark', 'infinity_light', 'box_dark', 'box_light', 'burnt_dark', 'burnt_light', 'modern-dark', 'modern-light' ]
					},
					actions: {
						if : [
						{
							element: '#envira-config-lightbox-title-display-box, #envira-config-lightbox-arrows-box, #envira-config-lightbox-arrows-position-box, #envira-config-lightbox-toolbar-box, #envira-config-lightbox-toolbar-title-box, #envira-config-lightbox-toolbar-position-box, #envira-config-thumbnails-position-box, #envira-config-mobile-arrows-box, #envira-config-mobile-toolbar-box, #envira-config-social-lightbox-orientation-box, #envira-config-social-lightbox-outside-box, #envira-config-social-lightbox-position-box, #envira-config-print-lightbox-position-box, #envira-config-downloads-lightbox-position-box',
							action: 'hide'
						}
						]
					}
				}

				]
			);

	}
);
