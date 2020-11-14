$( document ).ready(function() {

$('#delivery_address_container').find('#delivery_address1').hide();
$('#delivery_address_container').find('#delivery_address2').hide();
$('#delivery_address_container').find('#delivery_address1').prev().hide();
$('#delivery_address_container').find('#delivery_address2').prev().hide();  
$('#delivery_valid_adr').hide();
$('#delivery_valid_adr').parent().hide();
$('#delivery_lng').hide();
$('#delivery_lng').parent().hide();
$('#delivery_lat').hide();
$('#delivery_lat').parent().hide();
$('#delivery_zone').hide();
$('#delivery_zone').parent().hide();
$('#delivery_zone_name').hide();
$('#delivery_zone_name').parent().hide();
$('#delivery_is_dm').hide();
$('#delivery_is_dm').parent().hide();




$('#delivery_address_container').on('change', 'input.custom_field', function(item){ 
       var adre = $('#form_address_delivery').find('#delivery_street').val();
       adre = adre + ', ' + $('#form_address_delivery').find('#delivery_house').val();
       adre = adre + ', кв/оф:' + $('#form_address_delivery').find('#delivery_apartment').val();
       adre = adre + ', под.:' + $('#form_address_delivery').find('#delivery_door').val();
       adre = adre + ', этаж:' + $('#form_address_delivery').find('#delivery_level').val();
       adre = adre + ', код домофона:' + $('#form_address_delivery').find('#delivery_intercom').val();
       if ($('#form_address_delivery').find('#delivery_elevator').is(":checked"))
           {
           adre = adre + ', лифт: есть';
           }
       else     
           {
           adre = adre + ', лифт: нет';
           }
       if ($('#form_address_delivery').find('#delivery_concierge').is(":checked"))
           {
           adre = adre + ', консьерж: есть';
           }
       else     
           {
           adre = adre + ', консьерж: нет';
           }

  $('#form_address_delivery').find('#delivery_address1').val(adre);
          
          var adre2 = $('#form_address_delivery').find('#delivery_street').val();
       adre2 = adre2 + '|' + $('#form_address_delivery').find('#delivery_house').val();
       adre2 = adre2 + '|' + $('#form_address_delivery').find('#delivery_apartment').val();
       adre2 = adre2 + '|' + $('#form_address_delivery').find('#delivery_door').val();
       adre2 = adre2 + '|' + $('#form_address_delivery').find('#delivery_level').val();
       adre2 = adre2 + '|' + $('#form_address_delivery').find('#delivery_intercom').val();
       if ($('#form_address_delivery').find('#delivery_elevator').is(":checked"))
           {
           adre2 = adre2 + '|1';
           }
       else     
           {
           adre2 = adre2 + '|0';
           }
       if ($('#form_address_delivery').find('#delivery_concierge').is(":checked"))
           {
           adre2 = adre2 + '|1';
           }
       else     
           {
           adre2 = adre2 + '|0';
           }
       $('#form_address_delivery').find('#delivery_address2').val(adre2);
       
       });

$('#delivery_address_container').on('change', 'input#delivery_street', function(item){
   $('#delivery_valid_adr').prop( "checked", false );
   $('#delivery_is_dm').prop( "checked", false );
   $('#delivery_lng').val('');
   $('#delivery_lat').val('');
   $('#delivery_zone').val('');
   $('#delivery_zone_name').val('');  
});
$('#delivery_address_container').on('change', 'input#delivery_house', function(item){
   $('#delivery_valid_adr').prop( "checked", false );
   $('#delivery_is_dm').prop( "checked", false ); 
   $('#delivery_lng').val('');
   $('#delivery_lat').val('');
   $('#delivery_zone').val('');
   $('#delivery_zone_name').val(''); 
});
$('#delivery_address_container').on('change', 'input#delivery_city', function(item){
   $('#delivery_valid_adr').prop( "checked", false );
   $('#delivery_is_dm').prop( "checked", false );
   $('#delivery_lng').val('');
   $('#delivery_lat').val('');
   $('#delivery_zone').val('');
   $('#delivery_zone_name').val('');  
});

 });