/**
 * Created by kbordyug on 4/9/2015.
 */

function updateShipping() {
    url = jQuery('#mst-address-request').val() + 'load/updateall';
    if (typeof ajax_request !== 'undefined')
        ajax_request.abort();
    ajax_request = jQuery.ajax({
        type: "POST",
        url: url,
        data: jQuery("#onepagecheckout_orderform").serialize(),
        cache: false,
        beforeSend: function () {
            jQuery('.loading_image').show();
            jQuery('.opc_available_additional').css('opacity', '0.5');
            jQuery('.opc_tool-tip_methods').css('opacity', '0.5');
            jQuery('#checkout-review-table-wrapper').css('opacity', '0.5');
            jQuery('#checkout-review-submit button').attr('disabled', 'disabled');
        },
        success: function (html) {
            var _json = jQuery.parseJSON(html);
            jQuery('.loading_image').hide();
            jQuery('.opc_available_additional').css('opacity', '1');
            jQuery('.opc_tool-tip_methods').css('opacity', '1');
            jQuery('#checkout-review-table-wrapper').css('opacity', '1');
            jQuery('#checkout-review-table-wrapper #shopping-cart-totals-table').remove();
            jQuery('#checkout-review-load').html(_json.info);
            jQuery('#checkout-shipping-method-load').html(_json.shipping_method);
            jQuery('#checkout-payment-method-load').html(_json.payment_method);
        },
        complete: function (data) {
            jQuery('#checkout-review-submit button').removeAttr('disabled');
        }
    });
}


jQuery(document).ready(function (jQuery) {
    var newAddStr = "New Address";

    if (jQuery('#billing_customer_address option:selected').text() != newAddStr && jQuery('#billing_use_for_shipping_yes').is(':checked')) {
        updateShipping();
    }

    jQuery('#billing_use_for_shipping_yes').click(function () {
        var length = jQuery("[id='billing_use_for_shipping_yes']:checked").length;
        switch (length) {
            case 1:
                jQuery('#billing_use_for_shipping').val('1');
                break;
            case 0:
                jQuery('#billing_use_for_shipping').val('0');
                break
        }
        updateShipping();
    });

    var selector = " [name='shipping[country_id]']," +
        "[name='shipping[region_id]']," +
        "[name='shipping[region]']," +
        "[name='shipping[postcode]']," +
        "[name='shipping[street][]']," +
        "[name='billing[street][]']," +
        "[name='shipping[city]']," +
        "#shipping_customer_address,#billing_customer_address";


    jQuery(selector).blur(function () {
        updateShipping();
    });

});