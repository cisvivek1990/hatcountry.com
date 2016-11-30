autocompleteAreasOpened = false;

jQuery().ready(function() {
    // Defines for the example the match to take which is any word (with Umlauts!!).
    function _leftMatch(string, area) {
        return string.substring(0, area.selectionStart).match(/[\{]{1}[\w\.]+$/)
    }

    function _setCursorPosition(area, pos) {
        if (area.setSelectionRange) {
            area.setSelectionRange(pos, pos);
        } else if (area.createTextRange) {
            var range = area.createTextRange();
            range.collapse(true);
            range.moveEnd('character', pos);
            range.moveStart('character', pos);
            range.select();
        }
    }

    jQuery('.CodeMirror input').each(function() {
    	var block_id = jQuery(this).parent().parent().prev().attr('id');
    	
    	jQuery(this).autocomplete({
            position: { my : "left top", at: "left bottom" },
            source: function(request, response) {
                var str = _leftMatch(request.term, jQuery(this).get(0));
                str = (str != null) ? str[0] : "";
                response(jQuery.ui.autocomplete.filterGroups(nodes, str, block_id == 'code_item'));
            },
            //minLength: 2,  // does have no effect, regexpression is used instead
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
            // Insert the match inside the ui element at the current position by replacing the matching substring
            select: function(event, ui) {
                var m = _leftMatch(this.value, this);
                if (m != null) {
                	m = m[0];
	                var beg = this.value.substring(0, this.selectionStart - m.length);
	                
	                this.value = beg + ui.item.value + this.value.substring(this.selectionStart, this.value.length);
	                var pos = beg.length + ui.item.value.length;
	                _setCursorPosition(this, pos);
	                return false;
                }
            },
            search:function(event, ui) {
                var m = _leftMatch(this.value, this);
                return (m != null )
            }, 
            open: function (event) {
            	autocompleteAreasOpened = true;
            }, 
            close: function (event) {
            	autocompleteAreasOpened = false;
            }
        });
        /*
        .keyup(function(event, ui) {
            var m = _leftMatch(this.value, jQuery(this).get(0));
            if (m != null) {
                m = m[0];
                if (m == '{c') {
                    var node = '{category.';
                } else if (m == '{p') {
                    var node = '{product.';
                }
                if (node) {
                    var beg = this.value.substring(0, this.selectionStart - m.length);
                    
                    this.value = beg + node + this.value.substring(this.selectionStart, this.value.length);
                    var pos = beg.length + node.length;
                    _setCursorPosition(this, pos);
                }
            } else {
                return true;
            }
        })
        */
	});
        
        
    jQuery.ui.autocomplete.filterGroups = function (array, term, show_all) {
    	var result = [];
        
        if (show_all) {
        	var feed_type = jQuery('#datagenerator_type_feed').val();
        	if (feed_type == 'product') {
		        var _productItems = jQuery.ui.autocomplete.filter(array['product'], term);
		        if (_productItems.length > 0) {
		        	result = [ {'label': '<strong>Product</strong>', 'value': '{product.', 'head': true} ];
		        	result = result.concat(_productItems);
		        }
		        
		        var curr_pos = itemEditor.getCursor();
				var text = itemEditor.getRange({line: 0, ch: 0}, curr_pos);
				
				var count_begins = text.match(/\{product.child\}/g) || [];
				var count_ends = text.match(/\{\/product.child\}/g) || [];
				
				if (count_begins.length > count_ends.length) {
					var _childItems = jQuery.ui.autocomplete.filter(array['child'], term);
			        if (_childItems.length > 0) {
			        	result = result.concat([ {'label': '<strong>Child</strong>', 'value': '{child.', 'head': true} ]);
			        	result = result.concat(_childItems);
			        }
				}
		    }
	        
	        var _categoryItems = jQuery.ui.autocomplete.filter(array['category'], term);
	        if (_categoryItems.length > 0) {
	        	result = result.concat([ {'label': '<strong>Category</strong>', 'value': '{category.', 'head': true} ]);
	        	result = result.concat(_categoryItems);
	        }
	    }
        
        var _siteItems = jQuery.ui.autocomplete.filter(array['site'], term);
        if (_siteItems.length > 0) {
        	result = result.concat([ {'label': '<strong>Site</strong>', 'value': '{site.', 'head': true} ]);
        	result = result.concat(_siteItems);
        }
        
        return result;
    };

    // Overrides the default autocomplete filter function to search only from the beginning of the string
    jQuery.ui.autocomplete.filter = function (array, term) {
        // prevent dublicate
        term = term.replace('{', '');

        var matcher = new RegExp("^" + jQuery.ui.autocomplete.escapeRegex('{' + term), "i");
        return jQuery.grep(array, function (value) {
            return matcher.test('{' + value.label || '') || matcher.test(value.value || '') || matcher.test(value || '');
        });
    };
})