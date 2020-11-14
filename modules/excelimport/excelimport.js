function setCookie (url, offset, id_lang, id_shop, import_options){
    	var ws=new Date();
        if (!offset && !url && !id_lang && !id_shop) {
                ws.setMinutes(10-ws.getMinutes());
            } else {
                ws.setMinutes(10+ws.getMinutes());
            }
        document.cookie="scriptOffsetUrl="+url+";expires="+ws.toGMTString();
        document.cookie="scriptOffsetOffset="+offset+";expires="+ws.toGMTString();
        document.cookie="scriptOffsetId_lang="+id_lang+";expires="+ws.toGMTString();
        document.cookie="scriptOffsetId_shop="+id_shop+";expires="+ws.toGMTString();
        document.cookie="scriptOffsetImport_options="+import_options+";expires="+ws.toGMTString();
    }
    
function getCookie(name) {
        var cookie = " " + document.cookie;
        var search = " " + name + "=";
        var setStr = null;
        var offset = 0;
        var end = 0;
        if (cookie.length > 0) {
            offset = cookie.indexOf(search);
            if (offset != -1) {
                offset += search.length;
                end = cookie.indexOf(";", offset)
                if (end == -1) {
                    end = cookie.length;
                }
                setStr = unescape(cookie.substring(offset, end));
            }
        }
        return(setStr);
    }

function showProcess (url, sucsess, offset, id_lang, id_shop, import_options) {
        $('.progress').show();
        $('.bar').text($('#loadtext').val());
        $('.bar').css('width', (sucsess * 100).toFixed(2) + '%');
        setCookie(url, offset, id_lang, id_shop, import_options);
        scriptOffset(url, offset, id_lang, id_shop, import_options);
    }
    
function scriptOffset (url, offset, id_lang, id_shop, import_options) {
        $.ajax({
            url: "../modules/excelimport/exceltotable.php",
            type: "POST",
            data: {
              "url":url,
              "offset":offset,
              "id_lang":id_lang,
              "id_shop":id_shop,
              "import_options":import_options
            },        
            success: function(data){
                data = $.parseJSON(data);
                if(data.sucsess != 1) {
                    showProcess(url, data.sucsess, data.offset, id_lang, id_shop, import_options);
                    } else {
                    setCookie();
                    $('.bar').css('width','100%');
                    $('.bar').text('OK');
                    setTimeout(function() {
                    $('.progress').hide();
                    $('.bar').css('width','1%');
                    }, 1000);
                    $('#PrepareFile').hide(); 
                    $('#lang_i').parent().parent().hide();
                    $('#ImportProducts').show();
                    }
            }
        });
    }
    
function setCookieUPD (offset, import_options, delete_features, off_products){
    	var ws=new Date();
        if (!offset && !import_options && !delete_features && !off_products) {
                ws.setMinutes(10-ws.getMinutes());
            } else {
                ws.setMinutes(10+ws.getMinutes());
            }
        document.cookie="scriptOffsetImport_options="+import_options+";expires="+ws.toGMTString();
        document.cookie="scriptOffsetOffset="+offset+";expires="+ws.toGMTString();
        document.cookie="scriptOffsetDelete_features="+delete_features+";expires="+ws.toGMTString();
        document.cookie="scriptOffsetOff_products="+off_products+";expires="+ws.toGMTString();
    }
    
function showProcessUPD (sucsess, offset, import_options, delete_features, off_products) {
        $('.progress').show();
        $('.bar').text('(' + offset + ') - ' + $('#loadtext').val());
        $('.bar').css('width', (sucsess * 100).toFixed(2) + '%');
        setCookieUPD (offset, import_options, delete_features, off_products);
        scriptOffsetUPD (offset, import_options, delete_features, off_products);
    }    


    
function scriptOffsetUPD (offset, import_options, delete_features, off_products) {
        $.ajax({
            url: "../modules/excelimport/tabletoupdate.php",
            type: "POST",
            data: {
              "import_options":import_options,
              "delete_features":delete_features,
              "off_products":off_products,
              "offset":offset
            },
            success: function(data){
                data = $.parseJSON(data);
                if(data.sucsess != 1) {
                    showProcessUPD(data.sucsess, data.offset, import_options, delete_features, off_products);
                    //console.log (off_products);
                    } else {
                    setCookieUPD();
                    $('.bar').css('width','100%');
                    $('.bar').text('OK');
                    setTimeout(function() {
                    $('.progress').hide();
                    $('.bar').css('width','1%');
                    }, 1000);
                    $('#ImportProducts').hide();
                    $('.alert-success').show();
                    $('#lang_i').parent().parent().show();
                    $('#PS_EXCELFILE_NEW-name').parent().parent().parent().parent().parent().show();
                    $("#ImportStop").hide();
                    $('#btnUpload').show();
                    }
            }
        });
    }
    
$(document).ready(function() {
   
  $('#ImportProducts').hide();
  $('#ImportStop').hide();
  
  if ($('#offset_c').val() == 1) $('#ImportContinue').hide();
   
  if ($('#PS_EXCELFILE_UPL').val() == 1)
    {
      $('#PrepareFile').show();
      $('#btnUpload').hide(); 
      $('#ImportContinue').hide();
      $('.alert.alert-info').hide();
      setTimeout(function() {
                    $('.alert-success').hide();
                    }, 700);
      $('#PS_EXCELFILE_NEW-name').parent().parent().parent().parent().parent().hide();
    }
  else
    {
      $('#PrepareFile').hide();
      
      
    }
    
  var impval =$('#import_options').val();
  
  if (impval == 'updatemass' || impval == 'updatemassa' || impval == 'updatepraq' || impval == 'updatepra' || impval == 'updateprice' || impval == 'updatepriceq') 
    {     
    $('#offprod_on').parent().parent().parent().show();
    }
  else
    {
    $('#offprod_on').parent().parent().parent().hide();
    }
    
  if (impval == 'newproduct' || impval == 'fullupdate' || impval == 'updatedescription' || impval == 'updatedescriptiona') 
    {     
    $('#clean_html_on').parent().parent().parent().show();
    }
  else
    {
    $('#clean_html_on').parent().parent().parent().hide();
    }
  
  if (impval == 'updatefeaturesa' || impval == 'updatefeatures' || impval == 'fullupdate') 
    {     
    $('#clearfeatures_on').parent().parent().parent().show();
    }
  else
    {
    $('#clearfeatures_on').parent().parent().parent().hide();
    }
 
  $('#import_options').change(function() {
    
    var impval =$(this).val();
    
    if (impval == 'updatemass' || impval == 'updatemassa' || impval == 'updatepraq' || impval == 'updatepra' || impval == 'updateprice' || impval == 'updatepriceq') 
    {     
    $('#offprod_on').parent().parent().parent().show();
    }
    else
    {
    $('#offprod_on').parent().parent().parent().hide();
    }
    
    if (impval == 'newproduct' || impval == 'fullupdate' || impval == 'updatedescription' || impval == 'updatedescriptiona') 
    {     
    $('#clean_html_on').parent().parent().parent().show();
    }
    else
    {
    $('#clean_html_on').parent().parent().parent().hide();
    }
    
    if (impval == 'updatefeaturesa' || impval == 'updatefeatures' || impval == 'fullupdate') 
    {     
    $('#clearfeatures_on').parent().parent().parent().show();
    }
    else
    {
    $('#clearfeatures_on').parent().parent().parent().hide();
    }
   
  });
  
    var divpr = '<div class="progress" style="display: none; height: 30px;"><div class="bar" style="width: 10%; background-color:blue;color: red; font-weight:bold; font-size: 18px;line-height:30px; height: 30px"></div></div>';
    var url = getCookie("scriptOffsetUrl");
    var loadtext = $('#loadtext').val();
    var offset = getCookie("scriptOffsetOffset");
    var id_shop = getCookie("scriptOffsetId_shop");
    var id_lang = getCookie("scriptOffsetId_lang");
    var import_options = getCookie("scriptOffImport_options");
    var delete_features = getCookie("scriptOffDelete_features");
    var off_products = getCookie("scriptOffOff_products");
   
    $('#barpr').parent().append(divpr);
    
    if (url && url != 'undefined') {		
            $('#url').val(url);
            $('#offset').val(offset);
            $('#lang_i').val(id_lang);
            $('#id_shop').val(id_shop);
        }
    if (import_options && import_options != 'undefined') {		
            $('#import_options').val(import_options);
            $('#offset').val(offset);
            $('input[name=clearfeatures]').val(delete_features);
            $('input[name=offprod]').val(off_products);
        }      
        
  $('#PrepareFile').click(function() {
            
            var offset = $('#offset').val();
            var url = $('#url').val();
            var id_lang = $('#lang_i').val();
            var id_shop = $('#id_shop').val();
            var import_options = $("select[name=import_options]").val();
            $("#PrepareFile").attr('disabled','disabled');
            $('#barpr').parent().removeClass('hide');
            $('.progress').show();
            $('.bar').text(loadtext);
            $('#ImportContinue').hide();
             
           if ($('#url').val() != getCookie("scriptOffsetUrl")) {
                    setCookie();
                    scriptOffset(url, 0, id_lang, id_shop, import_options);
                } else {
                    scriptOffset(url, offset, id_lang, id_shop, import_options);
                }          
            return false;  
    
  }); 
  
  $('#ImportStop').click(function() {
  window.location.href = window.location.href;
  });
  
  $('#ImportContinue').click(function() {
            var offset = $('#offset_c').val();
            var url = $('#url').val();
            var delete_features = $("input[name=clearfeatures]:checked").val();
            var import_options = $("select[name=import_options]").val();
            var off_products = $("input[name=offprod]:checked").val();
            $('.alert.alert-info').hide();
            $('#btnUpload').hide();
            $("#ImportProducts").hide();
            $("#ImportContinue").attr('disabled','disabled');
            $("#ImportStop").show();
            $('#barpr').parent().removeClass('hide');
            $('#lang_i').parent().parent().hide();
            $('#PS_EXCELFILE_NEW-name').parent().parent().parent().parent().parent().hide();
            $('.progress').show();
            $('.bar').text(loadtext);
            scriptOffsetUPD(offset, import_options, delete_features, off_products);
            return false;  
  });
  
  $('#ImportProducts').click(function() {
            var offset = $('#offset').val();
            var url = $('#url').val();
            var delete_features = $("input[name=clearfeatures]:checked").val();
            var import_options = $("select[name=import_options]").val();
            var off_products = $("input[name=offprod]:checked").val();
            $("#ImportProducts").attr('disabled','disabled');
            $("#ImportStop").show();
            $('#barpr').parent().removeClass('hide');
            $('.progress').show();
            $('.bar').text(loadtext);
            
           if ($('#import_options').val() != getCookie("import_options")) {
                    setCookieUPD();
                    scriptOffsetUPD(0, import_options, delete_features, off_products);
                } else {
                    scriptOffsetUPD(offset, import_options, delete_features, off_products);
                }                   
            return false;  
  }); 
});