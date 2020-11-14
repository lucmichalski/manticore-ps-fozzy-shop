<input id="id_order" name="id_order" type="hidden" value ="{$id_order}"/>
<table>
<tbody>
<tr><td colspan=3><hr></td></tr>
{foreach $carriers_list as $carrier}
<tr>
<td width="10%" align="center">
	<input id="carrier_{$carrier.id_carrier}" class="delivery_option_radio"
	onclick="changecarr('{$id_order}', '{$carrier.id_carrier}')"
	name="carrier" type="radio" value="{$carrier.id_carrier}" {$carrier.selected}>
</td>
<td width="30%" align="center">
<img class="carrier_logo" src="{$img_dir}s/{$carrier.id_carrier}.jpg" alt="{$carrier.name}">
</td>

<td width="60%">
	<strong>{$carrier.name}</strong>
	<br>Время доставки:&nbsp;{$carrier.delay}
</td>
</tr>
<tr><td colspan=3><hr></td></tr>
{/foreach}
</tbody>
</table>
<center><button  onClick="parent.jQuery.fancybox.close();"  class="button button-small btn btn-default" >
<span> <i class="icon-save"></i> {l s='OK' mod='ecm_novaposhta'} </span>
</button>
</center>

<style>
.carrier_logo {
    max-height: 48px;
    max-width: 48px;
}
</style>