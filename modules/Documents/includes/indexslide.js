/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 * $Id: indexslide.js 6 2015-07-08 07:07:06Z PragmaMx $
 */

jQuery(document).ready(function(){
	
	jQuery('img#index').click(function(){
		if(jQuery("div.indexcontent").css("display")!="block"){
			/*jQuery('.adminForm_fieldset_collapsed').slideUp(200);*/
			/*jQuery(this).next().slideDown(600);*/
			jQuery('div.indexcontent').slideDown(300);
			jQuery("div.indexcontent").css("display","block");
		} else {
			/*jQuery(this).next().slideUp(600);*/
			jQuery('div.indexcontent').slideUp(300);
			jQuery.delay(300);
			jQuery("div.indexcontent").css("display","none");
		}
		
	})
})