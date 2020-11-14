{*
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}


<div class="panel">
	<div class="panel-heading">
		<i class="icon-cogs"></i> {l s='Manual Push Notification' mod='wkpwa'}
	</div>
	<form class="defaultForm {$name_controller} form-horizontal" action="{if $edit}{$current}&update{$table}&id={$smarty.get.id}&token={$token}{else}{$current}&add{$table}&token={$token}{/if}" method="post" enctype="multipart/form-data">
		<div class="form-wrapper">
			<div class="form-group" id="customer-type-wrapper">
				<label class="control-label col-lg-3 required">
					{l s='Select target customer type' mod='wkpwa'}
				</label>
				<div class="col-lg-9">
					<select name="customer_type" class="fixed-width-xl" id="customer_type">
						{foreach from=$customerTypes key=idCustomerType item=customerType}
							<option value="{$idCustomerType}"
							{if isset($smarty.post.customer_type)}
								{if $idCustomerType == $smarty.post.customer_type}selected="selected"{/if}
							{elseif $edit}
								{if $idCustomerType == $notificationDetail['customer_type']}selected="selected"{/if}
							{/if}>{$customerType}</option>
						{/foreach}
					</select>
				</div>
			</div>

			<div class="form-group customer-type-option-fields" id="customer-type-group">
				<label class="col-lg-3 control-label required">
					<span>{l s='Select customer group' mod='wkpwa'}</span>
				</label>
				<div class="col-lg-3">
					<select name="customer_type_idGroup_value">
						{foreach from=$groups item=group}
							<option value="{$group['id_group']}"
							{if isset($smarty.post.customer_type) && isset($smarty.post.customer_type_idGroup_value)}
								{if $smarty.post.customer_type == $CUSTOMER_TYPE_GROUP}
									{if $smarty.post.customer_type_idGroup_value == $group['id_group']}selected="selected"{/if}
								{/if}
							{elseif $edit}
								{if $notificationDetail['customer_type'] == $CUSTOMER_TYPE_GROUP}
									{if $group['id_group'] == $notificationDetail['customer_type_value']}selected="selected"{/if}
								{/if}
							{/if}>{$group['name']}</option>
						{/foreach}
					</select>
				</div>
			</div>

			<div class="form-group customer-type-option-fields" id="customer-type-particular-customer">
				<label class="col-lg-3 control-label required">
					<span>{l s='Select customer' mod='wkpwa'}</span>
				</label>
				<div class="col-lg-3">
					<input type="text" name="customer_type_idCustomer_valueTxt" id="customer-suggestion-input" autocomplete="off" class="form-control"
					{if isset($smarty.post.customer_type_idCustomer_valueTxt)}
						value="{$smarty.post.customer_type_idCustomer_valueTxt}"
					{elseif $edit}
						{if $notificationDetail['customer_type'] == $CUSTOMER_TYPE_PARTICULAR_CUSTOMER}
							value="{$notificationDetail['customer_type_value_text']}"
						{/if}
					{/if}>
					<input type="hidden" name="customer_type_idCustomer_value" id="customer_type_idCustomer_value"
					{if isset($smarty.post.customer_type_idCustomer_value)}
						value="{$smarty.post.customer_type_idCustomer_value}"
					{elseif $edit}
						{if $notificationDetail['customer_type'] == $CUSTOMER_TYPE_PARTICULAR_CUSTOMER}
							value="{$notificationDetail['customer_type_value']}"
						{/if}
					{/if}>
					<ul id="wk_customer_suggestion_cont"></ul>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3 required">
					{l s='Title' mod='wkpwa'}
				</label>
				<div class="col-lg-9">
					<input type="text" name="title" id="title" class="form-control"
					{if isset($smarty.post.title)}
						value="{$smarty.post.title}"
					{elseif $edit}
						value="{$notificationDetail['title']}"
					{/if}>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3 required">
					{l s='Body' mod='wkpwa'}
				</label>
				<div class="col-lg-9">
					<input type="text" name="body" id="body" class="form-control"
					{if isset($smarty.post.body)}
						value="{$smarty.post.body}"
					{elseif $edit}
						value="{$notificationDetail['body']}"
					{/if}>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3 required">
					{l s='Target URL' mod='wkpwa'}
				</label>
				<div class="col-lg-9">
					<input type="text" name="target_url" id="target_url" class="form-control"
					{if isset($smarty.post.target_url)}
						value="{$smarty.post.target_url}"
					{elseif $edit}
						value="{$notificationDetail['target_url']}"
					{/if}>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='Icon' mod='wkpwa'}
				</label>
				<div class="col-lg-9">
					<div class="form-group">
						<div class="col-lg-12" id="icon-images-thumbnails">
							<div>
								<img src="{if $edit}{$notificationDetail['icon']}{else}{$psLogo}{/if}" style="max-width: 200px;" class="img-thumbnail">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-6">
							<input id="icon" type="file" name="icon" class="hide">
							<div class="dummyfile input-group">
								<span class="input-group-addon">
									<i class="icon-file"></i>
								</span>
								<input id="icon-name" type="text" name="icon" readonly="">
								<span class="input-group-btn">
									<button id="icon-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
										<i class="icon-folder-open"></i> {l s='Add file' mod='wkpwa'}
									</button>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group" id="schedule-push-switch-wrapper">
				<label class="control-label col-lg-3">
					{l s='Schedule Notification' mod='wkpwa'}
				</label>
				<div class="col-lg-8">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="schedule_push_switch" id="schedule_push_switch_on" value="1"
						{if isset($smarty.post.schedule_push_switch)}
							{if $smarty.post.schedule_push_switch}checked="checked"{/if}
						{elseif $edit}
							{if $notificationDetail['push_schedule'] != $DEFAULT_DATE_TIME}checked="checked"{/if}
						{/if}/>
						<label for="schedule_push_switch_on">{l s='Yes' mod='wkpwa'}</label>
						<input type="radio" name="schedule_push_switch" id="schedule_push_switch_off" value="0"
						{if isset($smarty.post.schedule_push_switch)}
							{if !$smarty.post.schedule_push_switch}checked="checked"{/if}
						{elseif $edit}
							{if $notificationDetail['push_schedule'] == $DEFAULT_DATE_TIME}checked="checked"{/if}
						{else}
							checked="checked"
						{/if}/>
						<label for="schedule_push_switch_off">{l s='No' mod='wkpwa'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>

			<div class="form-group" id="schedule-push-input-wrapper">
				<label class="control-label col-lg-3 required">
					{l s='Select date' mod='wkpwa'}
				</label>
				<div class="col-lg-3">
					<div class="input-group">
						<input type="text" class="wk_datepicker input-medium" name="push_schedule"
						value="{if isset($smarty.post.push_schedule)}{$smarty.post.push_schedule}{elseif $edit}{if $notificationDetail['push_schedule'] == $DEFAULT_DATE_TIME}{$defaultSchedulePushTime}{else}{$notificationDetail['push_schedule']}{/if}{else}{$defaultSchedulePushTime}{/if}" />
						<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
					</div>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminManualPushNotification')}" class="btn btn-default">
				<i class="process-icon-cancel"></i>{l s='Cancel' mod='wkpwa'}
			</a>
			<button type="submit" name="submitAdd{$table}" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save' mod='wkpwa'}
			</button>
			<button type="submit" name="submitAdd{$table}AndStay" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save and stay' mod='wkpwa'}
			</button>
		</div>
	</form>
</div>

{strip}
	{addJsDef pushNotificationContLink = $link->getAdminlink('AdminManualPushNotification')}
	{addJsDefL name=noResultFound}{l s='No result found' js=1 mod='wkpwa'}{/addJsDefL}
{/strip}
