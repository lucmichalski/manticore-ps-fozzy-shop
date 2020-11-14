<div class="panel">
	<div class="panel-heading">
	 <i class="icon-truck"></i>
   {if $can_change || $super_change}
   <span id="nvdeliverydatepanel" style="cursor: pointer">{l s='Дата доставки:' mod='nove_dateofdelivery_change'}</span>
   <span class="badge" id="nvdeliverydate">{$cart_date}</span>
	</div>
  <input id="nvdeliverydateinput" type="text" value="{$cart_date}" style="cursor: pointer" readonly>
  <p></p>   
  <select name="stimeofdelivery" id="stimeofdelivery" class="form-control" style="text-transform: uppercase;" disabled>
  <option value="0" disabled {if $period == 0}selected{/if}>{l s='Выберите период доставки:' mod='nove_dateofdelivery_change'}</option>
  {foreach from=$periods item=pers}
    <option value="{$pers.id_period}" {if $period == $pers.id_period}selected{/if}>{l s='С' mod='nove_dateofdelivery_change'}&nbsp;{$pers.timefrom|date_format:"%H:%M"}&nbsp;{l s='по' mod='nove_dateofdelivery_change'}&nbsp;{$pers.timeto|date_format:"%H:%M"}&nbsp;{$pers.carriers_name}</option>
  {/foreach}
   </select>
   {else}
   
   <span id="nvdeliverydatepanelreadonly">{l s='Дата доставки:' mod='nove_dateofdelivery_change'}</span>
   <span class="badge" id="nvdeliverydatereadonly">{$cart_date}</span>
   </div>
   <input type="text" readonly disabled value="Заказ размечен для отправки или у Вас нет прав на изменение даты.">
   <input type="text" readonly disabled value="Для изменения даты и времени обратитель в отдел логистики или к руководителю филиала.">
   {foreach from=$periods item=pers}
   {if $period == $pers.id_period}<input id="nvdeliverydatetime"  type="text" value="{l s='С' mod='nove_dateofdelivery_change'}&nbsp;{$pers.timefrom|date_format:"%H:%M"}&nbsp;{l s='по' mod='nove_dateofdelivery_change'}&nbsp;{$pers.timeto|date_format:"%H:%M"}&nbsp;{$pers.carriers_name}" disabled>{/if}
   {/foreach}
   {/if}
</div>

