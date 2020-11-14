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
		<i class="icon-cogs"></i> {l s='Order Status Notification' mod='wkpwa'}
	</div>
	<form class="defaultForm {$name_controller} form-horizontal" action="{if $edit}{$current}&update{$table}&id={$notificationDetail['id']}&token={$token}{else}{$current}&add{$table}&token={$token}{/if}" method="post" enctype="multipart/form-data">
		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='Enable Order Status Notification' mod='wkpwa'}
				</label>
				<div class="col-lg-8">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="active" id="active_on" value="1"
						{if isset($smarty.post.active)}
							{if $smarty.post.active}checked="checked"{/if}
						{elseif $edit}
							{if $notificationDetail['active'] != $DEFAULT_DATE_TIME}checked="checked"{/if}
						{/if}/>
						<label for="active_on">{l s='Yes' mod='wkpwa'}</label>
						<input type="radio" name="active" id="active_off" value="0"
						{if isset($smarty.post.active)}
							{if !$smarty.post.active}checked="checked"{/if}
						{elseif $edit}
							{if $notificationDetail['active'] == $DEFAULT_DATE_TIME}checked="checked"{/if}
						{else}
							checked="checked"
						{/if}/>
						<label for="active_off">{l s='No' mod='wkpwa'}</label>
						<a class="slide-button btn"></a>
					</span>
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

			<div class="form-group">
				<label class="control-label col-lg-3 required">
					{l s='Order status for sending notifications' mod='wkpwa'}
				</label>
				<div class="col-lg-3">
					<select name="order_status[]" class="input-large" multiple>
						{if $orderStatus|@count}
							{foreach from=$orderStatus item='status'}
								<option value="{$status.id_order_state|intval}"
								{if isset($smarty.post.order_status)}
									{if $smarty.post.order_status}
										{if in_array($status.id_order_state, $smarty.post.order_status)}selected="selected"{/if}
									{/if}
								{elseif $edit}
									{if $notificationDetail['order_status']}
										{if in_array($status.id_order_state, $notificationDetail['order_status'])}selected="selected"{/if}
									{/if}
								{/if}>&nbsp;{$status.name}</option>
							{/foreach}
						{/if}
					</select>
				</div>
			</div>

			<div class="form-group wk-available-tag-wrapper">
				<div class="col-sm-6 wk-available-tag-cont">
					<p class="wk-available-tag-heading">{l s='Tags' mod='wkpwa'}</p>
					<ul>
						<li class="wk-available-tag-list">
							<span class="wk-available-tag-span">{literal}{$order_reference}{/literal}</span> - {l s='Order Reference' mod='wkpwa'}
						</li>
						<li class="wk-available-tag-list">
							<span class="wk-available-tag-span">{literal}{$order_total}{/literal}</span> - {l s='Order Total (Tax Included)' mod='wkpwa'}
						</li>
						<li class="wk-available-tag-list">
							<span class="wk-available-tag-span">{literal}{$order_status}{/literal}</span> - {l s='Order Status' mod='wkpwa'}
						</li>
					</ul>
					<p class="wk-available-tag-info">{l s='The text used inside \'{ }\' with \'$\' symbol is a variable (example: {$product_name}). Do Not change these variables as they are representing their corresponding values. You can use these tags in Title and Body field' mod='wkpwa'}</p>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button type="submit" name="submitAdd{$table}" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save' mod='wkpwa'}
			</button>
		</div>
	</form>
</div>
