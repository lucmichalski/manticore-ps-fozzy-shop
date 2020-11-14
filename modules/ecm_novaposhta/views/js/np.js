//ютф
$(document).ajaxComplete(function( event, xhr, settings) {
	
	if (!$("#another_recipient").prop("checked")) {
		$(".for_collapse_7").hide();
		$(".another_recipient").hide();
		$(".another").prop('required',false);
	} else {
		$(".for_collapse_7").show();
		$(".another_recipient").show();
		$(".another").prop('required',true);
	}

	if ($("#id_ware_delivery").val() != 1) {
		$(".np_address_delivery").hide();
		$("#StreetName").removeAttr('required');
	}else {
		$(".np_address_delivery").show();
		$("#StreetName").prop('required',true);
	}

	np_fill_address();	
	add_events();
	showhide();
});
 
function showhide(){
	if (   ecm_novaposhta.id == $("#carrier").val() 
		|| ecm_novaposhta.id+',' == $('input:radio[class=delivery_option_radio]:checked').val()
		|| ecm_novaposhta.id+',' == $('.delivery_option_radio:checked').val()
		|| ecm_novaposhta.id+',' == $('input[name^=delivery_option]:checked').val()
		|| '1'+ecm_novaposhta.id+'00' == $('input[name^=id_carrier]:checked').val()
		){
			$('.for_hide').hide();
		} else {
			$('.for_hide').hide();
		}
}

function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
};


$(document).ready(function() {
	
	if($("#md_page").val() == 'order') {
		var destination = $('#delivery_panel').offset().top - 150;
		if ($.browser.webkit) {
//				$('body').animate({ scrollTop: destination}, 1500); //1100 - скорость
	        } else {
//				$('html').animate({ scrollTop: destination}, 1500);
	        }
		if(!$('#md_firstname')[0].checkValidity()) showErrorMessage("Ім`я - Недопустимі символи !!!")
		if(!$('#md_lastname')[0].checkValidity())  showErrorMessage("Прізвище - Недопустимі символи !!!")
		//if(!$('#md_middlename')[0].checkValidity())  showErrorMessage("По батькові - Недопустимі символи !!!")
		if(!$('#md_phone')[0].checkValidity())  showErrorMessage("Помилка в номері телефону !!!")
		if ($("#another_recipient").prop("checked")) {	
			if(!$('#another_firstname')[0].checkValidity()) showErrorMessage("Ім`я - Недопустимі символи !!!")
			if(!$('#another_lastname')[0].checkValidity())  showErrorMessage("Прізвище - Недопустимі символи !!!")
			//if(!$('#another_middlename')[0].checkValidity())  showErrorMessage("По батькові - Недопустимі символи !!!")
			if(!$('#another_phone')[0].checkValidity())  showErrorMessage("Помилка в номері телефону !!!")
		}
	}



	if (!$("#another_recipient").prop("checked")) {$(".another_recipient").hide();}
	
	if($("#id_ware_delivery").val() == 1){$("#sender_address").show();}else{$("#sender_address").hide();} //adminka

	if ($("#id_ware_delivery").val() != 1) {
		$(".np_address_delivery").hide();
		$("#StreetName").removeAttr('required');
	}else {
		$(".np_address_delivery").show();
		$("#StreetName").prop('required',true);
	}

	np_fill_address();
	add_events();
	showhide();	
});

function add_events(){
	
	$('#np_address_delivery').off('change')
	$('#np_address_delivery').on('change', function() {
		if($('#id_city_delivery :selected').val()=="0") return; 
		if($(this).prop("checked")) {
			$('#id_ware_delivery').val(1).change();
			$(".np_address_delivery").show();
			$("#StreetName").prop('required',true);
		} else {
			$('#id_ware_delivery').val(0).change();
			$(".np_address_delivery").hide();
			$("#StreetName").removeAttr('required');
		}
	});
	
	$(".np_ontyped").off('blur')
	$(".np_ontyped").on("blur", function (){
		if(this.value){
			if ($(this)[0].checkValidity()){
				$(this).removeClass('sc-error').addClass('sc-ok');
				this.defaultValue = this.value;
				saveform(0);
			} else {
				$(this).removeClass('sc-ok').addClass('sc-error');
				return;
			}
		}
	})

	// ps 1.6
	$(document).off('click', '.payment_module > a')	
	$(document).on('click', '.payment_module > a', function (npy){	
		if ($('input[name^=delivery_option]:checked').val() == ecm_novaposhta.id+',' && !np_isvalid()){
			event.stopPropagation();
			event.preventDefault();
			return false;
		}
	});

	// ps 1.7
	$('button[name=confirmDeliveryOption]').off('click')
	$('button[name=confirmDeliveryOption]').on("click", function (nps){
		if ($('input[name^=delivery_option]:checked').val() == ecm_novaposhta.id+',' && !np_isvalid()){
			event.stopPropagation();
			event.preventDefault();
			return false;
		}
	});

	$(".paymentallowed").off("click")
	$(".paymentallowed").on("click", function (npt){
		if ($('input[name^=delivery_option]:checked').val() == ecm_novaposhta.id+',' && !np_isvalid()){
			event.stopPropagation();
			event.preventDefault();
			return false;
		}
	});

	// zelarg 1.6
	$(document).off('click', '.confirm_button')	
	$(document).on('click', '.confirm_button', function (npw){	
		if ($('input[name^=id_carrier]:checked').val() == '1'+ecm_novaposhta.id+'00' && !np_isvalid()){
			event.stopPropagation();
			event.preventDefault();
			return false;
		}
	});
	
	// advancedcheckout 1.6
	$(document).off('click', '.opc-flr')	
	$(document).on('click', '.opc-flr', function (npw){	
		if ($('input[name^=delivery_option]:checked').val() == ecm_novaposhta.id+',' &&  !np_isvalid()){
			event.stopImmediatePropagation();
			event.stopPropagation();
			event.preventDefault();
		}
	});
	
	// supercheckout 1.6
	$(document).off('click', '#buttonWithProgres')	
	$(document).on('click', '#buttonWithProgres', function (npw){	
		if ($('input[name^=delivery_option]:checked').val() == ecm_novaposhta.id+',' &&  !np_isvalid()){
			event.stopImmediatePropagation();
			event.stopPropagation();
			event.preventDefault();
			return false;
		}
		saveform(0);
	});
	
	//onepagecheckoutps 1.6 1.7
	$("#btn_place_order").off("click")
	$("#btn_place_order").on("click", function (npt){
		if ($('input[name^=delivery_option]:checked').val() == ecm_novaposhta.id+',' && !np_isvalid()){
			npt.stopPropagation();
			return false;
		}
	});

}

function np_isvalid(){
	if (   ecm_novaposhta.id == $("#carrier").val() 
		|| ecm_novaposhta.id+',' == $('input:radio[class=delivery_option_radio]:checked').val()
		|| ecm_novaposhta.id+',' == $('.delivery_option_radio:checked').val()
		|| ecm_novaposhta.id+',' == $('input[name^=delivery_option]:checked').val()
		|| '1'+ecm_novaposhta.id+'00' == $('input[name^=id_carrier]:checked').val()
		){
		if ($("#another_recipient").prop("checked")) {
			if(document.getElementById("another_firstname").validity.patternMismatch || $('#another_firstname').val()==''){
				np_alert("Треба заповнити українською");
				$("#another_firstname").focus();
				return false;
			}
			if(document.getElementById("another_lastname").validity.patternMismatch || $('#another_lastname').val()==''){
				np_alert("Треба заповнити українською");
				$("#another_lastname").focus();
				return false;
			}
			if(document.getElementById("another_middlename"))
				if(document.getElementById("another_middlename").validity.patternMismatch || $('#another_middlename').val()==''){
					np_alert("Треба заповнити українською");
					$("#another_middlename").focus();
					return false;
				}
				
			if(document.getElementById("another_phone").validity.patternMismatch || $('#another_phone').val()==''){
				np_alert("Помилка в номері телефону !!!");
				$("#another_phone").focus();
				return false;
			}
		}
		
		if ($("#id_ware_delivery").val() == '0') {
			np_alert($('#id_ware_delivery option:selected').text());
			$("#id_ware_delivery").focus();
			return false;
		}
		if ($("#id_ware_delivery").val() == '1') {
			if($('#StreetName').val()==''){
				np_alert("Треба заповнити українською хочаб вулицю");
				$("#StreetName").focus();
				return false;
			}
		}	
		np_fill_address();
	}
	return true;
}

function np_alert(msg) {
	//console.log(msg);
	if (typeof($.growl)== 'function'){
		$.growl.error({
			title: msg,
			size: "medium",
			message: "",
		});
	} else {
		alert(msg);
	}
}


function refreshDeliveryOption() {
	var data = {
		ajax    :"1",
		action  :"selectDeliveryOption",
	};
	var url = window.location.href;
	$.ajax({
		data:data, url:url, allow_refresh:0, method:'POST',
		success: function(html) {

		}
	});
}



function np_fill_address(){
	if($("#md_page").val() != 'cart') {return}
	if(typeof(ecm_novaposhta) != 'object') {return}
	if(ecm_novaposhta.id != $("#carrier").val()) {return}
	if ($("#id_ware_delivery  option:selected").val() == undefined || $("#id_ware_delivery  option:selected").val() == '0') {return}
	np_fill_address_sc();
	if($("#fill").val() == "1" &&  ecm_novaposhta.ac == "1" && $("#md_page").val() == 'cart'){
	//if (typeof($('.opc-flr'))=="object") $("#address1").val(''); // advancedcheckout
		$("#city").val("");
		$("#delivery_city").val("");
		$("#shipping_address[city]").val('');

		if ($("#id_city_delivery  option:selected").val() != '0') {
			$("#city").val($("#id_city_delivery  option:selected").text()).change();
			$("#delivery_city").val($("#id_city_delivery  option:selected").text());
			$("#shipping_address[city]").val($("#id_city_delivery  option:selected").text());
		}
		
		$("#address1").val('');
		$("#delivery_address1").val('');
		$("#shipping_address[address1]").val('');
		if ($("#id_ware_delivery  option:selected").val() != '0') {
			
			if ($("#id_ware_delivery  option:selected").val() == 1 && $("#StreetName").val()!='') {
				var StreetName = $("#StreetName").val()+', дім '+$("#BuildingNumber").val()+', кв. '+$("#Flat").val();
			}else{
				if ($("#id_ware_delivery  option:selected").val() == 1 && $("#StreetName").val()==''){
					var StreetName = '';
				}else{
			 		var StreetName = $("#id_ware_delivery  option:selected").text();
				}
			}
			
			$("#address1").val(StreetName).change();
			$("#delivery_address1").val(StreetName);
			$("#shipping_address[address1]").val(StreetName);
		}
		
		if ($("#id_area_delivery  option:selected").val() != '0') {
			$("#shipping_address[id_state]").val($("#id_area_delivery  option:selected").attr('area_id'));
			$("#delivery_id_state").val($("#id_area_delivery  option:selected").attr('area_id'));
			$("#id_state").val($("#id_area_delivery  option:selected").attr('area_id')).change();
		}
	}
}


function np_gettrueurl(){
	return ecm_novaposhta.module_dir;
}

function clearJSON(result){
	var position = result.indexOf("{");
	//console.warn(result.substring(0,position));
	return jQuery.parseJSON(result.substring(position));
}




function refreshdelivery(cart) {
	var url = np_gettrueurl();
	var cart_qties = $("#cart_qty").val();
	var size = [];
	if($("#nal").prop("checked")){
		var nal = 1;
	}else{
		var nal = 0;
	}
	if($("#customsize").prop("checked")){
		var customsize = 1;
		size[0] =[];
		size[0][0] = $("#width").val();
		size[0][1] = $("#height").val();
		size[0][2] = $("#depth").val();
		size[0][3] = $("#weight").val();
		size[0][4] = $("#qty").val();
		size[0][5] = $("#price").val();
	}else{
		var customsize = 0;
		for (var i = 0; i < cart_qties; i++) {
			size[i] =[];
			size[i][0] = $("#width"+i).val();
			size[i][1] = $("#height"+i).val();
			size[i][2] = $("#depth"+i).val();
			size[i][3] = $("#weight"+i).val();
			size[i][4] = $("#qty"+i).val();
			size[i][5] = $("#price"+i).val();
		}
	}
	var data = {
		mode      :"refreshdelivery",
		customer  :$("#customer").val(),
		area      :$("#id_area_delivery").val(),
		city      :$("#id_city_delivery").val(),
		ware      :$("#id_ware_delivery").val(),
		nal       :nal,
		customsize:customsize,
		cart_qties:cart_qties,
		sizes     :JSON.stringify(size),
		cart      :cart,
	};
	$.ajax({
		data:data, url:url, allow_refresh:1,
		success: function(html) {
			$("#refreshdelivery").html(html);
			if(ecm_novaposhta.ac != 1){
				//window.location = window.location.href.toString().replace("?step=2", "") + "?step=2";
			}
		}
	});
}


function refreshcity() {
	var url = np_gettrueurl();
	var data = {
		mode: "city",
		area: $("#id_area_delivery").val(),
		lang: $("#id_lang").val(),
	};
	$("#id_city_delivery").val(0);
	$("#id_ware_delivery").val(0);
	$('#id_city_delivery').prop('disabled', true);
	$('#id_ware_delivery').prop('disabled', true);
	$.ajax({
		datatype: "json",
		data: data, url: url,
		success: function(result) {
			var a = clearJSON(result);
			//var a = jQuery.parseJSON(result);
			var objSel = document.getElementById("id_city_delivery");
			objSel.options.length = 0;
			var key
			for (key in a) {
				objSel.options[objSel.options.length] = new Option(a[key], key);
			}
			if(ecm_novaposhta.capital_top == '1' && $("#id_area_delivery").val() != '0') {
				$("#id_city_delivery :nth-child(2)").prop('selected', true).change();
			}else{
				//refreshware();
				$("#id_city_delivery").val(0).change();
			}
			$('#id_city_delivery').prop('disabled', false);
			np_fill_address();
		}
	});
}

function refreshoutcity() {
	var url = np_gettrueurl();
	var data = {
		mode: "city",
		area: $("#id_area_out").val(),
		lang: $("#id_lang").val(),
	};
	$("#id_city_out").val(0);
	$("#id_ware_out").val(0);
	$('#id_city_out').prop('disabled', true);
	$('#id_ware_out').prop('disabled', true);
	$.ajax({
		datatype: "json",
		data: data, url: url,
		success: function(result) {
			var a = clearJSON(result);
			//var a = jQuery.parseJSON(result);
			var objSel = document.getElementById("id_city_out");
			objSel.options.length = 0;
			var key
			for (key in a) {
				objSel.options[objSel.options.length] = new Option(a[key], key);
			}
			$('#id_city_out').prop('disabled', false);
			if(ecm_novaposhta.capital_top == '1') {
				$("#id_city_out :nth-child(2)").prop('selected', true);
				$("#id_city_out").change();
			}else{
				refreshoutware();
				$("#id_city_out").val(0).change();
			}
		}
	});
}


function cost_by_city() {
	if($("#id_city_delivery").val() != 0){
		var selected = $("#id_ware_delivery").val();
		var url = np_gettrueurl();
		var data = {
			mode: "cost_by_city",
			area: $("#id_area_delivery").val(),
			city: $("#id_city_delivery").val(),
			lang: $("#id_lang").val(),
			cart: $("#cart_id").val(),
			page: $("#md_page").val(),
			module :$('input[name="method_payment"]:checked').val(),
		};
		$("#id_ware_delivery").val(0);
		$('#id_ware_delivery').prop('disabled', true);
		$.ajax({
			datatype: "json",
			data: data, url: url,
			success: function(result) {
				a = clearJSON(result);
				//a['is_change'] = false;
				if (a['is_change'] && a['success']){
					$("#md_cost").text(parseFloat(a['Cost']));
					$("#mdcost").val(a['Cost']);
					$("#md_costredelivery").text(parseFloat(a['CostRedelivery']));

					if (typeof(action) == 'function') {action(renderSeq);}
					
					//refreshDeliveryOption();

					//if (ecm_novaposhta.ac==0  && $("#md_page").val() == 'cart') location.reload();
					//saveform(1);
					//uniform_update();
					if (typeof(updateCarrierSelectionAndGift) == 'function') updateCarrierSelectionAndGift();
					if (typeof(updateCarrierOnDeliveryChange) == 'function') updateCarrierOnDeliveryChange();
					if (typeof(updcarrieraddress) == 'function') updcarrieraddress(1);
					if (typeof(Carrier) != 'undefined')
					if (typeof(Carrier.update) == 'function') Carrier.update({load_carriers: true, load_payments: true, load_review: false});
				}
			}
		});
	}
}

function refreshware() {
	
	var url = np_gettrueurl();
	var data = {
		mode: "ware",
		city: $("#id_city_delivery").val(),
		lang: $("#id_lang").val(),
		page: $("#md_page").val(),
		cart: $("#cart_id").val(),
	};
	//$("#id_ware_delivery").val(0);
	$('#id_ware_delivery').prop('disabled', true);
	$.ajax({
		datatype: "json",
		data: data, url: url,
		success: function(result) {
			var a = clearJSON(result);
			var objSel = document.getElementById("id_ware_delivery");
			objSel.options.length = 0;
			var key
			for (key in a) {
				objSel.options[objSel.options.length] = new Option(a[key], key);
			}
			$("#id_ware_delivery").val(ecm_novaposhta.address_default).change();
			$('#id_ware_delivery').prop('disabled', false);
			//if ($("#id_ware_delivery").val()=='0') $("#id_ware_delivery").val(ecm_novaposhta.address_default).change();
			np_fill_address();
		}
	});

}

function refreshoutware() {
	var selected = $("#id_ware_out").val();
	var url = np_gettrueurl();
	var data = {
		mode: "ware",
		city: $("#id_city_out").val(),
		lang: $("#id_lang").val(),
		page: 'settings',
		cart: $("#cart_id").val(),
	};
	//$("#id_ware_delivery").val(0);
	$('#id_ware_out').prop('disabled', true);
	$.ajax({
		datatype: "json",
		data: data, url: url,
		success: function(result) {
			var a = clearJSON(result);
			var objSel = document.getElementById("id_ware_out");
			objSel.options.length = 0;
			var key
			for (key in a) {
				objSel.options[objSel.options.length] = new Option(a[key], key);
			}
			//$("#id_ware_delivery").val(1).css('font-size', '120%');
			$('#id_ware_out').prop('disabled', false);
			$("#id_ware_out").val(selected).change();
			if (!$("#id_ware_out").val()) $("#id_ware_out").val(0).change();
		}
	});

}

var timeoutId;

function delaysave(timeout){
	uniform_update();
	clearTimeout(timeoutId);
	timeoutId = setTimeout('saveform(0)', timeout);


}

function uniform_update(){
	if($("#md_page").val() == 'cart' && $.uniform) {$.uniform.update(".form-control");$.uniform.update(".form-control");}
}

function another_update(){
	if ($("#another_recipient").prop("checked")) {
		$(".for_collapse_u").css("display", "1");
		$(".for_collapse_m").css("display", "none");
		$(".another_recipient").show();
		$(".another").prop('required',true)
	}
	else {
		$(".for_collapse_m").css("display", "1");
		$(".for_collapse_u").css("display", "none");
		$(".another_recipient").hide();
		$(".another").removeProp('required');
		$(".another").removeAttr('required');
	}
	saveform(0);
}

function saveform(updated) {
	np_fill_address();

	if ($("#id_ware_delivery :checked").val() == '1') {
		$(".for_collapse").css("display", "1");
		$(".np_address_delivery").show();
		$("#StreetName").prop('required',true);
		$('#np_address_delivery').prop('checked',true);
		$('#np_address_delivery').parent().addClass('checked');
	}
	else {
		$(".for_collapse").css("display", "none");
		$(".np_address_delivery").hide();
		$("#StreetName").removeAttr('required');
		$('#np_address_delivery').removeAttr('checked');
		$('#np_address_delivery').parent().removeClass('checked');
	}
	
	//$(".for_collapse").css("visibility", "collapse") ;

	if ($("#id_ware_delivery").val() !=0 ||  updated) {
		if (typeof timeoutId !== 'undefined') clearTimeout(timeoutId);
		var url = np_gettrueurl();
		if($("#nal").prop("checked")){
			var nal = 1;
		}else{
			var nal = 0;
		}
		if ($("#md_lastname").val() == ""){$("#md_lastname").val($("#lastname").val())}
		if ($("#md_firstname").val() == ""){$("#md_firstname").val($("#firstname").val())}
		if ($("#md_middlename").val() == ""){$("#md_middlename").val($("#middlename").val())}
		if ($("#md_phone").val() == ""){$("#md_phone").val($("#phone").val())}

		var data = {
			mode      :"saveform",
			employee  :$("#employee").val(),
			customer  :$("#customer").val(),
			page      :$("#md_page").val(),
			cart      :$("#cart_id").val(),
			order     :$("#id_order").val(),
			area      :$("#id_area_delivery").val(),
			area_id   :$("#id_area_delivery  option:selected").attr('area_id'),
			city      :$("#id_city_delivery").val(),
			ware      :$("#id_ware_delivery").val(),
			StreetRef     :$("#Street").val(),
			StreetType     :$("#StreetType").val(),
			StreetName     :$("#StreetName").val(),
			BuildingNumber :$("#BuildingNumber").val(),
			Flat           :$("#Flat").val(),
			total_wt  :$("#total_wt").val(),
			lastname  : $("#md_lastname").val(),
			firstname : $("#md_firstname").val(),
			middlename : $("#md_middlename").val(),
			phone     : $("#md_phone").val(),
			another_recipient :Number($("#another_recipient").prop("checked")),
			another_middlename  :$("#another_middlename").val(),
			another_lastname  :$("#another_lastname").val(),
			another_firstname :$("#another_firstname").val(),
			another_phone     :$("#another_phone").val(),
			msg       :$("#msg").val(),
			nal       :nal,
			redelivery:Number($("#redelivery").prop("checked")),
			weight	  :$("#weight").val(),
			vweight   :$("#width").val()*$("#height").val()*$("#depth").val()/4000,
			module    :$('input[name="method_payment"]:checked').val(),
		};
		$.ajax({
			datatype: "json",
			type: "POST",
			data:data, url:url,
			success: function(result) {
				var a = clearJSON(result);
				if ($("#customer").val() < 0) {
					if($("#id_ware_delivery").val() == 1){
						$("#sender_address").show();
						if (a.api.success){
							showSuccessMessage("Адрес :"+a.api.Description);
						} else {
							showErrorMessage('Erorr! See in console');
						}
					} else {
						$("#sender_address").hide();
					}
					return;
				}
				//a['ware_change'] = false;
				if (a['ware_change'] && a['success']){
					if(ecm_novaposhta.show){
						$("#mdcost").val(a['Cost']);
						$("#md_cost").text(Number($("#mdcost").val()).toFixed(2));
						$("#md_costredelivery").text(Number(a['CostRedelivery']).toFixed(2));
					}
					if ($("#another_recipient").prop("checked")) {
						$("#another_lastname").prop("defaultValue",$("#another_lastname").val());
						$("#another_firstname").prop("defaultValue",$("#another_firstname").val());
						$("#another_middlename").prop("defaultValue",$("#another_middlename").val());
						$("#another_phone").prop("defaultValue",$("#another_phone").val());
						$("#StreetName").prop("defaultValue",$("#StreetName").val());
					}
					if (typeof(action) == 'function') {action(renderSeq);}

					//if (ecm_novaposhta.ac==0  && $("#md_page").val() == 'cart') location.reload();
					//saveform(1);
					//uniform_update();
					if (typeof(updateCarrierOnDeliveryChange) == 'function') updateCarrierOnDeliveryChange();
					if (typeof(updcarrieraddress) == 'function') updcarrieraddress(1);
					if (typeof(Carrier) != 'undefined')
						if (typeof(Carrier.update) == 'function') Carrier.update({load_carriers: true, load_review: false});
					
				}
			}
		});
	}
}

function saveform_adm(updated) {

	if ($("#id_ware_delivery").val() !=0 ||  updated) {
		var url = np_gettrueurl();
		var data = {
			mode      :"saveform",
			employee  :$("#employee").val(),
			customer  :$("#employee").val(),
			page      :'settings',
			area      :$("#id_area_out").val(),
			city      :$("#id_city_out").val(),
			ware      :$("#id_ware_out").val(),
		};
		$.ajax({
			datatype: "json",
			type: "POST",
			data:data, url:url,
			success: function(result) {
				var a = clearJSON(result);
				if ($("#employee").val() < 0) {
					return;
				}
			}
		});
	}
}

function fixaddress(){
	var url = np_gettrueurl();
	var data2 = {
		mode: "fixaddress",
		cart: $("#cart_id").val(),
	};
	$.ajax({
		datatype: "json", data: data2, url: url,
		success: function(result) {
			if(document.getElementById("id_address_delivery")){
				setTimeout('addresslist()', 50);
			}
		}
	});
}

function edrpou(){
	var url = np_gettrueurl();
	var data = {
		mode:   "edrpou",
		edrpou: $("#edrpou").val(),
		city:   $("#id_city_delivery").val(),
		employee:$("#employee").val(),
	};
	$.ajax({
		datatype: "json", 
		data: data, 
		url: np_gettrueurl(),
		type: "POST",
		success: function(result) {
			result = jQuery.parseJSON(result);
			//console.log(result);
			if(result!=false && result[0].CounterpartyFullName){
				$('.edrpou').html(result[0].CounterpartyFullName+', '+result[0].CityDescription);
				$('#counterparty').val(result[0].Ref);
			} else {
				$('.edrpou').html('');
				$('#counterparty').val('')
			}		
		},
	});
}

function makettn(id_order,update) {
	if ($("#msg").val().length > $("#TrimMsg").val()){
		alert("Длинное описание! - "+$("#msg").val().length+ " символов");
		return;
	}
	if (Number($("#free_limit").val()) < Number($("#cod_value_od").val()) && !($("#pay_check").prop("checked"))){
		if(!confirm('Не отмечена "отправка отправителем" !!! \nСумма заказа достаточна для бесплатной доставки.' )) return;
	}
	if (Number($("#free_limit").val()) > Number($("#cod_value_od").val()) && ($("#pay_check").prop("checked"))){
		if(!confirm('Отмечена "отправка отправителем" !!! \nСумма заказа не достаточна для бесплатной доставки !!!' )) return;
	}
	if (Number($("#cod_value").val()) != (Number($("#cod_value_od").val())-Number($("#total_paid_real").val()))  && ($("#nal_check").prop("checked"))){
		if(!confirm("Сумма доплаты ("+ (Number($("#cod_value_od").val())-Number($("#total_paid_real").val()) ) +") отличается от суммы наложенного платежа ("+Number($("#cod_value").val())+") !")) return;
	}
	if (update == 1) mode = "updatettn"; else mode = "makettn";

/*  	var data = {
		mode:mode,
		id_order:id_order,
		id_cart:$("#id_cart").val(),
		lastname : $("#md_lastname").val(),
		firstname : $("#md_firstname").val(),
		phone : $("#md_phone").val(),
		redelivery:Number($("#redelivery").prop("checked")),
		pay_check:Number($("#pay_check").prop("checked")),
		nal_pay_check:Number($("#nal_pay_check").prop("checked")),
		another_recipient :Number($("#another_recipient").prop("checked")),
		another_lastname  :$("#another_lastname").val(),
		another_firstname :$("#another_firstname").val(),
		another_phone     :$("#another_phone").val(),
		employee:$("#employee").val(),
		customer  :$("#customer").val(),
		area_out :$("#id_area_out").val(),
		city_out :$("#id_city_out").val(),
		ware_out :$("#id_ware_out").val(),
		ware:$("#id_ware_delivery").val(),
		city:$("#id_city_delivery").val(),
		area:$("#id_area_delivery").val(),
		area_id:$("#id_area_delivery  option:selected").attr('area_id'),
		StreetName     :$("#StreetName").val(),
		BuildingNumber :$("#BuildingNumber").val(),
		Flat           :$("#Flat").val(),
		data:$("#data").val(),
		weight:$("#weight").val(),
		vweight:$("#vweight").val(),
		insurance:$("#insurance").val(),
		cod:$("#cod").val(),
		cod_value:$("#cod_value").val(),

		payment_method :$(".payment_method").val(),

		on_card:$("#on_card").val(),
		description:$("#description").val(),
		pack:$("#pack").val(),
		PackingNumber:$("#PackingNumber").val(),
		msg:$("#msg").val(),
		seats_amount:$("#seats_amount").val(),
	}
	
 */	
    var data_s = $('#np_fieldset').serializeArray();
	//console.log(data_s);
	var data_q = {};
	
	data_q ['mode'] = mode;
	data_q ['id_order'] = id_order;
	
	$.each(data_s, function (index, value) {
        data_q[value.name] = value.value;
	});
	
	data_q ['redelivery'] = Number($("#redelivery").prop("checked"));
	data_q ['pay_check'] = Number($("#pay_check").prop("checked"));
	data_q ['nal_pay_check'] = Number($("#nal_pay_check").prop("checked"));
	data_q ['another_recipient'] = Number($("#another_recipient").prop("checked"));

	//console.log(data_q)
	
	$.ajax({
		data:data_q, 
		url:np_gettrueurl(),
		type: "POST",
		success: function(html) {
			$("#result").html(html);
			setTimeout('location.reload()', 2000);
		}
	});
//	location.reload();
}

function deletettn(id_order) {
	var url = np_gettrueurl();
	var data = {mode: "deletettn", id_order: id_order, employee:$("#employee").val(),};
	$.ajax({
		data:data, url:url,
		success: function(html) {
			//$("#ttn").empty();
			//$("#ttn").append(html);
			setTimeout('location.reload()', 500);
		}
	});

}

function CheckPossibilityCreateReturn(ref) {
	var url = np_gettrueurl();
	var data = {mode: "CheckPossibilityCreateReturn", ref: ref};
	$.ajax({
		data:data, url:url,
		success: function(html) {
			$("#result").html(html);
			//$("#ttn").empty();
			//$("#ttn").append(html);
			//setTimeout('location.reload()', 500);
		}
	});

}

function cost() {
	if ($("#msg").val().length > $("#TrimMsg").val()){
		alert("Длинное описание! - "+$("#msg").val().length+ " символов");
		return;
	}
	var url = np_gettrueurl();
	var data = {
		mode: "cost",
		id_order:$("#id_order").val(),
		id_cart:$("#id_cart").val(),
		lastname : $("#md_lastname").val(),
		firstname : $("#md_firstname").val(),
		middlename : $("#md_middlename").val(),
		phone : $("#md_phone").val(),
		another_recipient :Number($("#another_recipient").prop("checked")),
		another_lastname  :$("#another_lastname").val(),
		another_firstname :$("#another_firstname").val(),
		another_middlename :$("#another_middlename").val(),
		another_phone     :$("#another_phone").val(),
		employee:$("#employee").val(),
		on_card:$("#on_card").val(),
		customer  :$("#customer").val(),
		payment_method :$("#payment_method").val(),
		city_out :$("#id_city_out").val(),
		ware_out :$("#id_ware_out").val(),
		ware:$("#id_ware_delivery").val(),
		city:$("#id_city_delivery").val(),
		area:$("#id_area_delivery").val(),
		area_id:$("#id_area_delivery  option:selected").attr('area_id'),
		StreetName     :$("#StreetName").val(),
		BuildingNumber :$("#BuildingNumber").val(),
		Flat           :$("#Flat").val(),
		redelivery:Number($("#redelivery").prop("checked")),
		pay_check:Number($("#pay_check").prop("checked")),
		nal_pay_check:Number($("#nal_pay_check").prop("checked")),
		weight:$("#weight").val(),
		vweight:$("#vweight").val(),
		insurance:$("#insurance").val(),
		cod:$("#cod").val(),
		cod_value:$("#cod_value").val(),
		description:$("#description").val(),
		pack:$("#pack").val(),
		msg:$("#msg").val(),
		seats_amount:$("#seats_amount").val(),
		RedBoxBarcode:$("#RedBoxBarcode").val(),
		InfoRegClientBarcodes:$("#InfoRegClientBarcodes").val(),
		edrpou:$("#edrpou").val(),
		counterparty:$("#counterparty").val(),
	};
	//console.warn(data);
	$.ajax({
		data:data, url:url,
		success: function(html) {
			$("#result").html(html);
		}
	});
	setTimeout('location.reload()', 1000);
}

function splitorder(id_order) {
	var url = np_gettrueurl();
	var data = {
		mode: "splitorder",
		employee:Math.abs($("#employee").val()),
		id_order: id_order
	};
	$.ajax({
		data:data, url:url,
		success: function(html) {
			$("#result").html(html);
		}
	});
	setTimeout('location.reload()', 1000);
}


function copy(item) {
	var item_od = item+"_od";
	var url = np_gettrueurl();
	var data = {mode: "copy", id_order: $("#id_order").val()};
	$.ajax({
		datatype: "json",
		data:data,
		url:url,
		success: function(result) {
			a = clearJSON(result);
			$("#"+item).val(a[item_od]);
		}
	});
}

function copy2(item) {
	var item_od = item+"_od";
	var url = np_gettrueurl();
	var data = {mode: "copy2", id_order: $("#id_order").val()};
	$.ajax({
		datatype: "json",
		data:data,
		url:url,
		success: function(result) {
			a = clearJSON(result);
			$("#"+item).val(a[item_od]);
		}
	});
}

function checkpackage(deklaracia,order) {
	var url = np_gettrueurl();
	$("#result").empty();
	var data = {mode: "checkpackage", deklaracia: deklaracia, order:order};
	$.ajax({
		data:data, url:url,
		success: function(html) {
			$("#result").html(html);
		}
	});
}

function length_check(len_max, field_id, counter_id) {
    var len_current = $("#"+field_id).val().length;
    var rest = len_max - len_current;
	document.getElementById(counter_id).firstChild.data = rest;
}

function use_cp(ref) {
	var url = np_gettrueurl();
	var data = {
		mode: "use_cp",
		ref: ref,
		employee:$("#employee").val(),
		counterparty:$("#counterparty").val(),
		phone:$("#Phones_"+ref).val()};
	$.ajax({
		data:data, url:url,
		success: function(result) {
			if (clearJSON(result)) showSuccessMessage("Успешно обновлено");
		}
	});
}

function delete_cp(ref) {
	if ($("#use_"+ref).prop("checked")){
		showErrorMessage("Контакт используется, удалять нельзя !!!");
		return;
	}
	if(!confirm(' Действительно удалить этот контакт ? \r\n\r\n Он может использоваться другим сотрудником !!!')) return
	var url = np_gettrueurl();
	var data = {mode: "delete_cp", ref: ref, employee:$("#employee").val()};
	$.ajax({
		data:data, url:url,
		success: function(result) {
			if (clearJSON(result)) {
				var table = document.getElementById('contacts');
				var tr = document.getElementById('row_'+ref);
				table.deleteRow(tr.rowIndex);
				showSuccessMessage("Успешно удалено");
			}
		}
	});
}

function update_cp(ref) {
	if ($("#LastName_"+ref).val() == $("#LastName_"+ref).prop("defaultValue") &&
		$("#FirstName_"+ref).val() == $("#FirstName_"+ref).prop("defaultValue") &&
		$("#MiddleName_"+ref).val() == $("#MiddleName_"+ref).prop("defaultValue") &&
		$("#Email_"+ref).val() == $("#Email_"+ref).prop("defaultValue") &&
		$("#Phones_"+ref).val() == $("#Phones_"+ref).prop("defaultValue") ) {
		showErrorMessage("Нечего обновлять !!!");
		return;
	}
	var data = {
		mode: "update_cp",
		ref: ref,
		employee:$("#employee").val(),
		counterparty:$("#counterparty").val(),
		firstname:$("#FirstName_"+ref).val(),
		lastname:$("#LastName_"+ref).val(),
		middlename:$("#MiddleName_"+ref).val(),
		email:$("#Email_"+ref).val(),
		phone:$("#Phones_"+ref).val(),
	};
	var url = np_gettrueurl();
	$.ajax({
		data:data, url:url,
		success: function(result) {
			if (clearJSON(result)){
				$("#LastName_"+ref).prop("defaultValue", data.lastname);
				$("#FirstName_"+ref).prop("defaultValue", data.firstname);
				$("#MiddleName_"+ref).prop("defaultValue", data.middlename);
				$("#Email_"+ref).prop("defaultValue", data.email);
				$("#Phones_"+ref).prop("defaultValue", data.phone);
				showSuccessMessage("Успешно обновлено");
			}
		else showErrorMessage("Ошибка обновления !!!");
		}
	});
}


function add_cp() {
	var data = {
		mode: "add_cp",
		counterparty:$("#counterparty").val(),
		employee:$("#employee").val(),
		Ownership:$("#Ownership").val(),
		out_company:$("#out_company").val(),
		city:$("#id_city_delivery").val(),
		firstname:$("#FirstName_cp").val(),
		lastname:$("#LastName_cp").val(),
		middlename:$("#MiddleName_cp").val(),
		email:$("#Email_cp").val(),
		phone:$("#Phones_cp").val(),
	};
	var url = np_gettrueurl();
	$.ajax({
		data:data, url:url,
		success: function(result) {
			if (clearJSON(result)){
				$("#LastName_").val('');
				$("#FirstName_").val('');
				$("#MiddleName_").val('');
				$("#Email_").val('');
				$("#Phones_").val('');
				setTimeout('location.reload()', 2000);
				showSuccessMessage("Успешно обновлено");
			}
		else showErrorMessage("Ошибка добавления !!!");
		}
	});
}

function add_c() {
	var data = {
		mode: "add_c",
		city:$("#id_city_delivery").val(),
		employee:$("#employee").val(),
		company:$("#Description_c").val(),
		email:$("#Email_c").val(),
		phone:$("#Phones_c").val(),
		Ownership:$("#OwnershipForm_c").val(),
	};
	var url = np_gettrueurl();
	$.ajax({
		data:data, url:url,
		success: function(result) {
			if (clearJSON(result)){
				$("#FirstName-").val('');
				$("#Email-").val('');
				$("#Phones-").val('');
				setTimeout('location.reload()', 2000);
				showSuccessMessage("Успешно обновлено");
			}
		else showErrorMessage("Ошибка добавления !!!");
		}
	});
}

function use_c(ref) {
	var url = np_gettrueurl();
	var data = {
		mode: "use_c",
		employee:$("#employee").val(),
		counterparty:ref,
		};
	$.ajax({
		data:data, url:url,
		success: function(result) {
			if (clearJSON(result)) showSuccessMessage("Успешно обновлено");
		}
	});
}

function delete_c(ref) {
	if(!confirm('Действительно очистить данные контрагента?')) return
	var url = np_gettrueurl();
	var data = {mode: "delete_c", ref: ref, employee:$("#employee").val()};
	$.ajax({
		data:data, url:url,
		success: function(result) {
			if (clearJSON(result)) {
				showSuccessMessage("Успешно удалено");
				setTimeout('location.reload()', 2000);

			}
		}
	});
}

function clear_clone(ref) {
	if(!confirm('Хотите очистить список контактов и клонировать отправителя по "программе лояльности"?')) return
	var url = np_gettrueurl();
	var data = {mode: "clear_clone", ref: ref, employee:$("#employee").val()};
	$.ajax({
		data:data, url:url,
		success: function(result) {
			if (clearJSON(result)) {
				showSuccessMessage("Успешно удалено и создано вновь");
				setTimeout('location.reload()', 1000);

			}
		}
	});
}

function np_fill_address_sc(){
	if(typeof(action) != 'function') {return;}
	if ($("#id_area_delivery  option:selected").val() != 0) {
		$("#id_state").val($("#id_area_delivery  option:selected").attr('area_id'));
		if ($("#id_state").val() != $("#id_state").prop('defaultValue')){
			$("#id_state").prop('defaultValue', $("#id_state").val());
			action('save_address', $("#id_state").val(), 'id_state', false);
		}
	} else {
		$("#id_state").val('-');
	}
	
	if ($("#id_city_delivery  option:selected").val() != 0) {
		$("#city").val($("#id_city_delivery  option:selected").text());
		if ($("#city").val() != $("#city").prop('defaultValue')){
			$("#city").prop('defaultValue', $("#city").val());
			action('save_address', $("#city").val(), 'city', false);
		}
	} else {
		$("#city").val('');
	}
	
	if ($("#id_ware_delivery  option:selected").val() != 0) {
		if ($("#id_ware_delivery  option:selected").val() == 1) 
			var StreetName = $("#StreetName").val()+', дім '+$("#BuildingNumber").val()+', кв. '+$("#Flat").val();
		else 
			var StreetName = $("#id_ware_delivery  option:selected").text();
		$("#address1").val(StreetName);
		if ($("#address1").val() != $("#address1").prop('defaultValue')){
			$("#address1").prop('defaultValue', $("#address1").val());
			action('save_address', $("#address1").val(), 'address1', false)
		}
	} else {
		$("#address1").val('')
	}

}

