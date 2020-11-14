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

<table class="table">
	<thead>
		<tr>
			<th width="35"></th>
			{foreach $fields_list as $k => $title}
			<th>
				<span class="title_box">
					{$title|escape:'html':'UTF-8'}
					{if $k != 'img_legend'}
						<a href="#" class="icon-caret-up list-order-way{if $order_way == 'ASC' && $order_by == $k} active{/if}" data-way="ASC" data-by="{$k|escape:'html':'UTF-8'}"></a>
						<a href="#" class="icon-caret-down list-order-way{if $order_way == 'DESC' && $order_by == $k} active{/if}" data-way="DESC" data-by="{$k|escape:'html':'UTF-8'}"></a>
					{/if}
				</span>
			</th>
			{/foreach}
			<th width="85"></th>
		</tr>
		<tr class="filter">
			<th class="row-selector text-center">--</th>
			{foreach array_keys($fields_list) as $input_name}
			<th>
				{if $input_name != 'img_legend'}
				<input type="text" class="filter" data-filter-by="{$input_name|escape:'html':'UTF-8'}" value="{if !empty($filters.$input_name)}{$filters.$input_name|escape:'html':'UTF-8'}{/if}">
				{/if}
			</th>
			{/foreach}
			<th class="actions text-right">
				<button class="btn btn-default filter-items" title="{l s='Filter' mod='metatagsgenerator'}">
					<i class="icon-search"></i>
				</button>
				<button class="btn btn-default reset-filters" title="{l s='Reset filters' mod='metatagsgenerator'}">
					<i class="icon-eraser"></i>
				</button>
			</th>
		</tr>
	</thead>
	<tbody>
	{if $items|count}
		{foreach $items as $item}
		<tr data-id="{$item.id|intval}" class="item-row">
			<td class="row-selector text-center">
				<input type="checkbox" name="bulk-items[]" value="{$item.id|intval}" class="noborder">
				<span class="item-identifier">{$item.id|intval}</span>
			</td>
			{foreach $fields_list as $meta_name => $title}
				<td class="meta-value {$meta_name|escape:'html':'UTF-8'}">
					{if $meta_name == 'img_legend'}
						{if empty($item.img_legend)}
							<span class="grey-note">{l s='no images' mod='metatagsgenerator'}</span>
						{else}
							{if count($item.img_legend) > 1}
								<div class="img-navigation">
									<a href="#" class="icon-chevron-left img-nav pull-left" data-direction="prev"></a>
									<a href="#" class="icon-chevron-right img-nav pull-right" data-direction="next"></a>
								</div>
							{/if}
							<div class="img-list">
							{foreach $item.img_legend as $id_image => $legend}
								<div class="img-item">
									{$src = $this->getSrcById($id_image, $this->list_img.name)}
									<img class="dynamic-src" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="{$src|escape:'html':'UTF-8'}" width="{$this->list_img.width|intval}" height="{$this->list_img.height|intval}"></img>
									<div class="value-holder">
										<span class="counter">
											<span class="current"></span>/<span class="max grey-note"></span>
										</span>
										{$meta_name = 'img_legend_'|cat:$id_image}
										<div class="editable-value img-legend" data-meta="{$meta_name|escape:'html':'UTF-8'}" data-id="{$id_image|intval}" contenteditable>
											{$legend|escape:'html':'UTF-8'}
										</div>
									</div>
								</div>
							{/foreach}
							</div>
						{/if}
					{else}
						<div class="value-holder">
							<span class="counter">
								<span class="current"></span>/<span class="max grey-note"></span>
							</span>
							<div{if $meta_name != 'name'} class="editable-value" data-meta="{$meta_name|escape:'html':'UTF-8'}" contenteditable{/if}>
								{$item.$meta_name|escape:'html':'UTF-8'}
							</div>
						</div>
					{/if}
				</td>
			{/foreach}
			<td>
				<button class="btn btn-default pull-right generate-meta" title="{l s='Generate' mod='metatagsgenerator'}">
					<i class="icon-repeat"></i>
				</button>
			</td>
		</tr>
		{/foreach}
	{else}
		<tr>
			<td class="list-empty" colspan="{$fields_list|count + 2}">
				<div class="list-empty-msg">
					<i class="icon-warning-sign list-empty-icon"></i>
					{l s='No items found' mod='metatagsgenerator'}
				</div>
			</td>
		</tr>
	{/if}
	</tbody>
</table>
<div class="panel-footer">
	<div class="pull-left">
		<div class="checker">
			<a href="#" class="chk-action all" title="{l s='Check/Uncheck all' mod='metatagsgenerator'}">
				<i class="icon-check-sign"></i>
				{l s='Check all' mod='metatagsgenerator'}
			</a> |
			<a href="#" class="chk-action none" title="{l s='Uncheck all' mod='metatagsgenerator'}">
				<i class="icon-check-empty"></i>
				{l s='Uncheck all' mod='metatagsgenerator'}
			</a> |
			<a href="#" class="chk-action invert" title="{l s='Invert selection' mod='metatagsgenerator'}">
				<i class="icon-random"></i>
				{l s='Invert selection' mod='metatagsgenerator'}
			</a>
		</div>
		<button class="btn btn-default pull-left bulk-generate">
			<i class="icon-repeat"></i> {l s='Bulk generate' mod='metatagsgenerator'}
		</button>
	</div>
	<div class="pull-right">
		{include file="./pagination.tpl" npp=$npp p=$p total=$total}
	</div>
</div>
{* since 1.6.2 *}
