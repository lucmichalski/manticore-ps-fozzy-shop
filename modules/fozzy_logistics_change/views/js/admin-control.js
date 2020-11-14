$( document ).ready(function() {
      
      $("#logisticsbors").change(function () {
        
        var sborshik= $("#logisticsbors").val();
      
        $.ajax({
            url:  "../modules/fozzy_logistics_change/ajax.php",
            type: "POST",
            data: {
              "admin":'1',
              "kto":'1',
              "sborshik":sborshik,
              "employee":employee,
              "cart_id":id_cart
            },
            success: function(data){
             window.location.reload(false); 
            }        
        });
      }); 
      
      $("#logisticvods").change(function () {
        
        var vodila= $("#logisticvods").val();
               
        $.ajax({
            url:  "../modules/fozzy_logistics_change/ajax.php",
            type: "POST",
            data: {
              "admin":'1',
              "vodila":vodila,
              "kto":'2',
              "employee":employee,
              "cart_id":id_cart
            },
            success: function(data){
              window.location.reload(false); 
            }        
        });
      });          
});
