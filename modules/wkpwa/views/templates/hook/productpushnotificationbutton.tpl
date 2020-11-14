{*
* 2010-2020 Webkul
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author Webkul IN <support@webkul.com>
*  @copyright  2010-2020 Webkul IN
*}

{if !$WK_PWA_PUSH_NOTIFICATION_ENABLE || !$isProductStateChanged || !$productPushNotificationEnabled}
    <div class="product-tab-content" id="product-tab-content-ModuleWkPwa">
    	<div class="product-tab-content">
            <div class="alert alert-danger">
                <button data-dismiss="alert" class="close" type="button">×</button>
                {if !$WK_PWA_PUSH_NOTIFICATION_ENABLE}
                    <span>{l s='Please allow push notification from' mod='wkpwa'} <a href="{$generalConfigLink}">{l s='General configuration' mod='wkpwa'}</a> {l s='for sending notification.' mod='wkpwa'}</span>
                {elseif !$isProductStateChanged}
                    <span>{l s='You must save this product for sending push notification.' mod='wkpwa'}</span>
                {elseif !$productPushNotificationEnabled}
                    <span>{l s='Enable' mod='wkpwa'} <a href="{$productNotifConfigLink}">{l s='Product push notiifaction' mod='wkpwa'}</a> {l s='to send notification.' mod='wkpwa'}</span>
                {/if}
            </div>
    	</div>
    </div>
{else}
    <div class="product-tab-content" id="product-tab-content-ModuleWkPwa">
        {$pushNotifiationProgress nofilter}
        <h3>{l s='Product Push Notification' mod='wkpwa'}</h3>
        <div class="row" style="margin-bottom:10px;">
            <label class="control-label col-lg-12">
                {l s='Send push notification of this product' mod='wkpwa'}
            </label>
            <div class="col-lg-12">
                <button type="button" class="btn btn-primary sendPushNotification" data-id-element="{$productId}" data-id-push-notification="{$idPushNotification}">
                    <i class="material-icons">flash_on</i>
                    <span>{l s='Push Notification' mod='wkpwa'}</span>
                </button>
            </div>
            <div style="clear:both;"></div>
        </div>
    </div>
{/if}
