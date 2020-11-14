{if isset($show_form) && $show_form ==1}
<div class="panel">
    <h3>
        <i class="icon icon-credit-card">
        </i>{l s='SMS sender' mod='ecm_smssender'}
    </h3>
    <p>
        <div class= "display_status">
            <div class="form-group" style = "clear: both;">
                <label class="control-label col-lg-2" style = "clear: both;">
                    {l s='Choose status' mod='ecm_smssender'}
                </label>
                <div class="col-lg-6">
                    <div style="width: 270px" >
                        {html_options class=status_pay name=status_pay_selected  options=$statuses selected=$status_pay_selected}
                    </div>
                    <p class="help-block">
                        {l s='Select the status of the order at which an SMS about successful payment will be sent.' mod='ecm_smssender'}
                    </p>
                </div>
            </div>
        </div>
    </p>
    <br />
    <p>
    <div class= "display_status_alert">
            <div class="form-group" style = "clear: both;">
                <label class="control-label col-lg-2" style = "clear: both;">
                    {l s='Choose status' mod='ecm_smssender'}
                </label>
                <div class="col-lg-6">
                    <div style="width: 270px" >
                        {html_options class=status_alert name=status_alert_selected  options=$statuses selected=$status_alert_selected}
                    </div>
                    <p class="help-block">
                        {l s='Select the order status at which an SMS will be sent stating that the parcel has been stored in the carrier’s warehouse for too long.' mod='ecm_smssender'}
                    </p>
                </div>
            </div>
        </div>
    </p>
    <br />
    <p>
    <div class= "display_status_alert">
            <div class="form-group" style = "clear: both;">
                <label class="control-label col-lg-2" style = "clear: both;">
                    {l s='Choose status' mod='ecm_smssender'}
                </label>
                <div class="col-lg-6">
                    <div style="width: 270px" >
                        {html_options class=status_en name=status_en  options=$statuses selected=$status_en_selected}
                    </div>
                    <p class="help-block">
                        {l s='Select the order status at which an SMS with the carriers TTN number will be sent.' mod='ecm_smssender'}
                    </p>
                </div>
            </div>
        </div>
    </p>
    <br />
    <p>
        <div class= "display_status_alert">
            <div class="form-group" style = "clear: both;">
                <label class="control-label col-lg-2" style = "clear: both;">
                {l s='Choose status' mod='ecm_smssender'}
                </label>
                <div class="col-lg-6">
                    <div style="width: 270px" >
                        {html_options class=status_closed name=status_closed  options=$statuses selected=$status_closed_selected}
                    </div>
                    <p class="help-block">
                    {l s='Select the order status at which an SMS will be sent with a link to the store’s performance evaluation page.' mod='ecm_smssender'}
                    </p>
                </div>
            </div>
        </div>
    </p>
    <br />
    <p>
        <div class= "display_status_alert">
            <div class="form-group" style = "clear: both;">
                <label class="control-label col-lg-2" style = "clear: both;">
                    {l s='Choose status' mod='ecm_smssender'}
                </label>
                <div class="col-lg-6">
                    <div style="width: 270px" >
                        {html_options class=status_noname1 name=status_noname1  options=$statuses selected=$status_noname1_selected}
                    </div>
                    <p class="help-block">
                        {l s='Select the order status at which an SMS will be sent from additional field №1' mod='ecm_smssender'}
                    </p>
                </div>
             </div>
        </div>
    </p>
    <br />
    <p>
        <div class= "display_status_alert">
            <div class="form-group" style = "clear: both;">
                <label class="control-label col-lg-2" style = "clear: both;">
                    {l s='Choose status' mod='ecm_smssender'}
                </label>
                <div class="col-lg-6">
                    <div style="width: 270px" >
                        {html_options class=status_noname2 name=status_noname2  options=$statuses selected=$status_noname2_selected}
                    </div>
                    <p class="help-block">
                        {l s='Select the order status at which an SMS will be sent from additional field №2' mod='ecm_smssender'}
                    </p>
                </div>
            </div>
        </div>
    </p>

    <p>
        <br />
    </p>
    <p>
        <br />
    </p>
</div>
{/if}
<div class="panel">
    <h3>
        <i class="icon icon-tags">
        </i>{l s='Template variables' mod='ecm_smssender'}
    </h3>
    <p>
        &raquo; {l s='Variables, when you can use in SMS' mod='ecm_smssender'} :
        <ul>
            <li>
                {l s='{id_order} - current Id  order' mod='ecm_smssender'}
            </li>
            <li>
                {l s='{reference_order} - current reference order' mod='ecm_smssender'}
            </li>
            <li>
                {l s='{total_paid} - total paid for current order' mod='ecm_smssender'}
            </li>
            <li>
                {l s='{total_shipping} - total shipping for current order' mod='ecm_smssender'}
            </li>
            <li>
                {l s='{track_shipping} - track number for current order' mod='ecm_smssender'}
            </li>
            <li>
                {l s='{carrier_name} - carrier for current order' mod='ecm_smssender'}
            </li>
            <li>
                {l s='{delivery_address} - delivery address' mod='ecm_smssender'}
            </li>
            <li>
                {l s='{firstname} - customer firstname' mod='ecm_smssender'}
            </li>
            <li>
                {l s='{lastname} - customer lastname' mod='ecm_smssender'}
            </li>
        </ul>
    </p>
</div>
{addJsDef module_name=$module_name}
<script>
var ajaxFormurl;

ajaxFormurl = '{$module_path}'+"/ajax/ajaxForm.php";
function ajax(data){
    return  $.ajax({
            type: "POST",
            url: ajaxFormurl,
            data: JSON.stringify(data, null, 2),
            success: function(message) {
                if(message == 'success') {
                    showSuccessMessage("Успешно обновлено");
                }  else {
                    showErrorMessage("Ошибка при обновлении статуса");
                }
            },
        });
}
$(document).ready(function (){
				$("select").on("change", function()  {
                var data = {
                    val_name:$(this).attr("class"),
                    val_flag         :$(this).val()
                };
                 ajax(data);
            });
    });
</script>
