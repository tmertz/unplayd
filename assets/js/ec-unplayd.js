jQuery(document).ready( function() {
	
	var allPanels = jQuery('.unplayd-content > li > .unplayd-body').hide();
		
	jQuery('.unplayd-content > li > h4 > a').click(function() {
		if( jQuery(this).parent().next().hasClass('open') ) {
			allPanels.slideUp();
			allPanels.removeClass('open');
		} else {
			allPanels.slideUp();
			allPanels.removeClass('open');
			jQuery(this).parent().next().addClass('open').slideDown();
			return false;
		}
	});

});