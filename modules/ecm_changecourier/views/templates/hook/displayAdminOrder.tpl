<input id="moduledir" name="moduledir" type="hidden" value ="{$module_dir}"/>
<a onclick="changecarrier({$id_order})" class="button button-small btn btn-default" title="{l s='Change carrier' mod='ecm_novaposhta'}">
<span><i class="icon-truck "></i> <i class="icon-refresh"></i> {l s='Change carrier' mod='ecm_changecourier'} </span></a>
<br>&nbsp;
<br>&nbsp;

<script>
function changecarrier(id_order) {
	var url =$("#moduledir").val() + "refresh.php";
	if (!!$.prototype.fancybox){
		$.fancybox({
			title  : null,
			padding: 25,
			type   : 'ajax',
			href   : url+"?mode=changecarrier&id_order="+id_order,
			ajax   :{},
			helpers: {
				overlay: {
					locked: false
				}
			},
    		afterClose: function () {
                parent.location.reload();
            }
		});
	}
}

function changecarr(id_order, carrier) {
	var url = $("#moduledir").val() + "refresh.php";
	var data = {
		mode:"changecarr", 
		id_order:id_order, 
		carrier:carrier
	};
	$.ajax({
		data:data, 
		url:url
	});
}

</script>
