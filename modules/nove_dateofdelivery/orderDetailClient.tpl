<!-- Nove Date of Delivery  -->
{if $cart_date} 
<div class="info-order box">
  <p><strong class="dark">{l s='Type of delivery:' mod='nove_dateofdelivery'}</strong> <span class="color-myaccount">{$car_del}</span></p>
  <p><strong class="dark">{l s='Date of delivery:' mod='nove_dateofdelivery'}</strong> <span class="color-myaccount">{$cart_date}</span></p>
  <p><strong class="dark">{l s='Period of delyvery:' mod='nove_dateofdelivery'}</strong> <span class="color-myaccount">{l s='From' mod='nove_dateofdelivery'}&nbsp;{$timefrom|date_format:"%H:%M"}&nbsp;{l s='to' mod='nove_dateofdelivery'}&nbsp;{$timeto|date_format:"%H:%M"}</span></p>
</div>
{/if}