{**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @author    Elcommerce <support@elcommece.com.ua>
 * @copyright 2010-2019 Elcommerce TM
 * @license   Comercial
 * @category  PrestaShop
 * @category  Module
*}
<div class="row">
	<div class="col-lg-12">
		<div id="fieldset_0" class="panel">
			<div class="panel-heading">
				<i class="icon-help"></i>{l s='Payment confirmation via Liqpay' mod='ecm_liqpay'}
			</div>
			
		<form action="{$link->getAdminLink('AdminLiqpayConfirmation')}" method="post">
		<fieldset>
        <p><b>{l s='You have a payment pending confirmation via Liqpay' mod='ecm_liqpay'}</b></p>
		<div class="alert alert-info">
		            {l s='You can:' mod='ecm_liqpay'}
					<ul style ="list-style: square outside;">
					<li>{l s='cancel payment for the full amount;' mod='ecm_liqpay'}</li>
					<li>{l s='confirm payment for the full amount;' mod='ecm_liqpay'}</li>
					<li>{l s='confirm the payment in part by entering the amount in the appropriate field below (but not more than the entire amount of the order). Returning the remaining amount can take up to 3 days' mod='ecm_liqpay'}</li>
					</ul>
		</div>
		<div class = "row">
			<div class = "col-md-2">
				<input name="ecmLiqpayhold_id_order" type="hidden" value="{$ecmLiqpayhold_id_order}">
				<input id="ecmLiqpayhold_paid" class="form-control" name="ecmLiqpayhold_paid" type="number" step="0.01" value="{$ecmLiqpayhold_paid}">
			</div>
			<div class = "col-md-9">
  				<input name="submitLiqpayRefund"  class="btn btn-danger"  type="submit" value="{l s='Refund' mod='ecm_liqpay'}" onclick="if(confirm('{l s='You definitely want to cancel the payment in the amount of ' mod='ecm_liqpay'}'+ $('#ecmLiqpayhold_paid').val()+ ' ?'))submit();else return false;"/>
		<!--		<input name="submitLiqpayHoldCompletion" class="btn btn-success"  type="submit" value="{l s='Completion' mod='ecm_liqpay'}" onclick="if(confirm('{l s='You confirm the payment for the amount ' mod='ecm_liqpay'}'+ $('#ecmLiqpayhold_paid').val()+ ' ?'))submit();else return false;"/>   -->
			</div>
		</div>
	</fieldset>
	</div>
</div>
{if isset($err) && $err}
<script>
{literal}
function error_modal(heading, msg,alert_type) {
	var errorModal =
		$('<div class="bootstrap modal hide fade">' +
			'<div class="modal-dialog">' +
			'<div class="modal-content alert alert-'+alert_type+' clearfix">' +
			'<div class="modal-header">' +
			'<a class="close" data-dismiss="modal" >&times;</a>' +
			'<h4>' + heading + '</h4>' +
			'</div>' +
			'<div class="modal-body">' +
			'<p><b>' + msg + '</b></p>' +
			'</div>' +
			'<div class="modal-footer">' +
			'<a href="#" id="error_modal_right_button" class="btn btn-default">' +
			'{/literal}{l s='Close' mod='ecm_liqpay'}{literal}' +
			'</a>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'</div>');
	errorModal.find('#error_modal_right_button').click(function () {
		errorModal.modal('hide');
	});
	errorModal.modal('show');
}
error_modal('{/literal}{l s='Error' mod='ecm_liqpay'}{literal}','{/literal}{$err}{literal}','{/literal}{$type}{literal}');{/literal}	
</script>
{/if}

