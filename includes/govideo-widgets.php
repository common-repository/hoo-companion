<?php

require_once 'widgets/govideo/widget-header-slider.php';
require_once 'widgets/govideo/widget-featured-slider.php';
require_once 'widgets/govideo/widget-beside-featured-slider.php';
require_once 'widgets/govideo/widget-section.php';
require_once 'widgets/govideo/widget-posts.php';

// Register and load the widget
function hooc_load_widget() {
    register_widget( 'HoocHeaderSlider_Widget' );
	register_widget( 'HoocFeaturedSlider_Widget' );
	register_widget( 'HoocBesideFeaturedSlider_Widget' );
	register_widget( 'HoocSectionPosts_Widget' );
	register_widget( 'HoocPosts_Widget' );
}
add_action( 'widgets_init', 'hooc_load_widget' );


function hooc_move_widget_area($section_args, $section_id, $sidebar_id) {
	
	$sections = array( 'hoo-header-slider','hoo-featured-slider', 'hoo-beside-featured-slider', 'hoo-main-section', 'hoo-sidebar-home' );
    if(  in_array($sidebar_id, $sections ) ) {
        $section_args['panel'] = 'panel-front-page-layout';
    }

    return $section_args;
}

add_filter('customizer_widgets_section_args', 'hooc_move_widget_area', 10, 3);