{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Order history' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
  <h6>{l s='Here are the orders you\'ve placed since your account was created.' d='Shop.Theme.Customeraccount'}</h6>

  {if $orders}
    <table class="table table-striped table-bordered table-labeled hidden-lg-down">
      <thead class="thead-default">
        <tr>
          <th>{l s='Order reference' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Date' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Total price' d='Shop.Theme.Checkout'}</th>
          <th class="hidden-md-down">{l s='Payment' d='Shop.Theme.Checkout'}</th>
          <th class="hidden-md-down text-center">{l s='Status' d='Shop.Theme.Checkout'}</th>
          <th colspan="3" class="hidden-md-down text-center">{l s='Действие' d='Shop.Theme.Checkout'}</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$orders item=order}
          <tr>
            <th scope="row">{$order.details.id}</th>
            <td class="no-wrap-table">{$order.details.order_date}</td>
            <td class="text-xs-right">{$order.totals.total.value}</td>
            <td class="hidden-md-down">{$order.details.payment}</td>
            <td class="text-center">
              <span
                class="label label-pill {$order.history.current.contrast}"
                style="background-color:{$order.history.current.color}"
              >
                {$order.history.current.ostate_name}
              </span>
            </td>
            <td class="order-actions">
              <a href="{$order.details.details_url}" data-link-action="view-order-details">
                {l s='Details' d='Shop.Theme.Customeraccount'}
              </a>
            </td>
            <td class="order-actions">
              {if $order.details.reorder_url && $order['not_reorder'] == 0}
                <a href="{$order.details.reorder_url}">{l s='Reorder' d='Shop.Theme.Actions'}</a>
              {/if}
            </td>
            <td class="order-actions">
      			  {if $order['status_order_not_close'] == 912 || $order['status_order_not_close'] == 913 
              || $order['status_order_not_close'] == 15 || $order['status_order_not_close'] == 914
              || $order['status_order_not_close'] == 915 || $order['status_order_not_close'] == 911
              || $order['status_order_not_close'] == 16 || $order['status_order_not_close'] == 921
              || $order['status_order_not_close'] == 910 || $order['status_order_not_close'] == 1
              || $order['status_order_not_close'] == 927 || $order['status_order_not_close'] == 930
              || $order['status_order_not_close'] == 931 || $order['status_order_not_close'] == 932
              || $order['status_order_not_close'] == 935}
      			    <a href="#" data-toggle="modal" data-target="#modalOrderClose{$order.details.id}">
      			      {l s='Cancel the order' d='Shop.Theme.Customeraccount'}
      			    </a>
      			    <div id="modalOrderClose{$order.details.id}" class="modal" tabindex="-1" role="dialog">
      			      <div class="modal-dialog" role="document">
      			        <div class="modal-content">
      			          <div class="modal-header">
      			            <h1 class="modal-title">Отменить заказ</h1>
      			            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      			              <span aria-hidden="true">&times;</span>
      			            </button>
      			          </div>
      			          <div class="modal-body">
      			            <h5>Вы точно уверены в отмене заказа?</h5>
      			          </div>
      			          <div class="modal-footer">
      			            <button type="button" class="btn btn-primary" onclick="order_close({$order.details.id})">Да</button>
      			            <button type="button" class="btn btn-danger" data-dismiss="modal">Нет</button>
      			          </div>
      			        </div>
      			      </div>
      			    </div>
      			    <div id="modalOrderWarning" class="modal" tabindex="-1" role="dialog">
	                  <div class="modal-dialog" role="document">
	                      <div class="modal-content">
	                          <div class="modal-header">
	                              <h1 class="modal-title">Отмена заказа</h1>
	                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                                  <span aria-hidden="true">&times;</span>
	                              </button>
	                          </div>
	                          <div class="modal-body">
	                              <h5>Ваш заказ уже прошел через кассу, отмена на данном этапе возможна только через колл-центр!</h5>
	                          </div>
	                          <div class="modal-footer">
	                              <button type="button" class="btn btn-danger" data-dismiss="modal">Закрыть</button>
	                          </div>
	                      </div>
	                  </div>
	                </div>
      			  {/if}
      			</td>
          </tr>
        {/foreach}
      </tbody>
    </table>

    <div class="orders hidden-md-up">
      {foreach from=$orders item=order}
        <div class="order">
          <div class="row">
            <div class="col-12">
              <a href="{$order.details.details_url}"><h3>{$order.details.id}</h3></a>
              <div class="date">{$order.details.order_date}</div>
              <div class="total">{$order.totals.total.value}</div>
              <div class="status">
                <span
                  class="label label-pill {$order.history.current.contrast}"
                  style="background-color:{$order.history.current.color}"
                >
                  {$order.history.current.ostate_name}
                </span>
              </div>
              <div class="order-actions">
                <a class="hist_button" href="{$order.details.details_url}" data-link-action="view-order-details">
                  {l s='Details' d='Shop.Theme.Customeraccount'}
                </a>
              </div>
              <div class="order-actions">
                {if $order.details.reorder_url && $order['not_reorder'] == 0}
                  <a class="hist_button" href="{$order.details.reorder_url}">{l s='Reorder' d='Shop.Theme.Actions'}</a>
                {/if}
              </div>
              <div class="order-actions">
				      {if $order['status_order_not_close'] == 912 || $order['status_order_not_close'] == 913 
              || $order['status_order_not_close'] == 15 || $order['status_order_not_close'] == 914
              || $order['status_order_not_close'] == 915 || $order['status_order_not_close'] == 911
              || $order['status_order_not_close'] == 16 || $order['status_order_not_close'] == 921
              || $order['status_order_not_close'] == 910 || $order['status_order_not_close'] == 1
              || $order['status_order_not_close'] == 927 || $order['status_order_not_close'] == 930
              || $order['status_order_not_close'] == 931 || $order['status_order_not_close'] == 932
              || $order['status_order_not_close'] == 935}
				    <a href="#" class="hist_button" data-toggle="modal" data-target="#modalOrderCloseMobile{$order.details.id}">
				      {l s='Cancel the order' d='Shop.Theme.Customeraccount'}
				    </a>
				    <div id="modalOrderCloseMobile{$order.details.id}" class="modal" tabindex="-1" role="dialog">
				      <div class="modal-dialog" role="document">
				        <div class="modal-content">
				          <div class="modal-header">
				            <h1 class="modal-title">Отменить заказ</h1>
				            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				              <span aria-hidden="true">&times;</span>
				            </button>
				          </div>
				          <div class="modal-body">
				            <h5>Вы точно уверены в отмене заказа?</h5>
				          </div>
				          <div class="modal-footer">
				            <button type="button" class="btn btn-primary" onclick="order_close_mobile({$order.details.id})">Да</button>
				            <button type="button" class="btn btn-danger" data-dismiss="modal">Нет</button>
				          </div>
				        </div>
				      </div>
				    </div>
				    <div id="modalOrderWarningMobile" class="modal" tabindex="-1" role="dialog">
                      <div class="modal-dialog" role="document">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <h1 class="modal-title">Отменить заказ</h1>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                  </button>
                              </div>
                              <div class="modal-body">
                                  <h5>Ваш заказ уже прошел через кассу, отмена на данном этапе возможна только через колл-центр!</h5>
                              </div>
                              <div class="modal-footer">
                                  <button type="button" class="btn btn-danger" data-dismiss="modal">Закрыть</button>
                              </div>
                          </div>
                      </div>
                    </div>
				  {/if}
				</div>
            </div>
          </div>
        </div>
      {/foreach}
    </div>
<script>
  function order_close(order_id) {
    var url = document.location.href;

    $.ajax({
    type: "GET",
    url: url + "?id_order_close=" + order_id,
    cache: false,
    dataType: "json",
      success: function (response){
        if(response.success === false) {
            $('#modalOrderWarning').modal('show');
            $('#modalOrderClose' + order_id).modal('hide');
        } else {
            $('#modalOrderClose' + order_id).modal('hide');
            window.location.reload();
        }
      }
    });
  }

  function order_close_mobile(order_id) {
    var url = document.location.href;

    $.ajax({
    type: "GET",
    url: url + "?id_order_close=" + order_id,
    cache: false,
    dataType: "json",
      success: function (response){
          if(response.success === false) {
              $('#modalOrderWarningMobile').modal('show');
              $('#modalOrderCloseMobile' + order_id).modal('hide');
          } else {
              $('#modalOrderCloseMobile' + order_id).modal('hide');
              window.location.reload();
          }
      }
    });
  }
</script>

  {/if}
{/block}
