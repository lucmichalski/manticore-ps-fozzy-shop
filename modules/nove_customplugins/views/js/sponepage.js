function hide_payments() {
    $('#customer_place').hide();
    $('#payment_place').hide();
    $('#checkout_place').hide();
  }
function show_payments() {
    $('#customer_place').show();
    $('#payment_place').show();
    $('#checkout_place').show();
  }
function edit_address_form() {
    $('.address_group').show();
    $('#edit_address_form_open').hide();
    
  }
$( document ).ready(function() {
/*
$('#address_container').find('#address1').hide();
$('#address_container').find('#address2').hide();
$('#address_container').find('#address1').prev().hide();
$('#address_container').find('#address2').prev().hide();  
$('#valid_adr').hide();
$('#valid_adr').parent().hide();
$('#lng').hide();
$('#lng').parent().hide();
$('#lat').hide();
$('#lat').parent().hide();
$('#zone').hide();
$('#zone').parent().hide();
$('#zone_name').hide();
$('#zone_name').parent().hide();
$('#is_dm').hide();
$('#is_dm').parent().hide();
*/


$('#customer_place').on('change', 'input', function(item){ 
       var adre = $('#customer_place #street').val();
       adre = adre + ', ' + $('#customer_place #house').val();
       adre = adre + ', кв/оф:' + $('#customer_place #apartment').val();
       adre = adre + ', под.:' + $('#customer_place #door').val();
       adre = adre + ', этаж:' + $('#customer_place #level').val();
       adre = adre + ', код домофона:' + $('#customer_place #intercom').val();
       if ($('#customer_place #elevator').is(":checked"))
           {
           adre = adre + ', лифт: есть';
           }
       else     
           {
           adre = adre + ', лифт: нет';
           }
       if ($('#customer_place #concierge').is(":checked"))
           {
           adre = adre + ', консьерж: есть';
           }
       else     
           {
           adre = adre + ', консьерж: нет';
           }

  $('#customer_place #address1').val(adre);
  action('save_address', adre, 'address1', 'false');        
          var adre2 = $('#customer_place #street').val();
       adre2 = adre2 + '|' + $('#customer_place #house').val();
       adre2 = adre2 + '|' + $('#customer_place #apartment').val();
       adre2 = adre2 + '|' + $('#customer_place #door').val();
       adre2 = adre2 + '|' + $('#customer_place #level').val();
       adre2 = adre2 + '|' + $('#customer_place #intercom').val();
       if ($('#customer_place #elevator').is(":checked"))
           {
           adre2 = adre2 + '|1';
           }
       else     
           {
           adre2 = adre2 + '|0';
           }
       if ($('#customer_place #concierge').is(":checked"))
           {
           adre2 = adre2 + '|1';
           }
       else     
           {
           adre2 = adre2 + '|0';
           }
       $('#customer_place #address2').val(adre2);
       action('save_address', adre2, 'address2', 'false');
       });
 
$('#customer_place').on('change', 'input#street', function(item){
   $('#customer_place #valid_adr').prop( "checked", false ).trigger('change');
   $('#customer_place #is_dm').prop( "checked", false ).trigger('change');
   $('#customer_place #lng').val('0.0000000000');
   action('save_address', '0.0000000000', 'lng', 'false');
   $('#customer_place #lat').val('0.0000000000');
   action('save_address', '0.0000000000', 'lat', 'false');
   $('#customer_place #zone').val('');
   action('save_address', '', 'zone', 'false');
   $('#customer_place #zone_name').val('');  
   action('save_address', '', 'zone_name', 'false'); 
});
$('#customer_place').on('change', 'input#house', function(item){
   $('#customer_place #valid_adr').prop( "checked", false ).trigger('change');
   $('#customer_place #is_dm').prop( "checked", false ).trigger('change');
   $('#customer_place #lng').val('0.0000000000');
   action('save_address', '0.0000000000', 'lng', 'false');
   $('#customer_place #lat').val('0.0000000000');
   action('save_address', '0.0000000000', 'lat', 'false');
   $('#customer_place #zone').val('');
   action('save_address', '', 'zone', 'false');
   $('#customer_place #zone_name').val('');  
   action('save_address', '', 'zone_name', 'false'); 
});
$('#customer_place').on('change', 'input#city', function(item){
   $('#customer_place #valid_adr').prop( "checked", false ).trigger('change');
   $('#customer_place #is_dm').prop( "checked", false ).trigger('change');
   $('#customer_place #lng').val('0.0000000000');
   action('save_address', '0.0000000000', 'lng', 'false');
   $('#customer_place #lat').val('0.0000000000');
   action('save_address', '0.0000000000', 'lat', 'false');
   $('#customer_place #zone').val('');
   action('save_address', '', 'zone', 'false');
   $('#customer_place #zone_name').val('');  
   action('save_address', '', 'zone_name', 'false');   
});

 });