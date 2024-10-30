( function( $ ) {
	"use strict";

	$( document ).on( 'ready', function() {

		// Show/hide both sidebars options
		var bothSidebarsField       = $( '#butterbean-control-hoo_post_layout select' ),
			bothSidebarsFieldVal  	= bothSidebarsField.val(),
			bothSidebarsSetting 	= $( '#butterbean-control-hoo_both_sidebars_style, #butterbean-control-hoo_both_sidebars_content_width, #butterbean-control-hoo_both_sidebars_sidebars_width, #butterbean-control-hoo_second_sidebar' );

		bothSidebarsSetting.hide();

		if ( bothSidebarsFieldVal === 'both-sidebars' ) {
			bothSidebarsSetting.show();
		}

		bothSidebarsField.change( function () {

			bothSidebarsSetting.hide();

			if ( $( this ).val() == 'both-sidebars' ) {
				bothSidebarsSetting.show();
			}

		} );

		// Show/hide header options
		var headerField          	= $( '#butterbean-control-hoo_display_header .buttonset-input' ),
			headerMainSettings   	= $( '#butterbean-control-hoo_header_style' );

		if ( $( '#butterbean-control-hoo_display_header #butterbean_hoo_mb_settings_setting_hoo_display_header_off' ).is( ':checked' ) ) {
			headerMainSettings.hide();
		} else {
			headerMainSettings.show();
		}

		headerField.change( function () {

			if ( $( this ).val() === 'off' ) {
				headerMainSettings.hide();
			} else {
				headerMainSettings.show();
			}

		} );

		// Show/hide custom header template field
		var headerStyleField        = $( '#butterbean-control-hoo_header_style select' ),
			headerStyleFieldVal  	= headerStyleField.val(),
			customHeaderSetting 	= $( '#butterbean-control-hoo_custom_header_template' );

		customHeaderSetting.hide();

		if ( headerStyleFieldVal === 'custom' ) {
			customHeaderSetting.show();
		}

		if ( $( '#butterbean-control-hoo_display_header #butterbean_hoo_mb_settings_setting_hoo_display_header_off' ).is( ':checked' ) ) {
			customHeaderSetting.hide();
		}

		headerField.change( function () {

			if ( $( this ).val() === 'off' ) {
				customHeaderSetting.hide();
			} else {
				var headerStyleFieldVal = headerStyleField.val();

				if ( headerStyleFieldVal === 'custom' ) {
					customHeaderSetting.show();
				}
			}

		} );

		headerStyleField.change( function () {

			customHeaderSetting.hide();

			if ( $( this ).val() == 'custom' ) {
				customHeaderSetting.show();
			}

		} );

		// Show/hide left menu for center header style
		var leftMenuSetting = $( '#butterbean-control-hoo_center_header_left_menu' );

		leftMenuSetting.hide();

		if ( headerStyleFieldVal === 'center' ) {
			leftMenuSetting.show();
		}

		if ( $( '#butterbean-control-hoo_display_header #butterbean_hoo_mb_settings_setting_hoo_display_header_off' ).is( ':checked' ) ) {
			leftMenuSetting.hide();
		}

		headerField.change( function () {

			if ( $( this ).val() === 'off' ) {
				leftMenuSetting.hide();
			} else {
				var headerStyleFieldVal = headerStyleField.val();

				if ( headerStyleFieldVal === 'center' ) {
					leftMenuSetting.show();
				}
			}

		} );

		headerStyleField.change( function () {

			leftMenuSetting.hide();

			if ( $( this ).val() == 'center' ) {
				leftMenuSetting.show();
			}

		} );

		// Show/hide title options
		var titleField          	= $( '#butterbean-control-hoo_disable_title .buttonset-input' ),
			titleMainSettings   	= $( '#butterbean-control-hoo_disable_heading, #butterbean-control-hoo_post_title, #butterbean-control-hoo_post_subheading, #butterbean-control-hoo_post_title_style' ),
			titleStyleField     	= $( '#butterbean-control-hoo_post_title_style select' ),
			titleStyleFieldVal  	= titleStyleField.val(),
			pageTitleBgSettings 	= $( '#butterbean-control-hoo_post_title_background, #butterbean-control-hoo_post_title_bg_image_position, #butterbean-control-hoo_post_title_bg_image_attachment, #butterbean-control-hoo_post_title_bg_image_repeat, #butterbean-control-hoo_post_title_bg_image_size, #butterbean-control-hoo_post_title_height, #butterbean-control-hoo_post_title_bg_overlay, #butterbean-control-hoo_post_title_bg_overlay_color' ),
			solidColorElements  	= $( '#butterbean-control-hoo_post_title_background_color' );

		pageTitleBgSettings.hide();
		solidColorElements.hide();

		if ( titleStyleFieldVal === 'background-image' ) {
			pageTitleBgSettings.show();
		} else if ( titleStyleFieldVal === 'solid-color' ) {
			solidColorElements.show();
		}

		if ( $( '#butterbean-control-hoo_disable_title #butterbean_hoo_mb_settings_setting_hoo_disable_title_on' ).is( ':checked' ) ) {
			titleMainSettings.hide();
			pageTitleBgSettings.hide();
			solidColorElements.hide();
		} else {
			titleMainSettings.show();
		}

		titleField.change( function () {

			if ( $( this ).val() === 'on' ) {
				titleMainSettings.hide();
				pageTitleBgSettings.hide();
				solidColorElements.hide();
			} else {
				titleMainSettings.show();
				var titleStyleFieldVal = titleStyleField.val();

				if ( titleStyleFieldVal === 'background-image' ) {
					pageTitleBgSettings.show();
				} else if ( titleStyleFieldVal === 'solid-color' ) {
					solidColorElements.show();
				}
			}

		} );

		titleStyleField.change( function () {

			pageTitleBgSettings.hide();
			solidColorElements.hide();

			if ( $( this ).val() == 'background-image' ) {
				pageTitleBgSettings.show();
			} else if ( $( this ).val() === 'solid-color' ) {
				solidColorElements.show();
			}

		} );

		// Show/hide breadcrumbs options
		var breadcrumbsField        = $( '#butterbean-control-hoo_disable_breadcrumbs .buttonset-input' ),
			breadcrumbsSettings   	= $( '#butterbean-control-hoo_breadcrumbs_color, #butterbean-control-hoo_breadcrumbs_separator_color, #butterbean-control-hoo_breadcrumbs_links_color, #butterbean-control-hoo_breadcrumbs_links_hover_color' );

		if ( $( '#butterbean-control-hoo_disable_breadcrumbs #butterbean_hoo_mb_settings_setting_hoo_disable_breadcrumbs_off' ).is( ':checked' ) ) {
			breadcrumbsSettings.hide();
		} else {
			breadcrumbsSettings.show();
		}

		breadcrumbsField.change( function () {

			if ( $( this ).val() === 'off' ) {
				breadcrumbsSettings.hide();
			} else {
				breadcrumbsSettings.show();
			}

		} );

	} );

} ) ( jQuery );