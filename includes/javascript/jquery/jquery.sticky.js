/**
 * Sticky Element plugin
 * Copyright (c) 2009 davidmr.d
 * http://plugins.jquery.com/project/Sticky
 * $Id: jquery.sticky.js 6 2015-07-08 07:07:06Z PragmaMx $
 */

(function($){
	
	
	$.fn.sticky = function(options) {
		var settings = $.extend({}, $.fn.sticky.defaults, options);
		
		
		var pos = settings.position.split(" ");
		var topDefined = false;
		var bottomDefined = false;
		var rightDefined = false;
		var leftDefined = false;
		
		for (var i = 0 ; i < pos.length; i++){
			var position = pos[i];
			!topDefined ?  (topDefined = position == "top") : false;
			!bottomDefined ?  (bottomDefined = position == "bottom") : false;
			!rightDefined ? (rightDefined = position == "right") : false;
			!leftDefined ?  (leftDefined = position == "left") : false;
		}
		
		if((topDefined && bottomDefined) || (rightDefined && leftDefined)){
			//do nothing if there is a contradiction in configuration
			return $(this);
		}		
		
		return this.each(function(){
			var obj = $(this);
			obj.css({position: "absolute", zIndex:settings.zIndex, height: settings.height, width: settings.width});
			
			moveObject(obj,topDefined, bottomDefined, rightDefined, leftDefined, settings, false);
					
			$(window).scroll(function(){
				moveObject(obj, topDefined, bottomDefined, rightDefined, leftDefined, settings, true);
			});
		});
	};
	
	

	function moveObject (obj, topDefined, bottomDefined, rightDefined, leftDefined, settings, animate){
		var scrollTopPosition = $(window).scrollTop();
		var scrollLeftPosition = $(window).scrollLeft();
		
		//FIX in version 1.3
		var heightWindow = document.getElementsByTagName('html')[0].clientHeight;
		var widthWindow = document.getElementsByTagName('html')[0].clientWidth;
		
		var newPosition = {top: 0, left: 0};
		
		if(topDefined ){
			newPosition.top = settings.margin + scrollTopPosition;
		}
		
		if(bottomDefined){
			newPosition.top = (scrollTopPosition + heightWindow) - (obj.height() + settings.margin)
		}
		
		if(rightDefined){
			newPosition.left = (scrollLeftPosition + widthWindow) - (obj.width() + settings.margin); 
		}
		
		if(leftDefined){
			newPosition.left = settings.margin + scrollLeftPosition;
		}
		
		var newProps = {top: newPosition.top+"px", left:newPosition.left+"px"};
		
		if(!animate){
			obj.css(newProps);
		}else{
			obj.stop({clearQueue: true, gotoEnd:true});
			obj.animate(newProps, settings.duration);
		}
	};
	
	$.fn.sticky.defaults  = {
			duration: 50,
			position: "right bottom",
			margin: 10,
			zIndex: 2,
			height: "100px",
			width: "100px"
	};
	
	
	
	
})(jQuery);
