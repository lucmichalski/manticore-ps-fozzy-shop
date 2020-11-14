{*
* 2007-2019 ETS-Soft
*
* NOTICE OF LICENSE
*
* This file is not open source! Each license that you purchased is only available for 1 wesite only.
* If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
* You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
* 
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2019 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
<script type="text/javascript">
var sp_link_base ='{$sp_link_base|escape:'html':'UTF-8'}';
</script>
{literal}
<script type="text/javascript">
function renderDataAjax(jsonData)
{
    for (var key in jsonData) {
	    if(key=='java_script')
        {
            $('body').append(jsonData[key]);
        }
        else
            if($('#ets_speed_dy_'+key).length)
              $('#ets_speed_dy_'+key).replaceWith(jsonData[key]);  
    }
    if($('#header .shopping_cart').length && $('#header .cart_block').length)
    {
        var shopping_cart = new HoverWatcher('#header .shopping_cart');
        var cart_block = new HoverWatcher('#header .cart_block');
        $("#header .shopping_cart a:first").live("hover",
            function(){
    			if (ajaxCart.nb_total_products > 0 || parseInt($('.ajax_cart_quantity').html()) > 0)
    				$("#header .cart_block").stop(true, true).slideDown(450);
    		},
    		function(){
    			setTimeout(function(){
    				if (!shopping_cart.isHoveringOver() && !cart_block.isHoveringOver())
    					$("#header .cart_block").stop(true, true).slideUp(450);
    			}, 200);
    		}
        );
    }
    if(jsonData['custom_js'])
        $('head').append('<script src="'+sp_link_base+'/modules/ets_superspeed/views/js/script_custom.js"></javascript');
}
</script>
{/literal}
<style>
.layered_filter_ul .radio,.layered_filter_ul .checkbox {
    display: inline-block;
}
</style>