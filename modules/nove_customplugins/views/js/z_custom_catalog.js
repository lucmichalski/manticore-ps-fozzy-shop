$(document).ready(function(){ 
  $("#cbp-hrmenu1").click(function() {
  $(".block-categories").toggle();
  $(".modal-backdrop").toggle();
  $("body").toggleClass('no_scroll');
  if ($("#desktop-header").hasClass("stuck-header")) { $('#footer .block-categories').toggleClass('stuck_category'); }
  });

  $(".modal-backdrop").click(function() {
  $(".block-categories").toggle();
  $(".modal-backdrop").toggle();
  $("body").toggleClass('no_scroll');
  if ($("#desktop-header").hasClass("stuck-header")) { $('#footer .block-categories').toggleClass('stuck_category'); }
  });
  
$(function() {
	$(".modal-cover").mouseenter(function() {
		document.onmousewheel = function (e) {
		  e.preventDefault();
		}
	}).mouseleave(function() {
		document.onmousewheel = null;
	})
})
});