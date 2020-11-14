$(document).ready(function() {

    $('#customers_horeca').select2();
    
    $('#customers_horeca').on('select2:select', function (e) { 
    var data = e.params.data;
    $('input[name=email]').val(data.id);
    $('input[name=password]').val('123456');
    $("#submit-login").click();
  //  console.log(data);
   // window.location.href = '/login?SubmitLogin=true&email=' + data.id + '&passwd=123456';
});

});