{if isset($order_details.id_carrier)}
{if {$order_details.id_carrier} == $np_id}
<!-- ecm_novaposta shipping module -->

<input id="x" name="x" type="hidden" value ="{$order_details.x}"/>
<input id="y" name="y" type="hidden" value ="{$order_details.y}"/>
<input id="ware" name="ware" type="hidden" value ="{$ware}"/>
<div>
<h5>{l s='Details of Nova Poshta delivery' mod='ecm_novaposhta'} 
{if $order_details.tracking_number}, {l s='declration' mod='ecm_novaposhta'}: <a href="{$followup|escape:'html':'UTF-8'}">{$order_details.tracking_number}{/if}</h4>

<table>

<tr>
<td>
{$order_details.firstname} <br>
{$order_details.lastname} <br>
{$order_details.phone} <br>
{$area} <br>
{$city} <br>
{$ware} <br>
{*$order_details.total_shipping*}<br>

</td>
{if Configuration::Get("PS_API_KEY")}
<td><div id="map_canvas" style="width: 280px; height: 280px;"></div></td>
{/if}
</tr>


{*
<tr>
<td>
<a onclick="checkpackage({$order_details.en},{$order_details.id_order})" class="button button-small btn btn-default pull-left" title="{l s='Track shipment' mod='ecm_novaposhta'}">
<span>{l s='Track shipment' mod='ecm_novaposhta'}</span></a>
</td>
<td><div id='result' name='result'>&nbsp;</div></td>
</tr>
*}
</table>
</div>


{if Configuration::Get("PS_API_KEY")}
<script>

$(document).ready(function() {
	$( ".adresses_bloc" ).css( "display", "none" );
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = 'https://maps.googleapis.com/maps/api/js?key={Configuration::Get("PS_API_KEY")}&v=3.exp&' + 'libraries=places&'+'callback=initialize';
    document.body.appendChild(script);
});


function initialize() {
	var myLatlng = new google.maps.LatLng($("#y").val(), $("#x").val());
	var myOptions = {
		zoom: 15,
		center: myLatlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	var marker = new google.maps.Marker({
		position: myLatlng,
		map: map,
		title: $("#ware").val()
	});
}
</script>
{/if}

<script>
function np_gettrueurl(){
	return $("#np_module_dir").val()+"/classes/refresh.php";
}

function checkpackage(deklaracia,order) {
	var url = np_gettrueurl();
	$("#result").empty();
	var data = {
		mode: "checkpackage",
		deklaracia: deklaracia,
		order:order
	}
	$.ajax({
		data:data, url:url,
		success: function(html) {
			$("#result").html(html);
		}
	});
}
</script>
<!-- /ecm_novaposta shipping module -->
{/if}{/if}