$( document ).ready(function() {

$('#vozvrat, #pretenzia, #m_summ_from_vodila').mask("###############.00", {translation:  {'#': {pattern: /[0-9.\,]/}}});

$("#vozvrat, #pretenzia, #m_summ_from_vodila").keyup(function(){    
   $(this).val($(this).val().replace(/,/, '.'));
});

$("form.AdminKassaMN").keydown(function(event){
        if(event.keyCode == 13) {
          event.preventDefault();
          return false;
      }
});

$("#m_summ_from_vodila").keyup(function(){    
   var summ_to_close = $("#m_summ_to_close").val();
   var summa = parseFloat(summ_to_close - $(this).val()).toFixed(2);
   if (summa < 0)
    {
     $('#m_summ_r').parent().prev().text('Сдача');
     $('#m_summ_r').css("background-color", "#7FFF00");
     $('#m_summ_r').css("color", "#000000");
    }
   if (summa > 0)
    {
     $('#m_summ_r').parent().prev().text('Недостача');
     $('#m_summ_r').css("background-color", "#FF0000");
     $('#m_summ_r').css("color", "#FFFFFF");
    }
   if (summa == 0)
    {
     $('#m_summ_r').parent().prev().text('Разница');
     $('#m_summ_r').css("background-color", "#716d6d");
     $('#m_summ_r').css("color", "#FFFFFF");
    }
   $('#m_summ_r').val(summa);
});


});