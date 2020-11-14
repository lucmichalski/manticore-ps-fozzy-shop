<input id="phone" name="phone" type="hidden" value ="{$phone}"/>
<p>
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
<img src="/img/s/{$carrier.id_carrier}.jpg" alt="{$carrier.name}">
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
</p>
<p>
<center><button  onClick="parent.jQuery.fancybox.close();"  class="button button-small btn btn-default" >
<span> <i class="icon-save"></i> {l s='OK' mod='ecm_novaposhta'} </span>
</button>
</center>
</p>