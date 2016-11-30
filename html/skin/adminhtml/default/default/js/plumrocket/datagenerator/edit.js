/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.

DISCLAIMER

Do not edit or add to this file

@package	Plumrocket_Rss_Generator-v1.4.x
@copyright	Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
@license	http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/

jQuery(document).ready(function(){
	jQuery('#datagenerator_url_key').keyup(function() {
		var val = jQuery(this).val();
		jQuery('#note_url_key span').text( baseUrl + val );
	}).keyup();

	jQuery('#show_options').click(function() {
		jQuery('#list_options').fadeToggle(200);
		jQuery(this).text( jQuery(this).text() == 'Show options'? 'Hide options': 'Show options');
		return false;
	});
	
	var soft_fields = ['name', 'url_key', 'count', 'cache_time', 'date_format', 'replace_from', 'replace_to'];
	
	jQuery('#datagenerator_template_id').change(function() {
		var template_id = parseInt( jQuery('#datagenerator_template_id').val() , 10);
		
		if (template_id) {
			if (_checkMessage()) {
				jQuery(this).hide();
				jQuery('<img />').attr({
					'src': loaderImg,
					'id': 'loader_ajax_template'
				}).insertAfter(this);
		
				jQuery.getJSON(ajaxUrl, {
						template_id: template_id
					}, function(data) {
						if (data.success) {
							for (i = 0, len = soft_fields.length; i < len; i++) {
								var $item = jQuery('#datagenerator_' + soft_fields[i]);
								//if (! $item.val()) {
									$item.val( data[ soft_fields[i] ]);
								//}				
							}
							
							headerEditor.setValue(data['code_header']);
							itemEditor.setValue(data['code_item']);
							footerEditor.setValue(data['code_footer']);
							
							jQuery('#datagenerator_type_feed option').removeAttr('selected').each(function() {
								if (jQuery(this).val() == data.type_feed) {
									jQuery(this).attr("selected",true);
								}
							});
							jQuery('#datagenerator_url_key').keyup();
							jQuery('#datagenerator_type_feed').val(data.type_feed).attr('disabled', 'disabled');
						}
						jQuery('#loader_ajax_template').remove();
						jQuery('#datagenerator_template_id').show();
					}
				);
			}
		} else if (_checkMessage()) {
			headerEditor.setValue('');
			itemEditor.setValue('');
			footerEditor.setValue('');
			
			for (i = 0, len = soft_fields.length; i < len; i++) {
				jQuery('#datagenerator_' + soft_fields[i]).val('');
			}
			
			jQuery('#datagenerator_url_key').keyup();
			
			jQuery('#datagenerator_type_feed').removeAttr('disabled');
		}
	});
	jQuery(window).resize(updateContainer).resize(); //Added by Designer for field fix
	
	var _checkMessage = function() {
		var no_filled = ((jQuery('#datagenerator_name').val() == '') 
			&& (jQuery('#datagenerator_url_key').val() == '')
			&& (jQuery('#code_header').val() == '')
			&& (jQuery('#code_item').val() == '')
			&& (jQuery('#code_footer').val() == '')
		);
			
		return no_filled || confirm('All current options will be replaced. Are you sure?');
	}

});

//Added by Designer for field fix
function updateContainer() {
    var containerWidth =  jQuery(window).width();
    var newwidth = containerWidth - 552;
    if (containerWidth > 990) {
    	jQuery('.CodeMirror').css('width', newwidth + 'px');
    }
}
//end
