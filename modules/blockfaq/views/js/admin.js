/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /*
 *
 * @author    StorePrestaModules SPM
 * @category content_management
 * @package blockfaq
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */

function blockfaq_list(id,action,value,type_action){

    if(action == 'active') {
        $('#activeitem' + id).html('<img src="../img/admin/../../modules/blockfaq/views/img/loader.gif" />');
    }

    $.post('../modules/blockfaq/ajax.php',
        { id:id,
            action:action,
            value: value,
            type_action: type_action
        },
        function (data) {
            if (data.status == 'success') {


                var data = data.params.content;

                var text_action = '';
                if(type_action == 'categoryfaq'){
                    text_action = 'faq category';
                } else if(type_action == 'questionfaq'){
                    text_action = 'question';
                }

                if(action == 'active'){

                    $('#activeitem'+id).html('');
                    if(value == 0){
                        var img_ok = 'ok';
                        var action_value = 1;
                    } else {
                        var img_ok = 'no_ok';
                        var action_value = 0;
                    }
                    var html = '<span class="label-tooltip" data-original-title="Click here to activate or deactivate '+text_action+' on your site" data-toggle="tooltip">'+
                            '<a href="javascript:void(0)" onclick="blockfaq_list('+id+',\'active\', '+action_value+',\''+type_action+'\');" style="text-decoration:none">'+
                        '<img src="../img/admin/../../modules/blockfaq/views/img/'+img_ok+'.gif" />'+
                        '</a>'+
                    '</span>';
                    $('#activeitem'+id).html(html);


                }

            } else {
                alert(data.message);

            }
        }, 'json');
}







