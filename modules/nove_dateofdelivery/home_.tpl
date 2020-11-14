<div class="dtd_container">
 <h2 style="color: white;text-align: center;margin: 12px 0;">{l s='Возможное время доставки курьером' mod='nove_dateofdelivery'}:</h2>
 {foreach from=$days_delivery item=day name=foo}
 {if $smarty.foreach.foo.iteration < 4}
  <div class="dtd_radio-tile-group  col col-xs-12 col-md-4 {if $smarty.foreach.foo.iteration == 2}rl_bord{/if}">
  <h5 style="color: white;text-align: center;margin: 12px 0;width: 100%;">{$day.dataU}:</h5>
    {foreach from=$periods item=pers }
    <div class="dtd_input-container">
      <input id="walk_{$day.data}_{$pers.id_period}" class="dtd_radio-button" type="radio" name="dtd_radio" value="{$day.data}_{$pers.id_period}"  {if $day.data == $today && $pers.timeoff < $pers.timenow}disabled{/if}
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
  {/if}
  {/foreach}
</div>
