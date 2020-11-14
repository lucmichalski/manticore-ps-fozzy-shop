<!-- Nove Date of Delivery  -->
{if $cart_date} 
<li>{l s='Дата доставки:' mod='nove_dateofdelivery'} {$cart_date}</li>
<li>{l s='Время доставки:' mod='nove_dateofdelivery'} {l s='с' mod='nove_dateofdelivery'}&nbsp;{$timefrom|date_format:"%H:%M"}&nbsp;{l s='до' mod='nove_dateofdelivery'}&nbsp;{$timeto|date_format:"%H:%M"}</li> 
{/if}