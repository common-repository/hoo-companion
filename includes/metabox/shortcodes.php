<?php
/**
 * Display shortcodes in front end
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Shortcode before the top bar
if ( ! function_exists( 'hoo_shortcode_before_top_bar' ) ) {
	function hoo_shortcode_before_top_bar() {

		if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_shortcode_before_top_bar', true ) ) {
			echo do_shortcode( $meta );
		}

	}
	add_action( 'hoo_before_top_bar', 'hoo_shortcode_before_top_bar', 10 );
}

// Shortcode after the top bar
if ( ! function_exists( 'hoo_shortcode_after_top_bar' ) ) {
	function hoo_shortcode_after_top_bar() {

		if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_shortcode_after_top_bar', true ) ) {
			echo do_shortcode( $meta );
		}

	}
	add_action( 'hoo_after_top_bar', 'hoo_shortcode_after_top_bar', 10 );
}

// Shortcode before the header
if ( ! function_exists( 'hoo_shortcode_before_header' ) ) {
	function hoo_shortcode_before_header() {

		if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_shortcode_before_header', true ) ) {
			echo do_shortcode( $meta );
		}

	}
	add_action( 'hoo_before_header', 'hoo_shortcode_before_header', 10 );
}

// Shortcode after the header
if ( ! function_exists( 'hoo_shortcode_after_header' ) ) {
	function hoo_shortcode_after_header() {

		if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_shortcode_after_header', true ) ) {
			echo do_shortcode( $meta );
		}

	}
	add_action( 'hoo_after_header', 'hoo_shortcode_after_header', 10 );
}

// Shortcode before the title
if ( ! function_exists( 'hoo_shortcode_before_title' ) ) {
	function hoo_shortcode_before_title() {

		if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_has_shortcode', true ) ) {
			echo do_shortcode( $meta );
		}

	}
	add_action( 'hoo_before_page_header', 'hoo_shortcode_before_title', 10 );
}

// Shortcode after the title
if ( ! function_exists( 'hoo_shortcode_after_title' ) ) {
	function hoo_shortcode_after_title() {

		if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_shortcode_after_title', true ) ) {
			echo do_shortcode( $meta );
		}

	}
	add_action( 'hoo_after_page_header', 'hoo_shortcode_after_title', 10 );
}

// Shortcode before the footer widgets
if ( ! function_exists( 'hoo_shortcode_before_footer_widgets' ) ) {
	function hoo_shortcode_before_footer_widgets() {

		if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_shortcode_before_footer_widgets', true ) ) {
			echo do_shortcode( $meta );
		}

	}
	add_action( 'hoo_before_footer_widgets', 'hoo_shortcode_before_footer_widgets', 10 );
}

// Shortcode after the footer widgets
if ( ! function_exists( 'hoo_shortcode_after_footer_widgets' ) ) {
	function hoo_shortcode_after_footer_widgets() {

		if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_shortcode_after_footer_widgets', true ) ) {
			echo do_shortcode( $meta );
		}

	}
	add_action( 'hoo_after_footer_widgets', 'hoo_shortcode_after_footer_widgets', 10 );
}

// Shortcode before the footer bottom
if ( ! function_exists( 'hoo_shortcode_before_footer_bottom' ) ) {
	function hoo_shortcode_before_footer_bottom() {

		if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_shortcode_before_footer_bottom', true ) ) {
			echo do_shortcode( $meta );
		}

	}
	add_action( 'hoo_before_footer_bottom', 'hoo_shortcode_before_footer_bottom', 10 );
}

// Shortcode after the footer bottom
if ( ! function_exists( 'hoo_shortcode_after_footer_bottom' ) ) {
	function hoo_shortcode_after_footer_bottom() {

		if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_shortcode_after_footer_bottom', true ) ) {
			echo do_shortcode( $meta );
		}

	}
	add_action( 'hoo_after_footer_bottom', 'hoo_shortcode_after_footer_bottom', 10 );
}