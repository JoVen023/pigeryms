( function($) {
  'use strict';
$(function(e) {
	
/*------------------------------------------------------------------
	Trending Slider
	-------------------------------------------------------------------*/
	var owl = $("#trending_slider");
	owl.owlCarousel({
	  itemsCustom : [
		[0, 1],
		[450, 1],
		[550, 1],
		[700, 3],
	  ],
	  loop: true,
	  nav : true,
	  navigation : false,
	  autoPlay  : 3000
	});


/*------------------------------------------------------------------
	Popular Brands
	-------------------------------------------------------------------*/
	var owl = $("#popular_brands");
	owl.owlCarousel({
	  itemsCustom : [
		[0, 2],
		[450, 2],
		[550, 2],
		[700, 3],
		[1024, 4],
		[1200, 5],
	  ],
	  loop: true,
	  nav : true,
	  navigation : false,
	  autoPlay  : 3000
	});



/*------------------------------------------------------------------
	Filter-Form
	-------------------------------------------------------------------*/
	$("#filter_toggle").click(function(){
		$("#filter_form").slideToggle();
	});
	
	
	
/*------------------------------------------------------------------
	Other-info
	-------------------------------------------------------------------*/
	$("#other_info").click(function(){
		$("#info_toggle").slideToggle();
	});
	
	
	
/*------------------------------------------------------------------
	back to top
	-------------------------------------------------------------------*/
 var top = $('#back-top');
	top .hide();
	 
		$(window).scroll(function () {
			if ($(this).scrollTop() > 100) {
				top .fadeIn();
			} else {
				top .fadeOut();
			}
		});
		$('#back-top a').on('click', function(e) {
			$('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	 


});


})(jQuery);