{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="panel kpi-container">
	<div class="kpi-refresh"><button class="close refresh" type="button" onclick="refresh_kpis();"><i class="process-icon-refresh" style="font-size:1em"></i></button></div>
	<div class="row">
		{foreach from=$kpis item=i name=kpi}
			<div class="col-sm-6 col-lg-2">{$i}</div>
		{/foreach}
	</div>
</div>
<script>
/* Britoff - добавляем триггеры для проверки не закрытых заказов */
$( document ).ready(function() {

$( "#box-new-order span.value" ).css('cursor','pointer');
$( "#box-s-order span.value" ).css('cursor','pointer');
$( "#box-nd-order span.value" ).css('cursor','pointer');




$( "#box-new-order span.value" ).click(function() {
        $("select[name='orderFilter_os!id_order_state'] option[value=912]").prop('selected', true);
        $("#submitFilterButtonorder").trigger('click');
      });
$( "#box-s-order span.value" ).click(function() {
        $("select[name='orderFilter_os!id_order_state'] option[value=914]").prop('selected', true);
        $("#submitFilterButtonorder").trigger('click');
      });
$( "#box-nd-order span.value" ).click(function() {
        $("select[name='orderFilter_os!id_order_state'] option[value=935]").prop('selected', true);
        $("#submitFilterButtonorder").trigger('click');
      });
});


setInterval (blinke_funk, 150);
function blinke_funk() { 
	var blinke_speed = 150; //миллисекунды анимации

	$("#box-nd-order").fadeIn(blinke_speed).fadeOut(blinke_speed);
}

/* Britoff - добавляем триггеры для проверки не закрытых заказов */      
</script>
