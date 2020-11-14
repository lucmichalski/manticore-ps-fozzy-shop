{**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @author    Elcommerce <support@elcommece.com.ua>
 * @copyright 2010-2018 Elcommerce
 * @license   Comercial
 * @category  PrestaShop
 * @category  Module
*}

<div class="panel">
	<div class="panel-heading"><i class="icon icon-tags"></i> {l s='Settings' mod='ecm_checkout'}</div>
	<ul class="nav nav-tabs">
	    <li class=""><a data-toggle="pill" href="#settings_general">{l s='General' mod='ecm_checkout'} </a></li>
	    <li class="active"><a data-toggle="pill" href="#settings_carriers">{l s='Carriers' mod='ecm_checkout'} </a></li>
	    <li class=""><a data-toggle="pill" href="#settings_carrier">{l s='Carriers customization' mod='ecm_checkout'} </a></li>
	    <li class=""><a data-toggle="pill" href="#settings_payment">{l s='Payments customization' mod='ecm_checkout'} </a></li>
	    <li class=""><a data-toggle="pill" href="#settings_customs">{l s='Customs' mod='ecm_checkout'} </a></li>
	    <li class=""><a data-toggle="pill" href="#settings_about">{l s='About' mod='ecm_checkout'} </a></li>
	</ul>

	<div class="tab-content">
		<div id="settings_general" class="tab-pane fade">
			{$general}
		</div>
		<div id="settings_carriers" class="tab-pane fade panel in active">
			<div class="panel-heading"><i class="icon icon-globe"></i> {l s='Carrier settings' mod='ecm_checkout'}</div>
			{include './carriers.tpl'}
		</div>
		<div id="settings_carrier" class="tab-pane fade panel">
			<div class="panel-heading"><i class="icon icon-tags"></i> {l s='Carriers customization' mod='ecm_checkout'}</div>
			{$CustomCarrierForms}
		</div>
		<div id="settings_payment" class="tab-pane fade panel">
			<div class="panel-heading"><i class="icon icon-tags"></i> {l s='Payments customization' mod='ecm_checkout'}</div>
			{$CustomPaymentForms}
		</div>
		<div id="settings_customs" class="tab-pane fade panel">
			{include './custom.tpl'}
			<div class="panel-heading"><i class="icon icon-tags"></i> {l s='Documentation' mod='ecm_checkout'}</div>
			<div class="row">
			<p>
				&raquo; {l s='You can get a PDF documentation to configure this module' mod='ecm_checkout'} :
				<ul>
					<li><a href="#" target="_blank">{l s='English' mod='ecm_checkout'}</a></li>
					<li><a href="#" target="_blank">{l s='French' mod='ecm_checkout'}</a></li>
				</ul>
			</p>
			</div>
		</div>
		<div id="settings_about" class="tab-pane fade panel">
			{include './about.tpl'}
		</div>
	</div>
</div>






