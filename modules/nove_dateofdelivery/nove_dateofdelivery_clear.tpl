<div class="dtd_container clear_cont">
<h5>{l s='Условия доставки' mod='nove_dateofdelivery'}:</h5>
<p>{l s='Уважаемые клиенты!' mod='nove_dateofdelivery'} <br> {l s='Заказы, размещенные до 16-00 будут отправлены завтра' mod='nove_dateofdelivery'}, {$tomorrow}.<br>{l s='Заказы, размещенные после 16-00, будут отправлены' mod='nove_dateofdelivery'} {$tomorrow1}. {l s='Спасибо за заказ' mod='nove_dateofdelivery'}.</p>
<h5>{l s='ВАЖНО!!!' mod='nove_dateofdelivery'}</h5>
<ul>
<li>{l s='мы не отправляем курьерскими службами товар' mod='nove_dateofdelivery'}:
<ul>
<li>{l s='требующий специального температурного режима в процессе перевозки' mod='nove_dateofdelivery'};</li>
<li>{l s='фрукты, овощи, ягоды' mod='nove_dateofdelivery'}.</li>
</ul>
</li>
<li>{l s='в Интернет магазине Вы оплачиваете только стоимость товаров' mod='nove_dateofdelivery'};</li>
<li>{l s='доставку оплачивает получатель при получении товара' mod='nove_dateofdelivery'};</li>
<li>{l s='стоимость доставки согласно тарифов курьерской службы' mod='nove_dateofdelivery'}.</li>
</ul>
</div>
<style>
#container_float_review {
	display: block;
}
</style>
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
   show_payments();     
});  
</script>