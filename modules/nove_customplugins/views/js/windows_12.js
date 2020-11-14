$( document ).ready(function() {
   
   $( ".show_delyvery img" ).on( "click", function() {
     $.ajax({
            url:  window.location.protocol.toString() + "//" + window.location.host.toString() + "/modules/nove_dateofdelivery/window.php",
            type: "POST",
            data: {
              "timeofdelivery":1,
              "cart_id":1,
              "n_id_lang": n_id_lang,
              "n_id_shop": n_id_shop
            },
            success: function(data){
              $('#index #dostavka').html(data);
              $(".show_delyvery img").remove();
            }        
        });    
     
   }); 
 
        
});