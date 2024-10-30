<?php

	$theme = wp_get_theme();
	$text_domain = $theme->get( 'TextDomain' );
	
	switch( $text_domain ){
		
		case "govideo":
		case "govideo-pro":
			require_once("govideo-widgets.php");
		
		break;
		case "singlepage":
		case "singlepage-pro":
			require_once("singlepage-widgets.php");
		break;
		
		}