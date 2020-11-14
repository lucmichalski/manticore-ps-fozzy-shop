<input id="module_dir" name="module_dir" type="hidden" value ="{$module_dir}"/>
	<h4 class="just_h4">{l s='Details of JustIn delivery' mod='nove_justin'}</h4>
  <input id="cart_id" name="cart_id" type="hidden" value="{$cart_id}">
  <input id="cost" name="cost" type="hidden" value="{$cost}">
	<div class="delivery_option alternate_item justindiv">
		<table class="resume table table-bordered"><tr><td>
			<table class="table-select ">
				<tr>
					<td><label for="id_area_delivery" class="just_label">{l s='Area' mod='nove_justin'}</label></td>
					<td >
						<select name="id_area_delivery" id="id_area_delivery" 
						class="opc-input-sm input-sm opc-form-control form-control" onchange="just_refreshcity()">
							{html_options options=$Areas selected=$region}
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="id_city_delivery" class="just_label">{l s='City' mod='nove_justin'}</label></td>
					<td>
						<select name="id_city_delivery" id="id_city_delivery" disabled 
						class="opc-input-sm input-sm opc-form-control form-control" onchange="just_refreshware()">
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="id_ware_delivery" class="just_label">{l s='Postoffice' mod='nove_justin'}</label></td>
					<td>
						<select name="id_ware_delivery" id="id_ware_delivery"  disabled
						class="opc-input-sm input-sm opc-form-control form-control" onchange="just_saveform()">
						</select>
					</td>
				</tr>
			</table>
		</td></tr>

    </table>
	</div>
		
	<div id="refreshdelivery"></div>
  <script language="JavaScript" src="{$module_dir}/tm_just.js" type="text/javascript"></script>

<!-- /Change shipping module -->
