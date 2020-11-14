<div class="dtd_container clear_cont">
<span class="step_number">2</span>
<h5 class="h5" style="padding: 1.5rem;">Вы выбрали тип доставки: {$carrir_name}.</h5>
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
<script>
{literal}
document.addEventListener('DOMContentLoaded', function(){
	add_ndc_event()
});    

if (typeof($)=='function'){
	$(document).ajaxComplete(function( event, xhr, settings) {
		add_ndc_event()
	})
}

function add_ndc_event() {
   show_payments();     
}
{/literal}  
</script>