/**
*  2007-2017 PrestaShop
*
*  @author    Amazzing
*  @copyright Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

$(window).load(function(){
    if (typeof showSuccessMessage != 'undefined' && typeof translate_javascripts != 'undefined'
        && 'Form update success' in translate_javascripts) {
        var showSuccessMessageOriginal = showSuccessMessage,
            savedTxt = translate_javascripts['Form update success'];
        showSuccessMessage = function(msg) {
            showSuccessMessageOriginal(msg);
            if (msg == savedTxt && $('[name="form[id_product]"]').length) {
                var id_product = $('[name="form[id_product]"]').val();
                $.ajax({
                    type: 'POST',
            		url: mtg_ajax_action_path,
            		data: 'action=GetProductMetaFields&id_product='+id_product,
            		dataType : 'json',
            		success: function(r) {
                        if ('meta_fields' in r) {
                            for (var id_lang in r.meta_fields) {
                                for (var meta_name in r.meta_fields[id_lang]) {
                                    var meta_value = r.meta_fields[id_lang][meta_name],
                                        $input = $('#form_step5_'+meta_name+'_'+id_lang);
                                    if (meta_name != 'id_lang' && $input.length && !$.trim($input.val())) {
                                        // console.dir(meta_name+' '+id_lang+': '+meta_value)
                                        $input.val(meta_value);
                                    }
                                }
                            }
                        }
            		},
            		error: function(r) {
                        console.warn($(r.responseText).text() || r.responseText);
            		}
                });
            }
        }
    }
});
/* since 1.6.0 */
