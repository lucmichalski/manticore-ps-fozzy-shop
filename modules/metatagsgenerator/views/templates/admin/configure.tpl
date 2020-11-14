{*
* 2007-2017 Amazzing
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*
*  @author    Amazzing <mail@amazzing.ru>
*  @copyright 2007-2017 Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*}

<script type="text/javascript">
	var savedTxt = "{l s='Saved' mod='metatagsgenerator'}";
</script>
<div class="panel light-bg clearfix">
	<form class="form-horizontal auto-fill-meta-tags-form">
		<span>{l s='Autofill empty meta-tags on saving' mod='metatagsgenerator'}</span>
		{foreach $resources as $type => $r}
			<label><input type="checkbox" name="autofill[{$type|escape:'html':'UTF-8'}]" value="1" class="auto-fill-resource"{if !empty($autofill_data[$type])} checked{/if}> {$r.name|escape:'html':'UTF-8'}</label>
		{/foreach}
	</form>
</div>
<div class="panel clearfix">
	<form class="form-horizontal meta-form">
		<div class="form-group">
			<div class="col-lg-5">
				<div class="col-lg-6 has-additional-element">
					<select name="resource_type" class="resource-types list-trigger">
						{foreach $resources as $type => $r}
							{$variables = $this->implodePatternVariables($r.variables)}
							<option value="{$type|escape:'html':'UTF-8'}" data-variables="{$variables|escape:'html':'UTF-8'}">{$r.name|escape:'html':'UTF-8'}</option>
						{/foreach}
					</select>
					<select name="id_lang" class="languages list-trigger inline-block additional-element">
						{foreach $languages as $lang}
							<option value="{$lang.id_lang|intval}">{$lang.iso_code|escape:'html':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
				<label class="control-label col-lg-2">{l s='Sort by' mod='metatagsgenerator'}</label>
				<div class="col-lg-4 has-additional-element sort-by">
					<select name="order_by" class="order-by list-trigger">
						{foreach $sorting_options as $value => $display_name}
							<option value="{$value|escape:'html':'UTF-8'}"{if $value == 'main.date_add'} selected{/if}>{$display_name|escape:'html':'UTF-8'}</option>
						{/foreach}
					</select>
					{$order_way = 'ASC'}
					<div class="order-way additional-element">
						<label class="way list-trigger">
							<i class="icon-caret-up"></i><input type="radio" name="order_way" value="ASC">
						</label>
						<label class="way list-trigger active">
							<i class="icon-caret-down"></i><input type="radio" name="order_way" value="DESC" checked>
						</label>
					</div>
				</div>
			</div>
			<div class="col-lg-7 additional-filters">
				<label class="control-label col-lg-2">{l s='Filter by' mod='metatagsgenerator'}</label>
				{foreach $special_filters as $type => $filters}
					{foreach $filters as $k => $f}
						<div class="col-lg-{if $k == 'id_category'}4{else}3{/if} special-option {$type|escape:'html':'UTF-8'}-option">
							<select name="{$k|escape:'html':'UTF-8'}" class="list-trigger additional-filter">
								<option value="-" class="grey-note">{$f.name|escape:'html':'UTF-8'}</option>
								{if !empty($f.options)}
								{foreach $f.options as $input_name => $display_name}
									<option value="{$input_name|intval}">{$display_name|escape:'html':'UTF-8'}</option>
								{/foreach}
								{/if}
							</select>
						</div>
					{/foreach}
				{/foreach}
				<div class="col-lg-3">
					<select name="active" class="list-trigger additional-filter">
						<option value="-" class="grey-note">{l s='Status' mod='metatagsgenerator'}</option>
						<option value="1">{l s='Active' mod='metatagsgenerator'}</option>
						<option value="0">{l s='Not active' mod='metatagsgenerator'}</option>
					</select>
				</div>
				<a href="#" class="clear-filters"><i class="icon-times"></i></a>
			</div>
		</div>
		<div class="meta-types">
			<div class="available-variables">
				{l s='Available variables' mod='metatagsgenerator'}: <span></span>
			</div>
			{foreach $meta_types as $type => $meta}
			<div class="form-group meta-type{if !empty($meta.class)} {$meta.class|escape:'html':'UTF-8'}{/if}">
				<label class="control-label col-lg-2">
					{$meta.name|escape:'html':'UTF-8'}
					<input type="checkbox" name="patterns[{$type|escape:'html':'UTF-8'}][active]" value="1" class="meta-checkbox" checked>
				</label>
				<div class="meta-details col-lg-10">
					<div class="inline-block pattern-input">
						<input type="text" name="patterns[{$type|escape:'html':'UTF-8'}][value]" value="" class="pattern">
					</div>
					{if !empty($meta.length)}
						<span class="control-label">{l s='Max chars' mod='metatagsgenerator'}</span>
						<div class="inline-block truncate-input">
							<input type="text" name="patterns[{$type|escape:'html':'UTF-8'}][length]" value="{$meta.length|intval}" class="length">
						</div>
						<span class="grey-note">{l s='Recommended: %d' mod='metatagsgenerator' sprintf=$meta.length}</span>
					{/if}
				</div>
			</div>
			{/foreach}
		</div>
		<div class="form-group additional-options">
			<label class="control-label col-lg-2">{l s='Override existing fields' mod='metatagsgenerator'}</label>
			<div class=" col-lg-2">
				<span class="switch prestashop-switch">
					<input type="radio" id="overwrite_fields" name="overwrite_fields" value="1">
					<label for="overwrite_fields">{l s='Yes' mod='metatagsgenerator'}</label>
					<input type="radio" id="overwrite_fields_0" name="overwrite_fields" value="0" checked>
					<label for="overwrite_fields_0">{l s='No' mod='metatagsgenerator'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
	</form>
	{if $shops_num > 1}
		<div class="alert alert-warning">
			{l s='NOTE: Meta fields will be processed for more than one shop' mod='metatagsgenerator'}
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
	{/if}
	<div class="col-lg-12 resource-list">
	</div>
</div>

{* this button is dynamically moved to top *}
<a id="module-documentation" class="toolbar_btn hidden" href="{$documentation_link|escape:'html':'UTF-8'}" target="_blank" title="{l s='Documentation' mod='metatagsgenerator'}">
	<i class="process-icon-t icon-file-text"></i>
	<div>{l s='Documentation' mod='metatagsgenerator'}</div>
</a>
{* since 1.6.2 *}
