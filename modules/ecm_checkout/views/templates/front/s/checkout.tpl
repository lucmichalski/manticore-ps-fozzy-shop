{**
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* We are experts and professionals in PrestaShop
*
* @author    Elcommerce support@elcommece.com.ua
* @copyright 2010-2018 Elcommerce
* @license   Comercial
* @category  PrestaShop
* @category  Module
*}


<div class="card-block">
    <h5 class="h5">
        <i class="material-icons mi-checkout">
            checkout
        </i>{l s='Summary' mod='ecm_checkout'}
    </h5>
</div>
<hr class="separator">

<p class="alert alert-warning" id="discount_error" style="display: block;"></p>

<section class="card-block">

    {block name='cart_detailed_totals'}
    <div class="cart-detailed-totals">

        <div class="card-block">
			{$no_shipping = 0}
            {foreach from=$cart.subtotals item="subtotal"}
            {if $subtotal.value && $subtotal.type !== 'tax'}
            <div class="cart-summary-line" id="cart-subtotal-{$subtotal.type}">
                <span class="label{if 'products' === $subtotal.type} js-subtotal{/if}">
                    {if 'products' == $subtotal.type}{$cart.summary_string}{else}{$subtotal.label}{/if}
                </span>
                <span class="value">
					{if $subtotal.type === 'shipping'}
						{if isset($cost["'replace'"])}
							{if $replace != ''}
								{$price = $replace}
							{else}
								{$price = {l s='By carrier tariff' mod='ecm_checkout'}}
							{/if}
							{$no_shipping =+ $subtotal.amount}
						{else}
							{$price = $subtotal.value}
						{/if}
					{else}
						{$price = $subtotal.value}
					{/if}
					{$price}
                </span>
                {if $subtotal.type === 'shipping'}
                <div>
                    <small class="value">
                       {hook h='displayCheckoutSubtotalDetails' subtotal=$subtotal}
                    </small>
                </div>
                {/if}
            </div>
            {/if}
            {/foreach}
        </div>

        {if $cart.vouchers.allowed}
        <div class="block-promo">
            <div class="cart-voucher">
                {if $cart.vouchers.added}
                <ul class="promo-name card-block">
                    {foreach from=$cart.vouchers.added item=voucher}
                    <li class="cart-summary-line">
                        <span class="label">
                            {$voucher.name}
                        </span>
                        <a onclick = "action('delete_Discount,{$renderSeq}', '{$voucher.id_cart_rule}')" 
                  title="{l s='Delete' d='Shop.Theme.Actions'}" class="material-icons" style="cursor: pointer;">
                            &#xE872;
                        </a>
                        <div class="float-xs-right">
                            {$voucher.reduction_formatted}
                        </div>
                    </li>
                    {/foreach}
                </ul>
                {/if}

                <div class="promo-code" id="promo-code">
                    <input class="promo-input" type="text" id="discount_name" placeholder="{l s='Promo code' d='Shop.Theme.Checkout'}"/>
                    <button onclick = "add_Discount()" class="btn btn-primary">
                        <span>
                            {l s='Add' d='Shop.Theme.Actions'}
                        </span>
                    </button>
                    <div class="alert alert-danger js-error" role="alert">
                        <i class="material-icons">
                            &#xE001;
                        </i>
                        <span class="ml-1 js-error-text">
                        </span>
                    </div>
                </div>

                {if $cart.discounts|count > 0}
                <p class="block-promo promo-highlighted">
                    {l s='Take advantage of our exclusive offers:' d='Shop.Theme.Actions'}
                </p>
                <ul class="card-block promo-discounts">
                    {foreach from=$cart.discounts item=discount}
                    <li title="{l s='Add' d='Shop.Theme.Actions'}">
                        <span class="label" >
                            <span class="code voucher_name" data-code="{$discount.code|escape:'html':'UTF-8'}">
                                {$discount.code}
                            </span>- {$discount.name}
                        </span>
                    </li>
                    {/foreach}
                </ul>
                {/if}
            </div>
        </div>
        {/if}

        <div class="card-block">
            <div class="cart-summary-line cart-total">
                <span class="label">
                    {$cart.totals.total.label} {$cart.labels.tax_short}
                </span>
                <span class="value">
                    {if $no_shipping}
						{Tools::displayPrice($cart.totals.total.amount-$no_shipping)}
					{else}
					 {$cart.totals.total.value}
					{/if}
                </span>
            </div>

            <div class="cart-summary-line">
                <small class="label">
                    {$cart.subtotals.tax.label}
                </small>
                <small class="value">
                    {$cart.subtotals.tax.value}
                </small>
            </div>
            <hr>    
            <div id="delivery">
                <label for="delivery_message">
                    {l s='If you would like to add a comment about your order, please write it in the field below.' d='Shop.Theme.Checkout'}
                </label>
                <textarea rows="2"  id="delivery_message" act = "save_message"
            	name="delivery_message" class="checkout_ontype" 
            	style="width: 100%;" 
            	>{strip}{if isset($oldMessage)}{$oldMessage|escape:'html':'UTF-8'}{/if}{/strip}</textarea>
            </div>

            {if $call_me_check}
            <div class="float-xs-left">
                <span class="custom-checkbox">
                    <input class="_oncheck" type="checkbox" name="callme" id="callme" value="1"
                    {if $callme == 1}checked="checked"{/if}/>
                    <span>
                        <i class="material-icons rtl-no-flip checkbox-checked">
                            &#xE5CA;
                        </i>
                    </span>
                    <label class="js-terms"  for="callme">
                       {l s='Call me please.' mod='ecm_checkout'} 
                    </label>
                </span>
            </div>
            {/if}
          
            {if $recyclablePackAllowed}
            <div class="float-xs-left">
                <span class="custom-checkbox">
                    <input type="checkbox" id="recyclable" name="recyclable" value="1" {if $recyclable} checked {/if}
                    class="oncheck" act="save_cart"/>
                    <span>
                        <i class="material-icons rtl-no-flip checkbox-checked">
                            &#xE5CA;
                        </i>
                    </span>
                    <label for="recyclable">
                        {l s='I would like to receive my order in recycled packaging.' d='Shop.Theme.Checkout'}
                    </label>
                </span>
            </div>
            {/if}

            {if $gift.allowed}
            <div class="float-xs-left">
                <span class="custom-checkbox">
                    <input class="js-gift-checkbox oncheck" id="gift" name="gift" type="checkbox" 
                    act="save_cart,cart,checkout"
                    value="1" {if $gift.isGift}checked="checked"{/if}/>
                    <span>
                        <i class="material-icons rtl-no-flip checkbox-checked">
                            &#xE5CA;
                        </i>
                    </span>
                    <label for="gift">
                        {l s='I would like my order to be gift wrapped.' mod='ecm_checkout'}
                        {if $gift_wrapping_price > 0}
                        &nbsp;
                        <i>
                            ({l s='Additional cost of' mod='ecm_checkout'}
                            <span class="price" id="gift-price">
                                {$total_wrapping_cost}
                            </span>)
                        </i>
                        {/if}
                    </label>
                </span>
            </div>
            <div id="gift" class="collapse {if $gift.isGift}in{/if}">
                <label for="gift_message">
                    {l s="If you'd like, you can add a note to the gift:" d='Shop.Theme.Checkout'}
                </label>
                <textarea rows="2" id="gift_message" name="gift_message" class="checkout_ontype" 
                act = "save_cart" style="width: 100%;">{$gift.message}</textarea>
            </div>
            {/if}
        </div>
 
    </div>
    {/block}

    {block name='cart_detailed_actions'}
    <div class="checkout cart-detailed-actions card-block">
        {if $conditions_to_approve|count}
        <div class="order-options">
            <p class="ps-hidden-by-js">
                {l s='By confirming the order, you certify that you have read and agree with all of the conditions below:' d='Shop.Theme.Checkout'}
            </p>
            <form>
            <ul>
                {foreach from=$conditions_to_approve item="condition" key="condition_name"}
                <li>
                    <div class="float-xs-left">
                        <span class="custom-checkbox">
                            <input  id = "conditions_to_approve[{$condition_name}]"
                            name  = "conditions_to_approve[{$condition_name}]"
                            required
                            type  = "checkbox" value = "1"
                            class = "ps-shown-by-js {if $condition_name == 'terms-and-conditions'}oncheck{/if}" act="set_TOS,cart,checkout"
                            title = "{l s='Terms and conditions' mod='ecm_checkout'}"
                            {if $checkedTOS && $condition_name == "terms-and-conditions"}checked="checked"{/if}
                            />
                            <span>
                                <i class="material-icons rtl-no-flip checkbox-checked">
                                    &#xE5CA;
                                </i>
                            </span>
                            <label class="js-terms" for="conditions_to_approve[{$condition_name}]">
                                {$condition nofilter}
                            </label>
                        </span>
                    </div>
                </li>
                {/foreach}
            </ul>
            </form>
        </div>
        {/if}
        {if $cart.minimalPurchaseRequired}
        <div class="alert alert-warning" role="alert">
            {$cart.minimalPurchaseRequired}
        </div>
        <div class="text-sm-center">
            <button type="button" class="btn btn-primary disabled" disabled>
                {l s='Proceed to checkout' d='Shop.Theme.Actions'}
            </button>
        </div>
        {elseif empty($cart.products) }
        <div class="text-sm-center">
            <button type="button" class="btn btn-primary disabled" disabled>
                {l s='Proceed to checkout' d='Shop.Theme.Actions'}
            </button>
        </div>
        {else}
        <div class="text-sm-center">
            <a onclick = "to_checkout()" class="btn btn-primary">
                {l s='Proceed to checkout' d='Shop.Theme.Actions'}
            </a>
            {hook h='displayExpressCheckout'}
        </div>
        {/if}
    </div>
    {/block}
</section>

<script>

    var cart_qties = {$cart_qties};
    var timeoutId;
    var showerror = false;
    if ($('#gift').is(':checked'))
    $('#gift_div').show();
    else
    $('#gift_div').hide();


    $(".checkout_ontype").on("blur", function (){
            delay(this,0,false); //checkout
        })

    $(".oncheck").on("change", function (){
            action($(this).attr('act'), 'check_'+$(this).is(':checked'), $(this).attr('id'), false);
        })

	$('#callme').on ('change', function(){
		action('save_callme','callme',this.checked ? 1 : 0);
	})

    $(".voucher_name").on('click', function(e){
            action('add_Discount,'+renderSeq, $(this).data('code'));
        });

    function to_checkout(){
        var noerror = true;
        var showerror = false;
        
		$.each($('.sc_customer input'), function(index, value) {
			if(typeof($(value).attr('hidden')) != 'string'){
				if ($(value).prop('defaultValue') != $(value).val() &&  $(value)[0].checkValidity()){
					action($(value).attr('act'),$(value).val(),$(value).attr('id'), false);
				}
			}
		});

    
        $.each($('#ecm_checkout input:required'), function(index, value) {
			if($(value).hasClass('sc-error')){
				noerror = false;
				showerror = true;
			} else {
				if(typeof($(value).attr('unvisible')) != 'string') {
					if(!$(value)[0].checkValidity()){
						$(value).removeClass('sc-ok').addClass('sc-error');
						noerror = false;
						showerror = true;
					} 
				}
			}
			if (showerror){

		   
				$.growl.error({
						title: $(value).attr('title'),
						size: "medium",
						message: fill_error,
					});
				showerror = false;
			}
		});
    
        $.each($('.delivery'), function(index, value) {
			if($(value).val() == '0') {
				noerror = false;
				$(value).focus();
				$.growl.error({
						title: $('#'+$(value).attr('id')+' option:selected').text(),
						size: "medium",
						message: "",
					});
			}
		});
    
        if(authMethod == 2){
            if($('.email').val() == '' && $('.phone').val() == ''){
                $('#authMethod2').show();
                $('.phone').removeClass('sc-ok').addClass('sc-error');
                noerror = false;
            } else {
                $('#authMethod2').hide();
            }
        }
        
        if (noerror) {
            if (type_auth == 'registration') {
	            action('make_order',null,$('#password').val(),false);
	        } else {
	            action('make_order',null,null,false);
	        }
        }
    }
</script>