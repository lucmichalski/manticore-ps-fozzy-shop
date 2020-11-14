$( document ).ready(function() {

          $("#valid_adr_on").prop('disabled', true);
          $("#valid_adr_off").prop('disabled', true);

$('#address_form').find('#address3').val($('#address1').val());

$(document).on('change', '#address_form', function(item){

       var adre = $('#address_form').find('#street').val();
       adre = adre + ', ' + $('#address_form').find('#house').val();
       adre = adre + ', кв/оф:' + $('#address_form').find('#apartment').val();
       adre = adre + ', под.:' + $('#address_form').find('#door').val();
       adre = adre + ', этаж:' + $('#address_form').find('#level').val();
       adre = adre + ', код домофона:' + $('#address_form').find('#intercom').val();
       if ($('#address_form').find('#elevator_on').is(":checked"))
           {
           adre = adre + ', лифт: есть';
           }
       else     
           {
           adre = adre + ', лифт: нет';
           }
       if ($('#address_form').find('#concierge_on').is(":checked"))
           {
           adre = adre + ', консьерж: есть';
           }
       else     
           {
           adre = adre + ', консьерж: нет';
           }

  $('#address_form').find('#address1').val(adre);
          
          var adre2 = $('#address_form').find('#street').val();
       adre2 = adre2 + '|' + $('#address_form').find('#house').val();
       adre2 = adre2 + '|' + $('#address_form').find('#apartment').val();
       adre2 = adre2 + '|' + $('#address_form').find('#door').val();
       adre2 = adre2 + '|' + $('#address_form').find('#level').val();
       adre2 = adre2 + '|' + $('#address_form').find('#intercom').val();
       if ($('#address_form').find('#elevator_on').is(":checked"))
           {
           adre2 = adre2 + '|1';
           }
       else     
           {
           adre2 = adre2 + '|0';
           }
       if ($('#address_form').find('#concierge_on').is(":checked"))
           {
           adre2 = adre2 + '|1';
           }
       else     
           {
           adre2 = adre2 + '|0';
           }
       if ($('#address_form').find('#valid_adr_on').is(":checked"))
           {
           adre2 = adre2 + '|1';
           }
       else     
           {
           adre2 = adre2 + '|0';
           } 
       $('#address_form').find('#address2').val(adre2);
     
/*     if ( $('#city').val() == '' || $('#street').val() == '' || $('#house').val() == ''  )
        {
          $("#valid_adr_on").prop('disabled', true);
          $("#valid_adr_off").prop('disabled', true);
        }
     else
        {
          $("#valid_adr_on").prop('disabled', false);
          $("#valid_adr_off").prop('disabled', false);
        }   */
});

$('#address_form').on('change', 'input#street', function(item){
         $('#valid_adr').prop( "checked", false );
         $('#lng').val('');
         $('#lat').val('');
         $('#zone').val('0');
         $('#zone_name').val('Зона не визначена'); 
         $('#valid_adr_on').prop( "checked", false ); 
         $('#valid_adr_off').prop( "checked", true ); 
      });
      $('#address_form').on('change', 'input#house', function(item){
         $('#valid_adr').prop( "checked", false ); 
         $('#lng').val('');
         $('#lat').val('');
         $('#zone').val('0');
         $('#zone_name').val('Зона не визначена');
         $('#valid_adr_on').prop( "checked", false ); 
         $('#valid_adr_off').prop( "checked", true ); 
      });
      $('#address_form').on('change', 'input#city', function(item){
         $('#valid_adr').prop( "checked", false );
         $('#lng').val('');
         $('#lat').val('');
         $('#zone').val('0');
         $('#zone_name').val('Зона не визначена'); 
         $('#valid_adr_on').prop( "checked", false ); 
         $('#valid_adr_off').prop( "checked", true ); 
      }); 

// DM SOLUTIONS
$( '<button id="city_check" type="button" class="btn btn-default pull-left"><i class="process-icon-update" style="font-size:16px;height:18px;width:18px;"></i></button>' ).insertAfter( $("#city").parent() ); 
$( '<button id="street_check" type="button" class="btn btn-default pull-left" style="display:none;"><i class="process-icon-update" style="font-size:16px;height:18px;width:18px;"></i></button>' ).insertAfter( $("#street").parent() );
$( '<button id="house_check" type="button" class="btn btn-default pull-left" style="display:none;"><i class="process-icon-update" style="font-size:16px;height:18px;width:18px;"></i></button>' ).insertAfter( $("#house").parent() );
$( '<button id="getzone" type="button" class="btn btn-default pull-left" style="display:none;"><i class="process-icon-update" style="font-size:16px;height:18px;width:18px;"></i></button>' ).insertAfter( $("#zone_name").parent() );

$( '<select id="city_sel" name="city_sel" style="display:none;"><option value="0" selected >Оберіть місто</option></select>' ).insertAfter( "#city" );
$( '<input id="city_selected" name="city_selected" type="hidden" value="0">' ).insertAfter( "#city_sel" );
$( '<select id="street_sel" name="street_sel" style="display:none;"><option value="0" selected >Оберіть вулицю</option></select>' ).insertAfter( "#street" );
$( '<input id="street_selected" name="street_selected" type="hidden" value="0">' ).insertAfter( "#street_sel" );
$( '<select id="house_sel" name="house_sel" style="display:none;"><option value="0" selected >Оберіть будинок</option></select>' ).insertAfter( "#house" );
$( '<input id="house_selected" name="house_selected" type="hidden" value="0">' ).insertAfter( "#house_sel" );

$(document).on('click', '#city_check', function(item){
     city_leg = document.getElementById("city").value.length;
     if (city_leg > 2) 
      {
      var city_settings = {
            "url": "https://fozzyshop.ua/modules/dmsolutions/ajax_city.php",
            "method": "GET",
            data: {
              "dm_token":dm_token,
              "sRequest":document.getElementById("city").value
            }
          };
        
      $.ajax(city_settings).done(function (response) {
      response = JSON.parse(response);
      if (response.Message) {
         $('#city_sel_error').remove();
         $("#city_sel").hide();
         $('#zone').val('0').addClass("inp_red");
         $('#zone_name').val('Зона не визначена').addClass("inp_red");
         $("#valid_adr_on").prop('disabled', false);
         $("#valid_adr_off").prop('disabled', false);
         $( '<input id="city_sel_error" value="Місто не знайдено" readonly>' ).insertAfter( "#city" );
      }
      else
      {
      var $el = $("#city_sel");
        $el.empty();
        $('#city_sel_error').remove();
        $el.append($("<option></option>").attr("value", 0).text( 'Оберіть місто' ));
      for (var i = 0; i < response.length; i++) {
          var counter = response[i];
          var City_full = counter.City;
          if (counter.Region != null) City_full = City_full + ', ' + counter.Region;
          if (counter.Area != null) City_full = City_full + ', ' + counter.Area; 
          $el.append($("<option></option>").attr("value", counter.st_moniker).text( City_full ));
              
      }
      $el.show();
      }
      });
      
       $(document).on('change', '#city_sel', function(item){
         $('#city_selected').val( $(this).val() ); 
         $('#city').val( $(this).find(":selected").text() );
         $('#street_check').show();
         $('#valid_adr_on').prop( "checked", false ); 
         $('#valid_adr_off').prop( "checked", true );
       });
      

      }


});

$(document).on('click', '#street_check', function(item){
     street_leg = document.getElementById("street").value.length;
     if (street_leg > 3) 
      {
      var street_settings = {
            "url": "https://fozzyshop.ua/modules/dmsolutions/ajax_street.php",
            "method": "GET",
            data: {
              "dm_token":dm_token,
              "sRequest":document.getElementById("street").value,
              "stMoniker":document.getElementById("city_selected").value
            }
          };
              
       
      $.ajax(street_settings)
      .done(function (response) {
      response = JSON.parse(response);
      if (response.Message) {
          $('#street_sel_error').remove();
          $("#street_sel").hide();
          $('#zone').val('0').addClass("inp_red");
          $('#zone_name').val('Зона не визначена').addClass("inp_red");
          $("#valid_adr_on").prop('disabled', false);
          $("#valid_adr_off").prop('disabled', false);
          $( '<input id="street_sel_error" value="Вулицю не знайдено" readonly>' ).insertAfter( "#street" );
      }
      else
      {
      var $el = $("#street_sel");
        $el.empty();
        $('#street_sel_error').remove();
        $el.append($("<option></option>").attr("value", 0).text( 'Оберіть вулицю' ));
      for (var i = 0; i < response.length; i++) {
          var counter = response[i];
            
          $el.append($("<option></option>").attr("value", counter.house_moniker).text( counter.StreetType + ' ' + counter.Street ));
              
      }
      $el.show();
      }
      });
              
       $(document).on('change', '#street_sel', function(item){
          $('#street_selected').val( $(this).val() ); 
          $('#street').val( $(this).find(":selected").text() );
          $('#house_check').show();
          $('#valid_adr_on').prop( "checked", false ); 
          $('#valid_adr_off').prop( "checked", true ); 
       });
      

      }


});

$(document).on('click', '#house_check', function(item){
     house_leg = document.getElementById("house").value.length;
     if (house_leg > 0) 
      {
      var house_settings = {
            "url": "https://fozzyshop.ua/modules/dmsolutions/ajax_house.php",
            "method": "GET",
            data: {
              "dm_token":dm_token,
              "sRequest":document.getElementById("house").value,
              "houseMoniker":document.getElementById("street_selected").value
            }
          };
              
      $.ajax(house_settings).done(function (response) {
      response = JSON.parse(response);
      if (response.Message) {
        $('#house_sel_error').remove();
        $("#house_sel").hide();
        $( '<input id="house_sel_error" value="Будинок не знайдено" readonly>' ).insertAfter( "#house" );
        $('#zone').val('0').addClass("inp_red");
        $('#zone_name').val('Зона не визначена').addClass("inp_red");
        $("#valid_adr_on").prop('disabled', false);
        $("#valid_adr_off").prop('disabled', false);
      }
      else
      {
      var $el = $("#house_sel");
        $el.empty();
        $('#house_sel_error').remove();
        $el.append($("<option></option>").attr("value", 0).text( 'Оберіть будинок' ));
      for (var i = 0; i < response.length; i++) {
          var counter = response[i];
          var house_full = counter.HouseNum;
          if (counter.HouseNumAdd != null) house_full = house_full + counter.HouseNumAdd;
          $el.append($("<option></option>").attr("value", counter.Lat + '|' + counter.Long).text( house_full ));
              
      }
      $el.show();
      }
      });
       
       $(document).on('change', '#house_sel', function(item){
          $('#house_selected').val( $(this).val() );
          $('#house').val( $(this).find(":selected").text() ); 
          var cordinate = $('#house_selected').val().split('|');
          $('#lat').val( cordinate[0] );
          if (cordinate[0] == 'null') $('#lat').val( '' );
          $('#lng').val( cordinate[1] );
          if (cordinate[1] == 'null') $('#lng').val( '' );
          $("#valid_adr_on").prop('disabled', false);
            $("#valid_adr_off").prop('disabled', false);
            $('#valid_adr_on').prop( "checked", false ); 
            $('#valid_adr_off').prop( "checked", true );
          if (cordinate[1] != 'null' && cordinate[0] != 'null')
            {
            $('#is_dm').val('1');
            getzone();
            }
          else
            {
             $('#zone').addClass("inp_green");
             $('#zone_name').addClass("inp_green");
            }
          
       });
      

      }


});


});

function unique(array){
    return array.filter(function(el, index, arr) {
        return index == arr.indexOf(el);
    });
}
function getzone() {
    var send_settings = {
            "url": "https://fozzyshop.ua/modules/dmsolutions/ajax_ml.php",
            "method": "GET",
            data: {
              "lat":document.getElementById("lat").value,
              "lng":document.getElementById("lng").value,
              "dm_id_shop":dm_id_shop
              
            }
          };
        $.ajax(send_settings).done(function (response) {
          response = JSON.parse(response);  
          var Points = response.Comps;
                  var zone_num = '';
                  var zone_nm = '';
                  
                  if ( Points[0].GeoArea_List == '' ) 
                    {
                     zone_num = 0;
                     zone_nm = 'Поза зоною';
                    }
                  else
                    {
                     zone_num = Points[0].GeoArea_List;
                    } 
                  if (zone_num.length > 1 && zone_num != 200 && zone_num != 300 && zone_num != 400 && zone_num != 500 && zone_num != 600)
                    {
                     var zoness = unique( zone_num.split(',') );
                     zoness = zoness.sort();
                     if ( zoness[0] == 4 && zoness[1] == 5 )
                      {
                        zone_num = '4,5';
                        zone_nm = 'Загальна';
                      }
                     if ( zoness[0] == 5 && zoness[1] == 6 )
                      {
                        zone_num = '5,6';
                        zone_nm = 'Загальна 2';
                      } 
                    }
                  else
                    {
                     if ( zone_num == 5 || zone_num == '5' ) zone_nm = 'Заболотного';
                     if ( zone_num == 6 || zone_num == '6' ) zone_nm = 'Проліски';
                     if ( zone_num == 4 || zone_num == '4' ) zone_nm = 'Петрівка';
                     if ( zone_num == 200 || zone_num == '200' ) zone_nm = 'Одеса';
                     if ( zone_num == 300 || zone_num == '300' ) zone_nm = 'Дніпро';
                     if ( zone_num == 400 || zone_num == '400' ) zone_nm = 'Харків'; 
                     if ( zone_num == 500 || zone_num == '500' ) zone_nm = 'Рівне';
                     if ( zone_num == 600 || zone_num == '600' ) zone_nm = 'Кременчуг'; 
                    }
                  
                  $('#zone').val( zone_num ).addClass("inp_green");
                  $('#zone_name').val( zone_nm ).addClass("inp_green");
                        
          });
}
/*
function getzone_old() {
     var auth_server_settings = {
            "url": "http://ant-logistics.com/config?req=api_http",
            "method": "GET",
            "timeout": 0,
            "headers": {
            },
          };
     $.ajax(auth_server_settings).done(function (response) {
        var ant_link = response;
        var user_link = 'DEX_UserAuthorization?format=json&type=login&email=fozzy.logistika.k@gmail.com&pass=123qaZ456&ByUser=0';
        if (dm_id_shop == 2) user_link = 'DEX_UserAuthorization?format=json&type=login&email=fozzy.logistika.o@gmail.com&pass=123qaZ456&ByUser=0';
        if (dm_id_shop == 3) user_link = 'DEX_UserAuthorization?format=json&type=login&email=fozzy.logistika.d@gmail.com&pass=123qaZ456&ByUser=0';
        if (dm_id_shop == 4) user_link = 'DEX_UserAuthorization?format=json&type=login&email=fozzy.logistka.kh@gmail.com&pass=123qaZ456&ByUser=0';
        
        var auth_settings = {
            "url": ant_link + user_link,
            "method": "POST",
            "timeout": 0,
            "headers": {
            },
          };
        $.ajax(auth_settings).done(function (response) {
            var ant_session = response.Session_Ident;
            var comps = '[{"Comp_Id":"100000000","Comp_Name":"TEST","Address":"' + document.getElementById("city").value + ', ' + document.getElementById("street").value + ', ' + document.getElementById("house").value + '","lat":"' + document.getElementById("lat").value + '","lng":"' + document.getElementById("lng").value + '","UserField_1":"31323"}]';
            var send_settings = {
                "url": ant_link + 'DEX_Import_Request_JSON?Session_Ident=' + ant_session + '&Ext_Ident=' + $('#id_address').val() + '&Date_Data=01.01.2020&remove=0&Update_GeoCoord=1&Comps=' + comps,
                "method": "POST",
                "timeout": 0,
              };

            $.ajax(send_settings).done(function (response) {
                var getpiont_settings = {
                    "url": ant_link + 'DEX_Export_Request?Session_Ident=' + ant_session + '&Ext_Ident=' + $('#id_address').val() + '&Date_Data=01.01.2020&ByUser=0&GeoAreaInfo=1',
                    "method": "GET",
                    "timeout": 0,
                  };
                $.ajax(getpiont_settings).done(function (response) {
                  var Points = response.Comps;
                  var zone_num = '';
                  var zone_nm = '';
                  
                  if ( Points[0].GeoArea_List == '' ) 
                    {
                     zone_num = 0;
                     zone_nm = 'Поза зоною';
                    }
                  else
                    {
                     zone_num = Points[0].GeoArea_List;
                    } 
                  if (zone_num.length > 1 && zone_num != 200 && zone_num != 300 && zone_num != 400)
                    {
                     var zoness = unique( zone_num.split(',') );
                     zoness = zoness.sort();
                     if ( zoness[0] == 4 && zoness[1] == 5 )
                      {
                        zone_num = '4,5';
                        zone_nm = 'Загальна';
                      }
                    }
                  else
                    {
                     if ( zone_num == 5 || zone_num == '5' ) zone_nm = 'Заболотного';
                     if ( zone_num == 4 || zone_num == '4' ) zone_nm = 'Петрівка';
                     if ( zone_num == 200 || zone_num == '200' ) zone_nm = 'Одеса';
                     if ( zone_num == 300 || zone_num == '300' ) zone_nm = 'Дніпро';
                     if ( zone_num == 400 || zone_num == '400' ) zone_nm = 'Харків'; 
                    }
                  
                  
                  
                  $('#zone').val( zone_num ).addClass("inp_green");
                  $('#zone_name').val( zone_nm ).addClass("inp_green");
                       var delete_settings = {
                          "url": ant_link + 'DEX_Delete_Request?Session_Ident=' + ant_session + '&Ext_Ident=' + $('#id_address').val() + '&Date_Data=01.01.2020&ByUser=0',
                          "method": "POST",
                          "timeout": 0,
                        };
                        $.ajax(delete_settings).done(function (response) {
                        });          
                  
                  
                  });
            })
             .fail(function (response) {
              });
             
        });
        
      
     });
     
}
*/