/*
Theme Name: Arcade
Theme URI: http://www.obox-design.com/
Description: The first of six limited edition themes from the Obox Signature Series collection.
Version: 1.0
Author: Marc and David Perel
Author URI: http://www.obox-design.com/
*/
post_page = ThemeAjax.ajaxurl;
function check_nan(element, element_value, max_value)
	{
		var len = element_value.length;
		if(isNaN(element_value))
			{
				alert("Only number vlues are allow in this input.");
				element.value = element_value.substring(0, (len/1)-1);
			}
			
		if(max_value && ((element_value/1) > (max_value/1)))
			{
				alert("The maximum value allowed for this input is "+max_value);
				element.value = max_value;
			}
	}
function check_linked(this_id, link_id)
	{
		this_id = "#"+this_id;
		link_div_id = "#"+link_id+"_div";
		link_id = "#"+link_id;
		
		if(jQuery(this_id).attr("value") !== "0")
			{
				jQuery(link_div_id).slideUp();
				jQuery(link_id).attr("disabled", "true");
			}
		else
			{
				jQuery(link_div_id).slideDown();
				jQuery(link_id).removeAttr("disabled");
			}
		
	}
	

jQuery(document).ready(function()
	{
		jQuery(".contained-forms input, .contained-forms select").live("change", function(){
			relid = jQuery(this).attr("name");
			element = jQuery(this);

			jQuery("[rel^='"+relid+"']").each(function(){
				
				if(element.val() == "off" || element.val() == "no")
					{jQuery(this).slideUp();}
				else if(element.attr("type") == "checkbox" && element.attr("checked") == "checked")
					{jQuery(this).slideDown();}
				else if(element.attr("type") == "checkbox")
					{jQuery(this).slideUp();}
				else
					{jQuery(this).slideDown();}
			});
		});
		
		jQuery("#ocmx-options").submit(function(){
			formvalues = jQuery("#ocmx-options").serialize();
			jQuery("#content-block").animate({opacity: 0.50}, 500)
			
			if(document.getElementById("ocmx-note"))
				{jQuery("#ocmx-note").html("<p>Saving...</p>");}
			else
				{jQuery("<div id=\"ocmx-note\" class=\"updated below-h2\"><p>Saving...</p></div>").insertBefore("#header-block");}
						
			jQuery.post(post_page,
				{action : 'mobile_save-options', data: formvalues}, 
				function(data)
						{
							setTimeout(function(){
								jQuery("#content-block").animate({opacity: 1}, 500)
								jQuery("#ocmx-note").html("<p>Your changes were successful.</p>");
							}, 500);
						}
					);
			return false;
		});
		
		jQuery("[id^='ocmx-reset']").click(function(){
			sure_reset = confirm("Are you sure you want reset these options to default?");
			if(sure_reset)
				{
					formvalues = jQuery("#ocmx-options").serialize();
					jQuery("#content-block").animate({opacity: 0.50}, 500)
					
					if(document.getElementById("ocmx-note"))
						{jQuery("#ocmx-note").html("<p>Saving...</p>");}
					else
						{jQuery("<div id=\"ocmx-note\" class=\"updated below-h2\"><p>Saving...</p></div>").insertBefore("#header-block");}
					jQuery.post(post_page,
						{action : 'ocmx_reset-options', data: formvalues}, 
						function(data)
								{
									setTimeout(function(){
										jQuery("#ocmx-note").html("<p>Refreshing Page...</p>");
										jQuery("#content-block").animate({opacity: 1}, 500)
										window.location = jQuery("#ocmx-options").attr("action").replace("&changes_done=1", "")+"&options_reset=1";
									}, 500);
								}
							);
				}
			else
				return false;
		});
		
		
		jQuery("#tabs a").click(function()
			{
				oldtabid = jQuery(".selected").children("a").attr("rel");
				tabid = jQuery(this).attr("rel");
				//$new_class = jQuery($oldtabid).attr("class");
				if(!(jQuery(this).parent().hasClass("selected")))
					{
						jQuery(".selected").removeClass("selected");
						jQuery(this).parent().addClass("selected");
						jQuery(oldtabid).slideUp();
						jQuery(tabid).slideDown();
						
						formaction = jQuery("form").attr("action");
						findtab = formaction.indexOf("tab=");
						action_len = formaction.length;
						tabno = jQuery(this).attr("rel").replace("#tab-", "");
						if(findtab == -1)
							{
								jQuery("form").attr("action", formaction+"&current_tab="+tabno);
							}
						else
							{
								formaction = formaction.substr(0,(findtab+4));
								jQuery("form").attr("action", formaction+tabno);
							}
						jQuery(oldtabid+"-href").fadeOut();
						jQuery(tabid+"-href").fadeIn();
						jQuery(oldtabid+"-href-1").fadeOut();
						jQuery(tabid+"-href-1").fadeIn();
					}
				return false;
			});
		jQuery("a[id^='ocmx_layout_']").click(function(){
			jQuery(".selected").removeClass("selected");
			jQuery(this).parent().addClass("selected");
			
			latout_id = jQuery(this).attr("id");
			layout = jQuery(this).attr("id").replace("ocmx_layout_", "");
			layout_option = layout+"_home_options";
			
			jQuery("#ocmx_home_page_layout").attr("value", layout);
			
			loading = "<li><div class=\"form-wrap\"><a href=\"#\"><img src=\"images/loading.gif\" alt=\"\" /></a></div></li>";
			
			jQuery("#layout_options").html(loading);
			
			i = 1;
			jQuery(".layout-selector").children("li").each(function(){				
				li_id = jQuery(this).children("a").attr("id");
				if(li_id == latout_id && i == 3)
					{
						jQuery("#layout_options").removeClass("clear-left-corner").addClass("clear-right-corner");
					}
				else if(li_id == latout_id && i == 1)
					{
						jQuery("#layout_options").removeClass("clear-right-corner").addClass("clear-left-corner");
					}
				else if(li_id == latout_id)
					{
						jQuery("#layout_options").removeClass("clear-right-corner").removeClass("clear-left-corner");
					}
				i++;
			});
			
			jQuery.get(post_page,
				{action : 'ocmx_layout-refresh', layout_option: layout_option, layout: layout}, 
				function(data)
						{jQuery("#layout_options").html(data).fadeIn()}
					);
			return false;
		});
		
		
		jQuery("a[id^='add_ad_']").live("click", function()
			{
				ad_option = jQuery(this).attr("id").replace("add_ad_", "");
				ad_select = "#"+ad_option;
				ad_div = "#"+ad_option+"_div";
				ad_width = "#ad_width_"+ad_option;
				ad_width = jQuery(ad_width).html();				
				ad_no_ads = "#"+ad_option+"_no_ads";
				
				ad_amt = (jQuery(ad_div+" > ul").children().length);
				jQuery.get(post_page,
					{action : 'mobile_ads-refresh', option: ad_option, prefix: jQuery(this).attr("rel"), width: ad_width, count: ad_amt}, 
					function(data)
							{
								jQuery(ad_no_ads).slideUp();
								newli = "<li style=\"display: none;\">"+data+"</li>";								
								jQuery(newli).attr("class", "no_display");		
								setTimeout(function(){
									jQuery(ad_no_ads).remove()
									jQuery(ad_div+" > ul").html(jQuery(ad_div+" > ul").html()+newli);	
									new_child = (jQuery(ad_div+" > ul").children().length);					
									jQuery(ad_select).attr("value", (new_child-1));
									jQuery(ad_div+" > ul").children("ul li:nth-child("+new_child+")").slideDown("slow");									
								}, 500);
							}
						);
				return false;
			});	
		
		jQuery("a[id^='remove_ad_']").live("click", function()
			{
				ad_prefix = jQuery(this).attr("rel");
				ad_option = ad_prefix+"s";
				ad_select = "#"+ad_option;
				ad_div = "#"+ad_option+"_div";t
				li_id = "#"+jQuery(this).attr("id");
				ad_number = jQuery(this).attr("id").replace("remove_ad_", "");
				ad_number = ad_number.replace(ad_prefix+"_", "");
				
				ad_width = jQuery("#ad_width_"+ad_option).html();
				
				sure_delete = confirm("Are you sure you want to remove this advert?");
				if(sure_delete)
					{
						jQuery.get(post_page,
							{action : 'mobile_ads-remove', option: ad_option, prefix: ad_prefix, ad_number: ad_number}, 
							function(data)
									{
										i = 1;
										ad_list = jQuery(ad_div).children("ul");
										//alert(ad_number+" | "+ad_list.children("li:eq("+(ad_number-1)+")").html());
										ad_list.children("li:eq("+(ad_number-1)+")").slideUp();
										ad_list.children("li").each(function(){
											i++;
											if(ad_number < i)
												{
													jQuery("#"+ad_prefix+"_title_"+i).attr("id", ad_prefix+"_title_"+(i/1-1)).attr("name", ad_prefix+"_title_"+(i/1-1));
													jQuery("#"+ad_prefix+"_link_"+i).attr("id", ad_prefix+"_link_"+(i/1-1)).attr("name", ad_prefix+"_link_"+(i/1-1));
													jQuery("#"+ad_prefix+"_img_"+i).attr("id", ad_prefix+"_img_"+(i/1-1)).attr("name", ad_prefix+"_img_"+(i/1-1));
													jQuery("#"+ad_prefix+"_href_"+i).attr("id", ad_prefix+"_href_"+(i/1-1)).attr("name", ad_prefix+"_href_"+(i/1-1));
													jQuery("#remove_ad_"+ad_prefix+"_"+i).attr("id", "remove_ad_"+ad_prefix+"_"+(i/1-1));
												}
										});
										setTimeout(function(){
											ad_list.children("li:eq("+(ad_number-1)+")").remove();
											new_child = (ad_list.children("li").length);
											jQuery(ad_select).attr("value", (new_child));
										}, 500);
									}
								);
					}
				return false;
			});
		
		jQuery("input[id^='ocmx_small_ad_img_']").live("blur", function()
			{
				ad_id = jQuery(this).attr("id").replace("ocmx_small_ad_img_", "");
				//Set the href Id
				href_id = "#ocmx_small_ad_href_"+ad_id;
				
				jQuery(href_id).attr("src", jQuery(this).attr("value"));
				
			});
		
		jQuery("input[id^='ocmx_mediu_ad_img_']").live("blur", function()
			{
				ad_id = jQuery(this).attr("id").replace("ocmx_mediu_ad_img_", "");
				//Set the href Id
				href_id = "#ocmx_mediu_ad_href_"+ad_id;
				
				jQuery(href_id).attr("src", jQuery(this).attr("value"));
				
			});
		
		//AJAX Upload & Logo Select
		jQuery("li a.remove").live("click", function(){
			sure_delete = confirm("Are you sure you want to remove this image?");
			if(sure_delete)
				{
					attachid = jQuery(this).parent().children("a.image").attr("id");
					jQuery.get(post_page,
						{action : 'ocmx_remove-image', attachid: attachid}, 
						function(data)
								{jQuery("#"+attachid).parent().fadeOut();}
							);
				}
			return false;
		});
		
		jQuery(".previous-logos ul li a.image").live("click", function(){
			parent = jQuery(this).parent();
			grandparent = jQuery(this).parent().parent();
			greatgrandparent = jQuery(this).parent().parent().parent();

			//Text Box for image
			selected_input = greatgrandparent.parent().children().children("input[type='text']");

			//Anchore which displays image
			selected_a = greatgrandparent.parent().children(".logo-display").children("a");
			
			//fadeOut the image
			jQuery(selected_a).stop().fadeOut();
			
			//Get the new image src
			image_value = jQuery(this).children("img").attr("src");
			
			//Change the BG and fade in the image
			setTimeout(function(){
				jQuery(selected_a).css({background: 'url("'+image_value+'") no-repeat center'}).fadeIn();
				jQuery(selected_input).attr("value", image_value);
			}, 500);
			return false;
		})
		
		jQuery("input[id^='clear_upload_']").click(function(){
			input_id = jQuery(this).attr("id").replace("clear_", "")+"_text";
			image_link_id = input_id.replace("_text", "_href");
			var clear_img = confirm("Are you sure you want to clear this image?");
			if(clear_img){
				jQuery("#"+image_link_id).css({background: 'url("") no-repeat center'}).fadeIn();
				jQuery("#"+input_id).attr("value", "")
			}
			return false;
		});
		
		jQuery("input[id^='upload_button_']").each(function(){
			//Get the button Id
			var input_id = "#"+jQuery(this).attr("id");			
			
			//Make sure we're only talking about the button, and not the text field, that'll get messy
			if(input_id.indexOf("_text") <= -1){
				meta = jQuery(this).attr("id").replace("upload_button_", "");
				// Set the approtpriate meta, links and input id's
				var meta = meta.replace("_href", "");
				var image_link_id = input_id+"_href";
				var image_input_id = input_id+"_text";
				
				//Beging the Ajax upload vibe
				new AjaxUpload(jQuery(this).attr("id"), {
				  action:	ThemeAjax.ajaxurl,
				  name: 	jQuery(this).attr("name"), // File upload name
				  data: 	{action:  "mobile_ajax-upload",
							input_name: jQuery(this).attr("name"),
							type: 'upload',
							meta_key: meta,
							data: jQuery(this).id},
				  autoSubmit: true, // Submit file after selection
				  responseType: false,
				  onChange: function(file, extension){
					  new_li = "<img src=\"images/loading.gif\" alt=\"\" /></a>";
					  jQuery("#new-upload-"+meta+" a.image").html(new_li);
					  jQuery("#new-upload-"+meta).fadeIn();
					},
				  onSubmit: function(file, extension){},
				  onComplete: function(file, response) {
					// If there was an error
					if(response.search('Upload Error') > -1){
						jQuery("#new-upload-"+meta+" a:nth-child(1)").html(response);
						setTimeout(function(){jQuery("#new-upload-"+meta).remove();}, 2000);
					}
					else{
						new_image = "<img src=\""+response+"\" alt=\"\" />";
						jQuery(image_link_id).fadeOut();
							
						setTimeout(function(){
							jQuery("#new-upload-"+meta+" a.image").html(new_image);
							jQuery("#new-upload-"+meta).attr("id", "");
							listItem = "<li id=\"new-upload-"+meta+"\" style=\"display: none;\"><a href=\"#\" class=\"image\"></a></li>";
							jQuery(".previous-logos").append(listItem);
							jQuery(image_input_id).attr("value", response);
							jQuery(image_link_id).css({background: 'url("'+response+'") no-repeat center'}).fadeIn();
						}, 1500);						
					}
				  }
				});
			}
		});
		
		
		jQuery(".obox-mobile_page_mobile-themes div input").live("mouseover", function(){
			jQuery(this).css("cursor", "pointer");
			jQuery(".empty").addClass("hover");
		});
		
		jQuery(".obox-mobile_page_mobile-themes div input").live("mouseout", function(){
			jQuery(this).css("cursor", "pointer");
			jQuery(".empty").removeClass("hover");
		});
		
		jQuery("[id^='theme-list-edit']").live("click", function(){
			if(jQuery(this).html() == "Cancel")
				{jQuery("[id^='theme-list-edit']").html("Edit List");}
			else 
				{jQuery("[id^='theme-list-edit']").html("Cancel");}
				
			jQuery(".theme-functions").toggleClass("no_display");
			jQuery("[id^='delete-theme-']").toggleClass("no_display");
			return false;
		});
		
		jQuery("[id^='delete-theme-'] a").live("click", function(){
			var theme_file = jQuery(this).parent().attr("id").replace("delete-theme-", "");
			var theme_name = jQuery(this).parent().parent().children("h4").text();
			var theme_div = jQuery(this).parent().parent();
			var confirm_delete = confirm("Are you sure you want to remove "+theme_name+"?")
			if(confirm_delete)
				{
					theme_div.addClass("loading").children("*").fadeOut();
					jQuery.get(post_page,
							{action : 'mobile_theme-remove', template: theme_file}, 
							function(data){
								if(data.indexOf("Success") !== -1)
									{
										theme_div.fadeOut()
										setTimeout(function(){theme_div.remove();}, 500);
									}
								else
									{alert("There was an error when removing this theme.");}
							}
					);
				}
			return false;
		});
		jQuery("#add-theme").each(function(){
			//Get the button Id
			var input_id = "#"+jQuery(this).attr("id");			
			
			meta = jQuery(this).attr("id").replace("upload_button_", "");
			
			// Set the approtpriate meta, links and input id's
			var meta = meta.replace("_href", "");
			
			//Beging the Ajax upload vibe
			new AjaxUpload(jQuery(this).attr("id"), {
			  action:	ThemeAjax.ajaxurl,
			  name: 	"new_theme", // File upload name
			  data: 	{action:  "mobile_theme-upload", type: 'upload'},
			  autoSubmit: true, // Submit file after selection
			  responseType: false,
			  onChange: function(file, extension){
				  if(extension == "zip"){
					  jQuery("#add-theme").parent().unbind();
					  jQuery(".empty").removeClass("hover");
					  jQuery("#add-theme").parent().addClass("loading").children("*").fadeOut();
				  }
				},
			  onSubmit: function(file, extension){},
			  onComplete: function(file, response) {
				jQuery("#add-theme").parent().children("div:eq(0)").remove();
				jQuery("#add-theme").parent().html(response).removeClass("empty").removeClass("loading").children("div:eq(1)").fadeIn();
				
				// If there was an error
				if(response.search('Upload Error') > -1){
					jQuery("#new-theme").html(response);
					setTimeout(function(){jQuery("#new-theme").remove();}, 2000);
				}
				else{
					return false;
				}
			  }
			});
		});
		
		/*********************/
		/* GALLERY FUNCTIONS */
			
		jQuery("a[id^='edit-image-']").click(function()
			{
				if(jQuery("a[id^='edit-image-']").html() == "edit")
					{
						jQuery(".gallery-item").parent().animate({width: 704}, {duration: 350});
						setTimeout(function(){
						jQuery(".image-form").fadeIn({duration: 450});
											}, 350);
						jQuery("a[id^='edit-image-']").html("cancel");
					}
				else
					{
						jQuery(".image-form").fadeOut({duration: 100});
						setTimeout(function(){
							jQuery(".gallery-item").parent().animate({width: 200}, {duration: 350});
						}, 50);
						jQuery("a[id^='edit-image-']").html("edit");
					}
				return false;
				
			});	
		
		jQuery("#sortable").sortable({
			over: function(event, ui) {jQuery(this).children().css({border: '1px dashed #39F', padding: '5px'})},
			stop: function(event, ui) {jQuery(this).children().css({border: '', padding: '0px'})},
		});
		
		jQuery(".sortable").sortable();
		jQuery("#sortable, .sortable").disableSelection();
		jQuery(".no-sort").sortable({ disabled: true });		
		
		jQuery("#width_1, #height_1, #width_2, #height_2").keyup(function(){
				check_nan(this, jQuery(this).attr("value"));
			});
		
		jQuery("#item").blur(function(){
			check_value = jQuery("#item").attr("value");
			use_value = "";
			validchar = "1234567980abcdefghijklmnopqrstuvwxyz- ";
			i_max = jQuery("#item").attr("value").length;
			for(i = 0; i < i_max; i++)
				{
					this_char = check_value.toLowerCase().charAt(i)
					if(validchar.indexOf(this_char) !== -1)
						{use_value = use_value + this_char;}
				}
			use_value = use_value.replace(/ /g, "-");
			jQuery("#linkTitle").attr("value", use_value);
		});
	});