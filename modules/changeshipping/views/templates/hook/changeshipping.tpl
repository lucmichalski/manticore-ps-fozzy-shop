<!-- Change shipping module -->
<input id="basedir" type="hidden" value="https://fozzyshop.ua/">
<input id="basedirpayment" type="hidden" value="https://fozzyshop.ua/">
<input id="orderid" type="hidden" value="{$id_order}">
{literal}
<script type="text/javascript">
  $(document).ready(function() {
      var button = '<button type="button" id="change_carrier_btn" name="change_carrier" class="btn btn-primary">{/literal}{l s='Change carrier' mod='changeshipping'}{literal}</button>';
      var button2 = '<button type="button" id="change_pmnt_btn" name="change_pmnt" class="btn btn-primary">{/literal}{l s='Change payment' mod='changeshipping'}{literal}</button>';
      var button_liq = '<button type="button" id="show_liq" name="show_liq" class="btn btn-primary">{/literal}{l s='Статус Liqpay' mod='changeshipping'}{literal}</button>';
      
      $(button).insertAfter($("#shipping hr"));
      $(button2).insertAfter($("#formAddPayment"));
      $(button_liq).insertAfter($("#change_pmnt_btn"));
      
$("#change_carrier_btn").click(function() {
var dirbase = $('#basedir').val();
var orderid = $('#orderid').val();
$.fancybox({
          		openEffect		: 'elastic',
          		closeEffect		: 'elastic',
          		autoSize : true,
          		type: 'ajax',
          		padding : 2,
          		helpers : {
                    		title : null,
                      	overlay : {
                              css : {
                                  'background' : 'rgba(0, 0, 0, 0.7)'
                              }
                          }
                        },
          		href : dirbase + 'modules/changeshipping/ajax.php?dirbase=' + dirbase + '&orderid=' + orderid,
          		ajax :  {
          		        type	: "GET"
          		        }
          });
});
$("#change_pmnt_btn").click(function() {
var dirbasep = $('#basedirpayment').val();
var dirbase = $('#basedir').val();
var orderid = $('#orderid').val();
$.fancybox({
          		openEffect		: 'elastic',
          		closeEffect		: 'elastic',
          		autoSize : true,
          		type: 'ajax',
          		padding : 2,
          		helpers : {
                    		title : null,
                      	overlay : {
                              css : {
                                  'background' : 'rgba(0, 0, 0, 0.7)'
                              }
                          }
                        },
          		href : dirbasep + 'modules/changeshipping/ajax2.php?dirbase=' + dirbase + '&orderid=' + orderid,
          		ajax :  {
          		        type	: "GET"
          		        }
          });
});
$("#show_liq").click(function() {
var orderid = $('#orderid').val();
var dirbasep = $('#basedirpayment').val();
$.fancybox({
          		openEffect		: 'elastic',
          		closeEffect		: 'elastic',
          		autoSize : true,
          		type: 'ajax',
          		padding : 2,
          		helpers : {
                    		title : null,
                      	overlay : {
                              css : {
                                  'background' : 'rgba(0, 0, 0, 0.7)'
                              }
                          }
                        },
          		href : dirbasep + 'modules/changeshipping/ajax_liqpay.php?orderid=' + orderid,
          		ajax :  {
          		        type	: "GET"
          		        }
          });
});

  });
</script>
{/literal}
<!-- /Change shipping module -->