<div class="dtd_container">
<!-- <p>{l s='Period of delyvery' mod='nove_dateofdelivery'}:</p> -->
 {foreach from=$days_delivery item=day}
  <div class="dtd_radio-tile-group">
  <p class="dtd_day_title">{$day.dataU} - {$day.data|date_format:"%d.%m.%Y"}</p>
    {foreach from=$periods item=pers}
    <div class="dtd_input-container">
      <input id="walk_{$day.data}_{$pers.id_period}" class="dtd_radio-button" type="radio" name="dtd_radio" value="{$day.data}_{$pers.id_period}" {if $period == $pers.id_period && $cart_date == $day.data}checked="checked"{/if} {if $day.data == $today && $pers.timeoff < $pers.timenow}disabled{/if}
      {foreach from=$holidays item=holiday}
        {if ($holiday.date|date_format:"%d.%m.%Y" == $day.data && $holiday.period == 0) || ($holiday.date|date_format:"%d.%m.%Y" == $day.data && $holiday.period == $pers.id_period) } disabled {/if}
      {/foreach}
      {foreach from=$day.close_on item=closes}
      {if $closes.window == $pers.id_period && $closes.plan <= $closes.close} disabled {/if}  
      {/foreach}
      {if $day.data == $tomorrow && $windowsclose == 1 && $pers.id_period == 71} disabled {/if}
      {if $day.data == $tomorrow && $windowsclose == 1 && $pers.id_period == 72} disabled {/if}
      /> 
      <div class="dtd_radio-tile">
        <div class="dtd_icon dtd_walk-icon col-xs-3">
            <img class="dtd_logo dtd_img-responsive" src="/modules/nove_dateofdelivery/img/clock.png" alt="" width="13px">
        </div>
        <label for="walk" class="dtd_radio-tile-label col-xs-9">
        {l s='From' mod='nove_dateofdelivery'}&nbsp;{$pers.timefrom|date_format:"%H:%M"}&nbsp;{l s='to' mod='nove_dateofdelivery'}&nbsp;{$pers.timeto|date_format:"%H:%M"}{if $pers.express == 1}<br/>{l s='Within 3 hours of ordering' mod='nove_dateofdelivery'}{/if}
        </label>
      </div>
    </div>
    {/foreach}
  </div>
  {/foreach}
</div>
{if $period}
<style>
#container_float_review {
	display: block;
}
</style>
{else}
<style>
#container_float_review {
	display: none;
}
</style>
{/if}

<script type="text/javascript">
{literal}
function hide_payments() {
    $('#payments_section').hide();
    $('#onepagecheckoutps_step_three_container').hide();
    $('#container_float_review').hide();
  }
function show_payments() {
    $('#payments_section').show();
    $('#onepagecheckoutps_step_three_container').show();
    $('#container_float_review').show();
  }
{/literal}
  
$( document ).ready(function() {
   {literal}
   $('input.dtd_radio-button:disabled').each(function () {
       $(this).next().css({"background-color":"#fff","border":"2px solid #E91E63","color":"#9E9E9E"});
       $(this).remove();
    }); 
    {/literal}
   if (!$("input[name=dtd_radio]:checked").size()) hide_payments();
    $('#customer_no_call').prop('checked', true);
   $("input[name=dtd_radio]:radio").change(function () {
     var timeofdelivery= $(this).val();
     var id_cart = {$id_cart};

     $.ajax({
            url:  window.location.protocol.toString() + "//" + window.location.host.toString() + "/modules/nove_dateofdelivery/ajax.php",
            type: "POST",
            data: {
              "timeofdelivery":timeofdelivery,
              "cart_id":id_cart
            },
            success: function(data){
              show_payments();
            }        
        });    
     
   }); 
 
        
});  
</script>