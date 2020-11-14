<div class="row">
	<div class="col-lg-4">
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-check"></i> {l s='Final statuses' mod='ecm_novaposhta'}
			</div>
		<select multiple  title="{l s='For multiple select use Ctrl-Click' mod='ecm_novaposhta'}"
		size="{count($status_list)}"  name="final_status" id="final_status" class="form-control form-control-select onchange">
			{html_options options=$status_list selected=$final_status}
		</select>	

		</div>
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-envelope"></i> {l s='Remember' mod='ecm_novaposhta'}
			</div>
		<label>{l s='In warehouse' mod='ecm_novaposhta'}</label>
		<select name="ware_status" id="ware_status" class="form-control form-control-select onchange">
			{html_options options=$status_list selected=$ware_status}
		</select>	
			
		<label>{l s='For warning' mod='ecm_novaposhta'}</label>
		<select name="warning_status" id="warning_status" class="form-control form-control-select onchange">
			{html_options options=$status_list selected=$warning_status}
		</select>	
		
		<label>{l s='Delay' mod='ecm_novaposhta'}</label>
		<input name="warning_day" id="warning_day" value="{$warning_day}" class="form-control ontyped"/>
			


		</div>
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-truck"></i> {l s='Carriers' mod='ecm_novaposhta'}
			</div>
		<select multiple title="{l s='For multiple select use Ctrl-Click' mod='ecm_novaposhta'}"
		size="{count($carriers_list)}"  name="carriers" id="carriers" class="form-control form-control-select onchange">
			{html_options options=$carriers_list selected=$carriers}
		</select>	
			


		</div>
	</div>
	
	<div class="col-lg-8">
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-eye-open"></i> {l s='Statuses' mod='ecm_novaposhta'}
			</div>
			
			<table class="table">
			<tr>
				<th></th>
				<th>{l s='Statuses NP' mod='ecm_novaposhta'}</th>
				<th>{l s='Shop status' mod='ecm_novaposhta'}</th>
			</tr>
			{foreach $np_statuses as $key=>$np_status}
				<tr>
					<td class="col-lg-1">{$key}</td>
					<td class="col-lg-7">{$np_status}</td>
					<td class="col-lg-4">
						<select name="status_for_{$key}" id="status_for_{$key}" data_key="{$key}" class="form-control form-control-select map_change">
							{if isset($status_map.$key)}{$selected=$status_map.$key}{else}{$selected=0}{/if}
							{html_options options=$status_list selected=$selected}
						</select>
					</td>
				</tr>
			{/foreach}

			</table>


		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-time"></i> {l s='Cron' mod='ecm_novaposhta'}
			</div>
			<p class="alert alert-info">
				{l s='Add this address to the cron ' mod='ecm_novaposhta'}: <br/>
				<a href="{$cron_url}" target="_blank>">{$cron_url}</a><br/>
				{l s='for process statuses on schedule.' mod='ecm_novaposhta'}</p>
			</p>
		</div>
	</div>
</div>

<script>
{literal}
$(".map_change").on("change", function (){
	map_change(this);
})
$(".onchange").on("change", function (){
	multi_change(this,$(this).attr('name'));
})

$(".ontyped").on("input", function (){
	multi_change(this,$(this).attr('name'));
})



function map_change(obj) {
    
    var data = {
        action: 'Refresh',
        ajax : true,
		command: 'map_change',
        np_status: $(obj).attr('data_key'),
        map_status: $(obj).val(),
    };
    $.ajax({
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(result) {
				if (result.status != 'failure') showSuccessMessage("Успешно обновлено");
				else showErrorMessage("Ошибка обновления !!!");
			},
	});
}

function multi_change(obj,command) {
    
    var data = {
        action: 'Refresh',
        ajax : true,
		command: command,
        values: $(obj).val(),
    };
    $.ajax({
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(result) {
				if (result.status != 'failure') showSuccessMessage("Успешно обновлено");
				else showErrorMessage("Ошибка обновления !!!");
			},
	});
}


{/literal}
</script>