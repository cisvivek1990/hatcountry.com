var ExitOfferPopup = {

    setSettings: function(url, type) {
        var nexUrl = url.replace('{{'+type+'}}', $(type).value);
        window.location = nexUrl;
    },

    addField: function(formId, requestUrl, grid)
    {
        $(formId).removeClassName('ignore-validate');
        var validationResult = $(formId).select('input',
                'select', 'textarea').collect( function(elm) {
                return Validation.validate(elm, {
                    useTitle :false,
                    onElementValidate : function() {
                    }
                });
            }).all();
        $(formId).addClassName('ignore-validate');

        if (!validationResult) {
            return;
        }

        var params = {
            'field_id': $$('#'+formId+' #entity_id')[0].value,
            'popup_id': $$('#'+formId+' #popup_id')[0].value,
            'name': $$('#'+formId+' #name')[0].value,
            'title': $$('#'+formId+' #title')[0].value,
            'options': $$('#'+formId+' #options')[0].value,
            'is_required': $$('#'+formId+' #is_required')[0].value,
            'position': $$('#'+formId+' #position')[0].value,
            'type': $$('#'+formId+' #type')[0].value
        };
        var dataGrid = eval(grid);
        new Ajax.Request(requestUrl, {
            parameters :params,
            method :'post',
            onComplete : function (transport, param){
                var response = false;
                if (transport && transport.responseText) {
                    response = eval('(' + transport.responseText + ')');
                }
                if (dataGrid) {
                    dataGrid.reload();
                }

                if (response && response.messages) {
                    ExitOfferPopup.resetFieldForm();
                    $('messages').update(response.messages);
                }

                if (response && response.error) {
                    alert(response.error);
                }
            }
        });
    },

    deleteField: function(rowId, requestUrl, grid)
    {
        var params = {
            'field_id': rowId
        };
        var dataGrid = eval(grid);
        new Ajax.Request(requestUrl, {
            parameters :params,
            method :'post',
            onComplete : function (transport, param){
                var response = false;
                if (transport && transport.responseText) {
                    response = eval('(' + transport.responseText + ')');
                }
                if (dataGrid) {
                    dataGrid.reload();
                }

                if (response && response.messages) {
                    ExitOfferPopup.resetFieldForm();
                    $('messages').update(response.messages);
                }

                if (response && response.error) {
                    alert(response.error);
                }
            }
        });
    },

    editField :function(id, data)
    {
        var formId = 'additional_field_fieldset';
        $$('#'+formId+' #popup_id')[0].value = data.popup_id;
        $$('#'+formId+' #name')[0].value = data.name;
        $$('#'+formId+' #name')[0].disabled = true;
        $$('#'+formId+' #title')[0].value = data.title;
        $$('#'+formId+' #options')[0].value = data.options;
        $$('#'+formId+' #is_required')[0].value = data.is_required;
        $$('#'+formId+' #position')[0].value = data.position;
        $$('#'+formId+' #type')[0].value = data.type;
        $$('#'+formId+' #type')[0].disabled = true;
        $$('#'+formId+' #entity_id')[0].value = data.entity_id;
        $('field_add_button').setStyle({'display':'none'});
        $('field_edit_button').setStyle({'display':'block'});
        $$('#exitoffer_series_tab_additional_fields_content .fieldset-legend')[0].innerHTML = 'Edit Additional Field #'+id;
    },

    resetFieldForm: function()
    {
        var formId = 'additional_field_fieldset';
        $$('#'+formId+' #entity_id')[0].value = '';
        $$('#'+formId+' #popup_id')[0].value = '';
        $$('#'+formId+' #name')[0].value = '';
        $$('#'+formId+' #name')[0].disabled = false;
        $$('#'+formId+' #title')[0].value = '';
        $$('#'+formId+' #options')[0].value = '';
        $$('#'+formId+' #is_required')[0].value = '';
        $$('#'+formId+' #position')[0].value = '';
        $$('#'+formId+' #type')[0].value = '';
        $$('#'+formId+' #type')[0].disabled = false;
        $('field_add_button').setStyle({'display':'block'});
        $('field_edit_button').setStyle({'display':'none'});
        $$('#exitoffer_series_tab_additional_fields_content .fieldset-legend')[0].innerHTML = 'Add New Additional Field';
    }
};
/*
document.observe("dom:loaded", function() {
    Event.observe($('giftcard_import_action'),'change', function(){
        GiftCard.changeImportAction($('giftcard_import_action').value);
    });
});

*/

