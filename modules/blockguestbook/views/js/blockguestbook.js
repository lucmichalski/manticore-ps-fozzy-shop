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

function trim(str) {
	   str = str.replace(/(^ *)|( *$)/,"");
	   return str;
	   }


function show_guestbook_form(){
    $('#add_guestbook').hide();
    $('#text-before-add-guestbook-form').show();
    $('#add-guestbook-form').show();
}



function field_state_change_blockguestbook(field, state, err_text)
{

    // gdpr
    field_gdpr_change_blockguestbook();
    //gdpr

    var field_label = $('label[for="'+field+'"]');
    var field_div_error = $('#'+field);

    if (state == 'success')
    {
        field_label.removeClass('error-label');
        field_div_error.removeClass('error-current-input');
    }
    else
    {
        field_label.addClass('error-label');
        field_div_error.addClass('error-current-input');
    }
    document.getElementById('error_'+field).innerHTML = err_text;

}


