<div class="dtd_container">
<span class="step_number">2</span>
<h5 class="h5"  style="padding: 1.5rem;">Вы выбрали способ доставки: {$carrir_name}.<br>Выберите, пожалуйста, период:</h5>
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
      {if $day.data == $tomorrow && $windowsclose == 1 && $pers.id_period == 73} disabled {/if}
      {if $day.data == $tomorrow && $windowsclose == 1 && $pers.id_period == 85} disabled {/if}
      {if $day.data == $tomorrow && $windowsclose == 1 && $pers.id_period == 97} disabled {/if}
      {if $day.data == $tomorrow && $windowsclose == 1 && $pers.id_period == 116} disabled {/if}
      {if $day.data == $tomorrow && $windowsclose == 1 && $pers.id_period == 72} disabled {/if}
      {if $day.data == $tomorrow && $windowsclose == 1 && $pers.id_period == 74} disabled {/if}
      {if $day.data == $tomorrow && $windowsclose == 1 && $pers.id_period == 86} disabled {/if}
      {if $day.data == $tomorrow && $windowsclose == 1 && $pers.id_period == 98} disabled {/if}
      {if $day.data == $tomorrow && $windowsclose == 1 && $pers.id_period == 117} disabled {/if}
      {if $day.data == $tomorrow && $windowsclose == 1 && $pers.id_period == 140} disabled {/if}
      {if $day.data == $tomorrow && $windowsclose == 1 && $pers.id_period == 141} disabled {/if}
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
  <div id="wrapper_counter_block">
      <p class="cart-grid-body alert alert-warning">{l s='Выбранное окно доставки сбросится через:' mod='nove_dateofdelivery'}
    		<span id="counter"></span></p>
  </div>
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
 var id_cart = {$id_cart};
 var cart_id_rule = {$id_cart};
document.addEventListener('DOMContentLoaded', function(){
	add_nd_event()
})    

if (typeof($)=='function'){
	$(document).ajaxComplete(function( event, xhr, settings) {
		add_nd_event()
	})
}  
function add_nd_event() {
{literal}
 $('#wrapper_counter_block #counter').timeTo({
    seconds: 600,
    fontSize: 14,
    callback: function(){ window.location.reload(); }
});
{/literal}

if (!$("input[name=dtd_radio]:checked").size()) hide_payments();
   $("input[name=dtd_radio]:radio").change(function () {
     var timeofdelivery= $(this).val();
      var destination = $('#customers').offset().top - 70;

      $('html').animate({ scrollTop: destination }, 1100); //1100 - скорость

     $.ajax({
            url:  window.location.protocol.toString() + "//" + window.location.host.toString() + "/modules/nove_dateofdelivery/ajax.php",
            type: "POST",
            data: {
              "timeofdelivery":timeofdelivery,
              "cart_id":id_cart
            },
            tryCount : 0,
            retryLimit : 10,
            beforeSend: function() {
                 $('#pleaseWaitDialog').show();
              },
            success: function(data){
              if (data == 1) {
               $('#pleaseWaitDialog').hide();
               show_payments();
              } else {
                this.tryCount++;
                  if (this.tryCount <= this.retryLimit) {
                      //try again
                      $.ajax(this);
                      return;
                      } else {
                        $('#pleaseWaitDialog').hide();
                        alert ('Невозможно оформить заказ, обновите страницу, пожалуйста');
                      }
              }
            }
        });
   });
}
</script>