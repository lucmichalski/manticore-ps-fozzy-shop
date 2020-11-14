$( document ).ready(function() {


$( ".summ_to_vz" ).click(function() {
 // var summ_to_vz = 0; 
  var id_order = $(this).parent().find('.id_order').text().trim();
  let summ_to_vz = prompt("Сумма погашения", '');
  summ_to_vz = summ_to_vz.replace(/,/, '.');
  $(this).text(summ_to_vz);
  var hr = $(this).parent().find('.link_to_change>a').attr('href') + '&summ=' + summ_to_vz;
  $(this).parent().find('.link_to_change>a').attr('href',hr);
});


});