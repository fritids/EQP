//Ajax Post Page
post_page = ThemeAjax.ajaxurl;

	
// Slider
function feature_slide(newid, moveby)
	{	
		jQuery(".post-count span").html(newid);
		new_left_attr = -(newid-1)*moveby;		
		jQuery("#feature-slider").animate({"left": new_left_attr+"px"});
	}
	
//CSS3 Animations
function mobilefadeIn(element){
	element.removeClass("out").removeClass("reverse").addClass("slidedown").addClass("in");
	setTimeout(function(){
		element.addClass("current");
	}, 300);
}
function mobilefadeOut(element){
	element.addClass("out").addClass("reverse").removeClass("in").removeClass("slidedown");
	setTimeout(function(){
		element.removeClass("current");
	}, 300);
}


jQuery(window).load(function()
	{
		jQuery("body").live('pagecreate, pageshow',function(event, ui){
			// Set video width
			jQuery(".post-thumbnail").fitVids();
			
			// Close the menu so that the new menu is ready for the new page
			current_menu = jQuery(".menu-types").children(".active").children("a").attr("rel");
			
			mobilefadeOut(jQuery(".menu-types"));
			if(current_menu !== false)
				mobilefadeOut(jQuery(current_menu));
		});
	
		//Sort out image and slider widths				
		body_width = jQuery('body').width();
		jQuery(".post-thumbnail").fitVids();
		jQuery(".slider, .slider ul li").css("width", (body_width+"px"));
		jQuery(".slider").css("height", (jQuery(".slider img").height()+20)+"px");
			
		jQuery.current_feature = 1;
		jQuery('#feature-slider').live("swiperight", function(){
			// Set the sliding width and max count
			move_by = (jQuery('body').width()+20);
			max_clicks = jQuery("#feature-count").html();
			
			// Check which direction we're moving in
			if(jQuery.current_feature == 1){
				jQuery("#feature-slider").animate({"left": "20px"}, {duration: 250}).animate({"left": "0px"}, {duration: 150});
			} else{
				newid = ((jQuery.current_feature/1)-1);
				// Slide frame and set current slide
				feature_slide(newid, move_by)
				jQuery.current_feature = newid;
			}
			return false;
		});
		jQuery('#feature-slider').live("swipeleft", function(){
			// Set the sliding width and max count
			move_by = (jQuery('body').width()+20);
			max_clicks = jQuery("#feature-count").html();
			
			// Check which direction we're moving in
			if(jQuery.current_feature == max_clicks){
				left = jQuery("#feature-slider").position().left;
				jQuery("#feature-slider").animate({"left": (left-20)+"px"}, {duration: 250}).animate({"left": left+"px"}, {duration: 150});
			} else {
				newid = ((jQuery.current_feature/1)+1);
				// Slide frame and set current slide
				feature_slide(newid, move_by)
				jQuery.current_feature = newid;
			}
			return false;
		});
		
		jQuery('body').bind("orientationchange", function(event, info){

			//Set the body width
			body_width = jQuery('body').width();
			jQuery(".post-thumbnail").fitVids();
								
			// Set the Slider Attributes
			move_by = (body_width+20);
			jQuery(".slider, .slider ul li").css("width", body_width+"px");
			jQuery(".slider").css("height", (jQuery(".slider img").height()+20)+"px");
			
			feature_slide(jQuery.current_feature, move_by);
		});
		
		jQuery(".drop-down").live("tap", function(){	
			if(!jQuery(".navigation").hasClass("in"))
				{
					//Transition
					jQuery(".search-button").removeClass("active");
					mobilefadeOut(jQuery(".search"));
					mobilefadeIn(jQuery(".navigation"));
				}
			else
				{
					//Transition
					jQuery(".search-button").removeClass("active");
					mobilefadeOut(jQuery(".search"));
					mobilefadeOut(jQuery(".navigation"));
				}
			return false;		
		});
		jQuery(".search-button").live("tap", function(){	
			//Set the menu Id
			useid = jQuery(this).attr("rel");			
			
			if(jQuery(this).hasClass("active"))
				{		
					//Remove active class for the button and hide sub menu
					jQuery(".active").removeClass("active");
					mobilefadeOut(jQuery(useid));
				}
			else
				{
					// Apply Active class to the menu
					jQuery(".active").removeClass("active");
					jQuery(this).addClass("active");
					
					if(useid == ".search")
						document.getElementById("s").focus();
						
					//Transition				
					mobilefadeIn(jQuery(useid));
				}
			return false;
		});	
	});