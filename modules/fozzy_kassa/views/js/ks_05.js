$( document ).ready(function() {

$('#k_summ_seif, #k_summ_moneta').mask("###############.00", {translation:  {'#': {pattern: /[0-9.\,]/}}});

$("#k_summ_seif, #k_summ_moneta").keyup(function(){    
   $(this).val($(this).val().replace(/,/, '.'));
});

$("form.AdminKassaSV").keydown(function(event){
        if(event.keyCode == 13) {
          event.preventDefault();
          return false;
      }
});


$("#k_summ_seif").keyup(function(){    
   var k_summ_seif = parseFloat($("#k_summ_seif").val());
   var k_summ_exp = parseFloat($("#k_summ_exp").val());
   var k_summ_vozv = parseFloat($("#k_summ_vozv").val());
   var k_summ_notzakr = parseFloat($("#k_summ_notzakr").val());
   var k_summ_moneta = parseFloat($("#k_summ_moneta").val());
   var k_summ = parseFloat($("#k_summ").val());
   var k_summ_kassa_rp = parseFloat($("#k_summ_kassa_rp").val());
   
   summ_to_close = k_summ_seif+k_summ_exp+k_summ_vozv+k_summ_notzakr+k_summ_moneta; 
   
   $('#k_summ_kassa').val(summ_to_close.toFixed(2));
   $('#k_summ_kassa_r').val((summ_to_close - k_summ).toFixed(2));
});

$("#k_summ_moneta").keyup(function(){    
   var k_summ_seif = parseFloat($("#k_summ_seif").val());
   var k_summ_exp = parseFloat($("#k_summ_exp").val());
   var k_summ_vozv = parseFloat($("#k_summ_vozv").val());
   var k_summ_notzakr = parseFloat($("#k_summ_notzakr").val());
   var k_summ_moneta = parseFloat($("#k_summ_moneta").val());
   var k_summ = parseFloat($("#k_summ").val());
   var k_summ_kassa_rp = parseFloat($("#k_summ_kassa_rp").val());
   
   summ_to_close = k_summ_seif+k_summ_exp+k_summ_vozv+k_summ_notzakr+k_summ_moneta; 
   
   $('#k_summ_kassa').val(summ_to_close.toFixed(2));
   $('#k_summ_kassa_r').val((summ_to_close - k_summ).toFixed(2));
});

});