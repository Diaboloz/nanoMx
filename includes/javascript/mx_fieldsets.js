/* pragmaMx CVS $Id: mx_fieldsets.js 6 2015-07-08 07:07:06Z PragmaMx $ */

jQuery(document).ready(function(){
	jQuery('.adminForm_fieldset_collapsed').hide();
	/* jQuery('.adminForm_fieldset .adminForm_fieldset_collapsed:first').slideDown();
	jQuery('.adminForm_fieldset_title:first').toggleClass("adminForm_fieldset_title_expanded");*/
	
	jQuery('h3.adminForm_fieldset_title').click(function(){
		if(jQuery(this).next().css("display")!="block"){
			/*jQuery('.adminForm_fieldset_collapsed').slideUp(200);*/
			jQuery(this).next().slideDown(600);
			/*jQuery(this).next().css("display","block");*/
		} else {
			jQuery(this).next().slideUp(600);
			/*jQuery(this).next().css("display","none");*/
		}
		
		jQuery(".adminForm_fieldset h3").removeClass();
		jQuery(".adminForm_fieldset h3").addClass("adminForm_fieldset_title");
		
		jQuery(this).removeClass();
		jQuery(this).addClass("adminForm_fieldset_title_expanded");
	})
	jQuery('button.fieldset-expand_all').click(function(){
		jQuery('.adminForm_fieldset_collapsed').slideDown(600);
	})
	jQuery('button.fieldset-collapse_all').click(function(){
		jQuery('.adminForm_fieldset_collapsed').slideUp(600);
	})
	
})