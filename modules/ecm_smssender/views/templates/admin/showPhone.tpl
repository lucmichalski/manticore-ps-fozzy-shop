<center>
<h3 width="100%">
    {if $header == 1}{l s='Отправка СМС с ТТН перевозчика'}{else}{l s='Отправка СМС с платежными реквизитами' mod='ecm_smssender'}{/if}
</h3>
</center>
<input id="id_order" name="id_order" type="hidden" value ="{$id_order}"/>
<center>
{l s='Выберите номер телефона, на который будет отправлена СМС'}
    <table>
        <tbody>
            <tr>
                <td colspan=3>
                    <hr>
                </td>
            </tr>
            {foreach $phones as $phone}
            <tr>
                {if isset($phone.phone) && $phone.phone}
                <td width="10%" align="center">
                    <input name="phone" class="option_radio"
                    id = "sel_{$phone.phone|regex_replace:'/[^0-9]/':''}"
                    type="radio" value="{$phone.phone}" {$phone.phone}>
                </td>
                <td width="80%">
                    <input preset = "{$phone.phone|regex_replace:'/[^0-9]/':''}" value="{$phone.phone}">
                </td>
            </tr>
            <tr>
                <td colspan=3>
                    <hr>
                </td>
            </tr>
            {/if}
            {if isset($phone.phone_mobile) && $phone.phone_mobile}
            <tr>
                <td width="10%" align="center">
                    <input class="option_radio"
                    id = "sel_{$phone.phone_mobile|regex_replace:'/[^0-9]/':''}"
                    name="phone" type="radio" value="{$phone.phone_mobile}" {$phone.phone_mobile}>
                </td>
                <td width="80%">
                    <input preset = "{$phone.phone_mobile|regex_replace:'/[^0-9]/':''}" value="{$phone.phone_mobile}">
                </td>
            </tr>
            <tr>
                <td colspan=3>
                    <hr>
                </td>
            </tr>
            {/if}
            {/foreach}
        </tbody>
    </table>
</center>
<center>
    <div id="error_message"/>
</center>
<center>
    <button  id="ecm_send_sms" onClick="sendsms('{$id_order}','{$header}')"  class="button button-small btn btn-default" >
        <span>
            <i class="icon-save">
            </i>{l s='OK' mod='ecm_novaposhta'}
        </span>
    </button>
</center>
<script>
    $(document).ready(function () {
            $("input:text").change(function() {
                    if ($(this).change) {
                        $("#sel_"+$(this).attr("preset")).val($(this).val());
                    }
                });
        });
</script>
