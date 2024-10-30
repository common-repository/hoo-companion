jQuery(document).ready(function($) {
	$("#header-slider").owlCarousel({
		autoPlay: 3000,
		items : 5,
		itemsDesktop : [1199,4],
		itemsDesktopSmall : [979,4],
	});
	
	$(".featured-main-slider").owlCarousel({
		autoPlay: 3000,
		items : 1,
		itemsDesktop : [1199,4],
		itemsDesktopSmall : [979,4],
	});
});
	