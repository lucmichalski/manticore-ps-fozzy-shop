//ютф

function gettrueurl(){
	return $("#module_dir").val()+"/refresh.php";
}

function changecarrier(id_order) {
	var url = gettrueurl();
	var data = {mode: "changecarrier", id_order:id_order};
	if (!!$.prototype.fancybox){
		$.fancybox({
			title  : null,
			padding: 25,
			type   : 'ajax',
			href   : url+"?mode=changecarrier&id_order="+id_order,
			ajax   :{
			},
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

