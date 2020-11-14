/**
 *  2019 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2019 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 */

var FSAU = FSAU || { };

$(document).ready(function(){
    if (window.location.hash) {
        var refresh_params = false;
        for (var anchor in FSAU.product_urls) {
            if (anchor === window.location.hash) {
                console.log(anchor);
                if (!FSAU.product_urls.hasOwnProperty(anchor)) {
                    continue;
                }
                refresh_params = FSAU.product_urls[anchor];
            }
        }

        if (refresh_params) {
            for (var i in refresh_params) {
                if (!refresh_params.hasOwnProperty(i)) {
                    continue;
                }
                var param_group = refresh_params[i].group;
                var param_value = refresh_params[i].value;
                var name_selector = 'group['+param_group+']';

                var input_type = $('input[name=\''+name_selector+'\']').attr('type');
                if (input_type === undefined) {
                    input_type = 'select';
                }

                if (input_type === 'select') {
                    $('#group_'+param_group).val(param_value);
                }

                if (input_type === 'radio') {
                    $('input[name=\''+name_selector+'\']').prop('checked', '');
                    $('input[name=\''+name_selector+'\'][value=\''+param_value+'\']').prop('checked', 'checked');
                }
            }

            prestashop.emit('updateProduct', { eventType: 'updatedProductCombination', event: null });
        }
    }
});
