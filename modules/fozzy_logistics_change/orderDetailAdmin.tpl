<div class="panel">
	<div class="panel-heading">
	 <i class="icon-truck"></i>
   <span id="logisticpanel" >{l s='Водители и сборщики:' mod='fozzy_logistics_change'}</span>
	</div>
 
    {if $can_change}
      <select name="logisticsbors" id="logisticsbors" class="form-control" style="text-transform: uppercase;">
      <option disabled selected>{l s='Выберите сборщика' mod='fozzy_logistics_change'}</option>
      {foreach from=$sborshiki item=ssborshik}
        <option value="{$ssborshik.id_sborshik}" {if $sborshik == $ssborshik.fio}selected{/if}>{if $sborshik == $ssborshik.fio}Сборщик - {/if}{$ssborshik.fio}</option>
      {/foreach}
      <option value="0">{l s='Удалить сборщика' mod='fozzy_logistics_change'}</option>
       </select>
     {if $super_change} 
      <select name="logisticvods" id="logisticvods" class="form-control" style="text-transform: uppercase;">
      <option disabled selected>{l s='Выберите водителя' mod='fozzy_logistics_change'}</option>
      {foreach from=$vodily item=svodila}
        <option value="{$svodila.id_vodila}" {if $vodila == $svodila.fio}selected{/if}>{if $vodila == $svodila.fio}Водитель - {/if}{$svodila.fio}</option>
      {/foreach}
      <option value="0">{l s='Удалить водителя' mod='fozzy_logistics_change'}</option>
       </select>
     {else}
     <input id="logisticvod"  type="text" value="{l s='Водитель' mod='fozzy_logistics_change'}&nbsp;-&nbsp;{$vodila}" disabled>
     {/if} 
    {else}
      <input id="logisticsbor" type="text" value="{l s='Сборщик' mod='fozzy_logistics_change'}&nbsp;-&nbsp;{$sborshik}" disabled>
      <input id="logisticvod"  type="text" value="{l s='Водитель' mod='fozzy_logistics_change'}&nbsp;-&nbsp;{$vodila}" disabled>
    {/if} 
</div>

