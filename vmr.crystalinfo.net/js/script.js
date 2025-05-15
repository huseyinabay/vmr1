/**********************************************************************************

	Project Name: SimpleAdmin CMS Theme
	Project Description: A clean admin theme
	File Name: script.js
	Author: Adi Purdila
	Author URI: http://www.adipurdila.com
	Version: 1.0.0
	
**********************************************************************************/

$(document).ready(function() {

	//Content boxes expand/collapse
	$(".initial-expand").hide();

	$("div.content-module-heading").click(function(){
		$(this).next("div.content-module-main").slideToggle();

		$(this).children(".expand-collapse-text").toggle();
	});
	
});

// sidebar sag sol kaydirma
$(document).ready(function () {
		    $("#toggle").click(function () {
		        if ($(this).data('name') == 'show') {
		            $("#sidebar").animate({
		                width: '0%'
		            }).hide()
		            $("#map").animate({
		                width: '120%'
		            });
		            $(this).data('name', 'hide')
		        } else {
		            $("#sidebar").animate({
		                width: '15%'
		            }).show()
		            $("#map").animate({
		                width: '100%'
		            });
		            $(this).data('name', 'show')
		        }
		    });
		});

		
