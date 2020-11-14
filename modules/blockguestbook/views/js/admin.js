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
 * @package blockguestbook
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */

function blockguestbook_list(id,action,value,type_action){

    if(action == 'active') {
        $('#activeitem' + id).html('<img src="../img/admin/../../modules/blockguestbook/views/img/loader.gif" />');
    }

    $.post('../modules/blockguestbook/ajax.php',
        { id:id,
            action:action,
            value: value,
            type_action: type_action
        },
        function (data) {
            if (data.status == 'success') {


                var data = data.params.content;

                var text_action = '';
                if(type_action == 'guestbook'){
                    text_action = 'guestbook';
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
                            '<a href="javascript:void(0)" onclick="blockguestbook_list('+id+',\'active\', '+action_value+',\''+type_action+'\');" style="text-decoration:none">'+
                        '<img src="../img/admin/../../modules/blockguestbook/views/img/'+img_ok+'.gif" />'+
                        '</a>'+
                    '</span>';
                    $('#activeitem'+id).html(html);


                }

            } else {
                alert(data.message);

            }
        }, 'json');
}




// remove add new comment button //
$('document').ready( function() {

    $('#desc-blockguestbook-new').css('display','none');


});
// remove add new comment button //


function delete_avatar(item_id){
    if(confirm("Are you sure you want to remove this item?"))
    {
        $('.avatar-form').css('opacity',0.5);
        $.post('../modules/blockguestbook/ajax.php', {
                action:'deleteimg',
                item_id : item_id
            },
            function (data) {
                if (data.status == 'success') {
                    $('.avatar-form').css('opacity',1);
                    $('.avatar-button15').remove(); // for ps 15,14
                    $('.avatar-form').html('');
                    $('.avatar-form').html('<img src = "../modules/blockguestbook/views/img/avatar_m.gif" />');


                } else {
                    $('.avatar-form').css('opacity',1);
                    alert(data.message);
                }

            }, 'json');
    }

}