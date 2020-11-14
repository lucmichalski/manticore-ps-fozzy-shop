{extends file='page.tpl'}




{block name='content'}

    <section id="content-hook_order_confirmation">
      <div class="row">
        <div class="col-sm-12 col-md-7 order-confirmation-title-payment">
          <h1 class="h1 page-title">
            <span><i class="fa fa-check rtl-no-flip" aria-hidden="true"></i> {l s='Your order is confirmed' d='Shop.Theme.Checkout'}</span>
          </h1>

          {block name='hook_order_confirmation_1'}
            {hook h='displayOrderConfirmation1'}
          {/block}

          {block name='order_details'}
            <div id="order-details">
              <h3 class="h3 card-title">{l s='Order details' d='Shop.Theme.Checkout'}:</h3>   
              <ul>
                {assign var="customerfio" value="{$customer.lastname}{' '}{$customer.firstname}"}
                <li>{l s='Order reference: %reference%' d='Shop.Theme.Checkout' sprintf=['%reference%' => $order.details.id]}</li>
                {foreach from=$customer.addresses item=address}
                {if $address.id == $order.id_address_delivery}<li>{l s='Вы указали доставку по адресу: %adr%' d='Shop.Theme.Checkout' sprintf=['%adr%' => $address.address1]}</li>{/if}
                {/foreach}
                {if !$order.details.is_virtual}
                  <li>
                    {l s='Shipping method: %method%' d='Shop.Theme.Checkout' sprintf=['%method%' => $order.carrier.name]}
                    <em class="text-muted">{$order.carrier.delay}</em>
                  </li>
                {/if}
                {block name='hook_order_confirmation'}
                  {$HOOK_ORDER_CONFIRMATION nofilter}
                {/block}
                <li>{l s='Payment method: %method%' d='Shop.Theme.Checkout' sprintf=['%method%' => $order.details.payment]}</li>
                <li>{l s='Вы указали контактные данные:' d='Shop.Theme.Checkout'}</li>
                <li>{l s='ФИО: %fio%' d='Shop.Theme.Checkout' sprintf=['%fio%' => $customerfio]}</li>
                {foreach from=$customer.addresses item=address}
                {if $address.id == $order.id_address_delivery}<li>{l s='Телефон: %mobile_ph%' d='Shop.Theme.Checkout' sprintf=['%mobile_ph%' => $address.phone_mobile]}</li>{/if}
                {/foreach}
                <li>{l s='Подтверждение отправлено на Ваш адрес: %email%' d='Shop.Theme.Checkout' sprintf=['%email%' => $customer.email]}</li>
              </ul>
            </div>
          {/block}

          {block name='hook_display_Thanks'}
            {hook h='displayThanks'}
          {/block}

        </div>
        <div class="col-sm-12 col-md-5 order-confirmation-details">

          {block name='order_confirmation_table'}
            {include
            file='checkout/_partials/order-confirmation-table-simple.tpl'
            products=$order.products
            subtotals=$order.subtotals
            totals=$order.totals
            labels=$order.labels
            add_product_link=false
            }
          {/block}


        </div>
      </div>
  </section>


    {block name='customer_registration_form'}
        {if $customer.is_guest}
            <div id="registration-form" class="card">
                <div class="card-body">
                    <h4 class="h4">{l s='Save time on your next order, sign up now' d='Shop.Theme.Checkout'}</h4>
                    {render file='customer/_partials/customer-form.tpl' ui=$register_form}
                </div>
            </div>
        {/if}
    {/block}

  {block name='hook_order_confirmation_2'}
    <section id="content-hook-order-confirmation-footer">
      {hook h='displayOrderConfirmation2'}
    </section>
  {/block}

{/block}


