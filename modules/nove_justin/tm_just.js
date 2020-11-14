function gettrueurl_just(){
	return "/justin-ware";
}

function just_refreshcity() {
  $('.container_card.selected button.edit_address').click();
	var url = gettrueurl_just();
	var data = {
		type: "town", 
		uuid_owner: $("#id_area_delivery").val(),
	};
	$("#id_city_delivery").val(0);
	$("#id_ware_delivery").val(0);
	$('#id_city_delivery').prop('disabled', true);
	$('#id_ware_delivery').prop('disabled', true);
	$.ajax({
//		datatype: "json",
		data: data, url: url, 
		success: function(result) {
      $("#id_city_delivery").html(result);
			$('#id_city_delivery').prop('disabled', false);
		}
	});
}

function just_refreshware() {

  $("#delivery_city").val($("#id_city_delivery :selected").text());
	var url = gettrueurl_just();
	var data = {
		type: "ware", 
    id_cart: $("#cart_id").val(),
		uuid_owner: $("#id_city_delivery").val(),
	};
	$("#id_ware_delivery").val(0);
	$('#id_ware_delivery').prop('disabled', true);
	$.ajax({
		datatype: "json",
		data: data, url: url, 
		success: function(result) {
      $("#id_ware_delivery").html(result);
			$('#id_ware_delivery').prop('disabled', false);
		}
	});
}

function just_saveform() {
  var url = gettrueurl_just();
	var data = {
		type: "adr", 
    id_cart: $("#cart_id").val(),
    uuid_region: $("#id_area_delivery").val(),
		uuid_town: $("#id_city_delivery").val(),
    uuid_ware: $("#id_ware_delivery").val(),
	};
  $.ajax({
		datatype: "json",
		data: data, url: url, 
		success: function(result) {
		}
	});
  
  $("#delivery_address1").val($("#id_ware_delivery :selected").text());
  $("#delivery_address2").val($("#id_ware_delivery :selected").text() + '|0|0|0|0|0|0|0');
  $("#delivery_address_street").val($("#id_ware_delivery :selected").text());
  $("#delivery_address_house").val('0');
  $("#delivery_address_hata").val('0');
  $("#delivery_address_level").val('0');
  $("#delivery_address_parad").val('0');
  $("#delivery_address_df").val('0');	
  $('div#action_address_delivery:not(".hidden") button#btn_update_address_delivery').click();
}                                               