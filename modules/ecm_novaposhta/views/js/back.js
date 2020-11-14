$(function(){
var result_np = '';
	    
	//Живой поиск
	//$('.who').bind("change keyup input click", function() {
	$('#referal_street').bind("input", function() {
		if(this.value.length >= 3){
			$.ajax({
				url: np_gettrueurl(), 
				data: {'referal':this.value, 'CityRef':$("#id_city_delivery").val(), mode: "searchstreet"},
				type: "POST",
				datatype: "json",
				success: function(rows){
					var res = "";
					result_np = jQuery.parseJSON(rows);
					//console.warn(result_np);
					if(result_np){
						for (index = 0; index < result_np.length; ++index) {
							res = res + "<li Street='" + result_np[index].Ref + "' id='" + index + "'>" + result_np[index].StreetsType + " " + result_np[index].Description + "</li>";
						}
						if(res) $("#street_result").html(res).fadeIn(); //Выводим полученые данные в списке
						else $("#street_result").fadeOut(); //Прячем если нечего показывать
					}
				}
			})
		}
		else $("#street_result").html("").fadeOut(); //Прячем если нечего показывать
	})
	
	$("#street_result").hover(function(){
		$("#referal_street").blur(); //Убираем фокус с input
	})
	
	//При выборе результата поиска, прячем список и заносим выбранный результат в input-ы
	$("#street_result").on("click", "li", function(){
		index = $(this).attr('id');
		$("#referal_street").val(result_np[index].Description);
		$("#Street").val(result_np[index].Ref);
		$("#StreetName").val(result_np[index].Description);
		$("#StreetsType").html(result_np[index].StreetsType);
		$("#StreetType").val(result_np[index].StreetsType);
		
		//$("#referal_street").val(s_user).attr('disabled', 'disabled'); //деактивируем input, если нужно
		$("#street_result").fadeOut();
		$("#referal_street").focus(); //Возвращаем фокус на input
		delaysave(600);
	})


})

$(document).ready(function() {
	$(".oncalc").on("input", function(){
		if ($(".oncalc.x").val() && $(".oncalc.y").val() &&$(".oncalc.z").val())
			$('#vweight').val($(".oncalc.x").val() * $(".oncalc.y").val() * $(".oncalc.z").val() / 4000)
	})
});






$(function(){
	//При клике вне списка, прячем списки и возвращаем значение
	$("#content").on("click", "div", function(){
		$("#referal_street").val($("#StreetName").val());
		$("#street_result").fadeOut();
	})
})

function putinputs (index,result_np){
		$("#email").val(result_np[index].email);
		$("#phone").val(result_np[index].phone_mobile);
		$("#phone2").val(result_np[index].phone);
		$("#firstname").val(result_np[index].firstname);
		$("#lastname").val(result_np[index].lastname);
		$("#id_cust").text(result_np[index].id_customer);
		$("#id_customer").val(result_np[index].id_customer);
		$("#id_area_delivery option[value=" + result_np[index].id_area_delivery + "]").prop("selected", true);
		$("#id_area_sel").val(result_np[index].id_area_delivery);
		$("#id_city_sel").val(result_np[index].id_city_delivery);
		$("#id_ware_sel").val(result_np[index].id_ware_delivery);
}