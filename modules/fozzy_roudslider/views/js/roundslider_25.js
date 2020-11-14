function tooltipVal(e) {
          arr = e.id.split('_');
          var id_product = arr[1];
          var val = e.value;
          var opt_qty = parseFloat($('#blockcart-modal-' + id_product + ' .opt-qty').text());
          var start_ed = $( '#blockcart-modal-' + id_product ).find('input[name ="start_ed"]').val();
          var start_price = $( '#blockcart-modal-' + id_product ).find('input[name ="price_with_reduction"]').val();
          var opt_price = parseFloat($( '#blockcart-modal-' + id_product ).find(".opt-price").text());
          var product_price = $( '#blockcart-modal-' + id_product ).find('input[name ="product_price"]').val();
          
          $('#blockcart-modal-' + id_product + ' input[name ="qty"]').val(val);
          
          if (val >= opt_qty) {
                 $('#blockcart-modal-' + id_product + " .opt-qty").parent().hide();
                 $('#blockcart-modal-' + id_product + " .product-price").text(opt_price + ' грн');
                 if (val < 1)
                  {
                  return "<div>" + parseFloat(val*1000).toFixed(0) + " " + "гр" + "</div><div>" + parseFloat(opt_price*val).toFixed(2) + " грн</div>";
                  }
                 else
                  {
                  if (start_ed == 'кг')
                    {
                      return "<div>" + parseFloat(val).toFixed(2) + " " + start_ed + "</div><div>" + parseFloat(opt_price*val).toFixed(2) + " грн</div>";
                    }
                  else
                    {
                      return "<div>" + parseFloat(val).toFixed(0) + " " + start_ed + "</div><div>" + parseFloat(opt_price*val).toFixed(2) + " грн</div>";
                    }
                  }
                }
                else
                {
                 $('#blockcart-modal-' + id_product + " .opt-qty").parent().show();
                 $('#blockcart-modal-' + id_product + " .product-price").text(product_price);
                 if (val < 1)
                  {
                  return "<div>" + parseFloat(val*1000).toFixed(0) + " " + "гр" + "</div><div>" + parseFloat(start_price*val).toFixed(2) + " грн</div>";
                  }
                 else
                  {
                  if (start_ed == 'кг')
                    {
                      return "<div>" + parseFloat(val).toFixed(2) + " " + start_ed + "</div><div>" + parseFloat(start_price*val).toFixed(2) + " грн</div>";
                    }
                  else
                    {
                      return "<div>" + parseFloat(val).toFixed(0) + " " + start_ed + "</div><div>" + parseFloat(start_price*val).toFixed(2) + " грн</div>";
                    }
                  }
                }
           
         }

function hideKeyboard() {
                //this set timeout 
                setTimeout(function() {
                var field = document.createElement('input');
                field.setAttribute('type', 'text');
                  field.setAttribute('style', 'position:absolute; top: 0px; -webkit-transform: translateY(-9999px); -webkit-user-modify: read-write-plaintext-only; left:0px;');
                document.body.appendChild(field);
              
                field.onfocus = function(){
                  setTimeout(function() {
                    field.setAttribute('style', 'display:none;');
                    setTimeout(function() {
                      document.body.removeChild(field);
                      document.body.focus();
                    }, 14);
                  }, 200);
                };
                field.focus();
                  }, 50);
              }

function active_krutilka() {
  $( '#blockcart-content [id^="blockcart-modal-"]' ).each(function( index ) {
    var from_cart = 0;
    $( this ).detach().prependTo("body");
    $(this).on('hidden.bs.modal', function (event) {
      if (from_cart == 1 && $("#mobile-cart-wrapper #blockcart-content").length) $('#mobile-cart-wrapper').addClass('show');
      if (from_cart == 1 && $("#blockcart #blockcart-content").length) $('#blockcart').addClass('show'); 
      from_cart = 0;
    });
    $( this ).on('show.bs.modal', function (event) {
         var button = $(event.relatedTarget);
         var from_open = button.data('link-place');
         
         if (from_open == 'cart-preview' && $("#mobile-cart-wrapper #blockcart-content").length) {
          $('#mobile-cart-wrapper').removeClass('show');
          from_cart = 1;
         }
         if (from_open == 'cart-preview' && $("#blockcart #blockcart-content").length) {
          $('#blockcart').removeClass('show');
          from_cart = 1;
         }
         
        // console.log(from_open);
         var id_product_modal = $( this ).find('input[name ="id_product"]').val();
         var start_slider = parseFloat($( this ).find('input[name ="cart_quantity"]').val());  
         var product_price = $( this ).find('input[name ="product_price"]').val();
         var start_step = parseFloat($( this ).find('input[name ="start_step"]').val());
         var half_step = parseFloat($( this ).find('input[name ="half_step"]').val());
         var start_ed = $( this ).find('input[name ="start_ed"]').val();
         var start_price = parseFloat($( this ).find('input[name ="price_with_reduction"]').val());
         var opt_qty = parseFloat($( this ).find(".opt-qty").text()).toFixed(2);
         var opt_price = parseFloat($( this ).find(".opt-price").text()).toFixed(2);
         var rangeColor_s = "#E30427";
         var maximum = 8;
         
         if (from_cart == 1) {
           $('input#popup_fromcart_' + id_product_modal).val(1);
          }
         else
          {
           $('input#popup_fromcart_' + id_product_modal).val(0);
          }
         
         
         if (start_step == 1) 
              {
                maximum = 15;
              }
         if (start_step == 0.1) 
              {
                maximum = 6;
              }
         
         if (start_slider >= maximum) maximum = parseFloat(start_slider) + 4;
         
         $("#type_"+id_product_modal).roundSlider({
                sliderType: "min-range",
                svgMode: true,
                value: start_slider,
                max: maximum,
                min: start_step,
                startAngle: 130,
                step:start_step,
                mouseScrollAction: 1,
                endAngle: 50,
                lineCap: "round",
                radius: 125,
                width: 12,
                handleSize: "+25",
            	  pathColor: "#F1F1F1",
                rangeColor: rangeColor_s,
                tooltipColor: "#000000",
                tooltipFormat: "tooltipVal",
                stop: function (e) {
               // console.log(e.value);
                if (e.value == maximum)
                  {
                  maximum = maximum*2;
                  if (start_step == 1) 
                      {
                        half_step = 1;
                      }
                  if (start_step == 0.1) 
                      {
                        half_step = 0.5;
                      }
                  if (start_step == 0.1 && maximum == 24) 
                      {
                        half_step = 1;
                      }
                  $("#type_"+id_product_modal).roundSlider({"max": maximum, "step":half_step});
                  }
                
                if (e.value == 0.1 && start_step == 0.1)
                  {
                   maximum = 6;
                   $("#type_"+id_product_modal).roundSlider({"max": maximum, "step": start_step});
                  }
                if (e.value == 1 && start_step == 1)
                  {
                   maximum = 15;
                   $("#type_"+id_product_modal).roundSlider({"max": maximum, "step": start_step});
                  }

                },
                valueChange: function (e) {
                   if ( e.value >= opt_qty && rangeColor_s != "#046F29" )
                   {
                    rangeColor_s = "#046F29";
                    $("#type_"+id_product_modal).roundSlider({"rangeColor": rangeColor_s});
                   }
                   if ( e.value < opt_qty && rangeColor_s != "#E30427" )
                   {
                    rangeColor_s = "#E30427";
                    $("#type_"+id_product_modal).roundSlider({"rangeColor": rangeColor_s});
                   }
                   if (e.value < 3 && e.options.max > 6 && start_step == 0.1)
                    {
                    $("#type_"+id_product_modal).roundSlider({"step":start_step});
                    }
                }      
         });
         
         $( this ).find("span.rs-edit").click(function(e) {
           $("#type_"+id_product_modal).roundSlider({ "max": 500});
             $('input.rs-input').keydown(function(e) {
                  if(e.keyCode === 13) {
                     hideKeyboard();
                  }
             });
         });       
         
         $( this ).find( ".set_" + id_product_modal ).click(function() {
          var vaga = parseFloat($('input[name ="type_' + id_product_modal + '"]').val());
          var st = parseFloat(start_step);
          var unit = 'кг';
          var text_to_button = vaga.toFixed(0);
          if (vaga < 1) {
          vaga = vaga*1000;
          text_to_button = vaga.toFixed(0);
          unit = 'гр';
          }
          else
          {
          if (st < 1) text_to_button = vaga.toFixed(2);
          }
          
          $('#btn_change_' + id_product_modal + " span.button_qty_in").text( text_to_button );
          $('#btn_change_' + id_product_modal + " span.button_qty_in_u").text( unit );
          $('input#popup_change_' + id_product_modal).val(1);
          var loader = '<div class="loader"><div class="cssload-clock"></div></div>';
          $('body').append(loader);
          if ($("#module-ecm_checkout-checkout").length) {
             action('cart');
             action('checkout');
          }
         });
         $( this ).find( ".remove-from-cart").click(function() {
            var vaga = parseFloat(start_step);
            var unit = 'кг';
            if (vaga < 1) {
            vaga = vaga*1000;
            unit = 'гр';
            }
          $('#btn_change_' + id_product_modal + " span.button_qty_in").text( vaga );
          $('#btn_change_' + id_product_modal + " span.button_qty_in_u").text( unit );
          var loader = '<div class="loader"><div class="cssload-clock"></div></div>';
          $('body').append(loader);
          if ($("#module-ecm_checkout-checkout").length) {
             action('cart');
             action('checkout');
          }
         });
         
         
   
   
    });
  $( '#blockcart-content .remove-from-cart' ).each(function( index ) {
        $( this ).click(function() {
          var id_product_modal = $(this).data('id-product');
          var minimum = $(this).data('minimal');
          var vaga = parseFloat(minimum);
            var unit = 'кг';
            if (vaga < 1) {
            vaga = vaga*1000;
            unit = 'гр';
            }
          $('#btn_change_' + id_product_modal + " span.button_qty_in").text( vaga );
          $('#btn_change_' + id_product_modal + " span.button_qty_in_u").text( unit );
          var loader = '<div class="loader"><div class="cssload-clock"></div></div>';
          $('body').append(loader);
          if ($("#module-ecm_checkout-checkout").length) {
             action('cart');
             action('checkout');
          }
         });  
    
        });
 /* $( '#blockcart-content a.in-a-cart' ).each(function( index ) {
        $( this ).click(function(e) {
             e.stopPropagation();
         });  
    
        });       */
  });
}         
$(document).ready(function () {
  
active_krutilka();

});