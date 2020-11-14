<div id="changepaymentpingbox" class="bootstrap" style="margin: 10px;">
<div id="errors" class="error" style="display: none"></div>
<h4>{l s='Change payment' mod='changeshipping'}</h4>
{$selector nofilter}
<h4>{l s='Payment date' mod='changeshipping'}</h4>
<input type="text" id="ddate" name="ddate" value="{$ddate}">
<h4>{l s='Change value' mod='changeshipping'}</h4>
<input type="text" id="paymentcostchangei" name="paymentcostchangei" value="{$order_payment_summ}">
<button type="submit" id="submitChangepayment" name="submitChangepayment" class="btn btn-primary" style="margin-top: 10px;">{l s='Change payment' mod='changeshipping'}</button>
<input id="basedir_payment" type="hidden" value="https://fozzyshop.ua/">
<input id="orderid" type="hidden" value="{$orderid}">
<input id="orderpid" type="hidden" value="{$orderpid}">
</div>
{literal}
<script type="text/javascript">
$(document).ready(function()
{

  $('#submitChangepayment').click(function(){
   var payment = $('#paymentslist').val();
   var payment_name = $( "#paymentslist option:selected" ).text();
   var basedirp = $('#basedir_payment').val();
   var orderid = $('#orderid').val();
   var orderpid = $('#orderpid').val();  
   var price = $('#paymentcostchangei').val();
   var ddate = $('#ddate').val();

    $.ajax({
			type: 'POST',
			url: basedirp + 'modules/changeshipping/ajax2.php',
			async: true,
			cache: false,
			dataType : "json",
			data: 'submitChangepayment=true' + '&payment=' + payment + '&payment_name=' + payment_name + '&orderid=' + orderid + '&orderpid=' + orderpid + '&price=' + price + '&ddate=' + ddate,
			success: function(jsonData)
			{
				if (jsonData == null)
				{
          $.fancybox.close();
					location.reload();
					return false;
				}

				if (jsonData.hasError)
				{
          var errors = '<b>'+'Ошибки: ' + '</b><ol>';
					for(error in jsonData.errors)
						if(error != 'indexOf')
							errors += '<li>'+jsonData.errors[error]+'</li>';						
						errors += '</ol>';
						$('#errors').html(errors).slideDown('slow');
				}
        else
        {
          location.reload();
        }
			},
		});
 //   location.reload();   
  });
});

</script>
{/literal}