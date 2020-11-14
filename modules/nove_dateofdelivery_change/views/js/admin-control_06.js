$( document ).ready(function() {
    
    $('#nvdeliverydateinput').datepicker({language: 'ru', defaultDate: cart_date, minDate: new Date()});   
     
    if (cart_date != "")
      {
       $("#nvdeliverydateinput").hide();
      }

      $("#nvdeliverydatepanel").click(function () {
        
        $("#nvdeliverydateinput").show();
      
      });
      
      $("#nvdeliverydateinput").change(function () {
        
        $("#nvdeliverydate").text($("#nvdeliverydateinput").val());
        $('#stimeofdelivery').prop('disabled', false);
        $("#stimeofdelivery").val(0);
        /*
        var timeofdelivery= $("#nvdeliverydateinput").val() + "_" + $('#stimeofdelivery').val();
               
        $.ajax({
            url:  "../modules/nove_dateofdelivery_change/ajax.php",
            type: "POST",
            data: {
              "admin":'1',
              "timeofdelivery":timeofdelivery,
              "cart_id":id_cart,
              "employee":employee
            },
            success: function(data){
             $("#nvdeliverydate").text($("#nvdeliverydateinput").val());
             $('#stimeofdelivery').prop('disabled', false);
            }        
        }); */
      }); 
      
      $("#stimeofdelivery").change(function () {
        
        var timeofdelivery= $("#nvdeliverydateinput").val() + "_" + $('#stimeofdelivery').val();
               
        $.ajax({
            url:  "../modules/nove_dateofdelivery_change/ajax.php",
            type: "POST",
            data: {
              "admin":'1',
              "timeofdelivery":timeofdelivery,
              "cart_id":id_cart,
              "employee":employee
            },
            success: function(data){
             $("#nvdeliverydate").text($("#nvdeliverydateinput").val());
             $('#stimeofdelivery').prop('disabled', true);
             $("#nvdeliverydateinput").hide();
             location.reload();
            }        
        });
      });          
});
