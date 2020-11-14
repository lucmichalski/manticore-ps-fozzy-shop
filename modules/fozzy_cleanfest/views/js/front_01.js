$( document ).ready(function() {

if ( $('body#module-fozzy_cleanfest-reestr').length) {

var al = $('#errordate').val();
var alte = $('#datet').val();
var options =  {
  onComplete: function(value) {
    var arrD = value.split("-");
    arrD[1] -= 1;
    var d = new Date(arrD[2], arrD[1], arrD[0]);
    if ((d.getFullYear() == arrD[2]) && (d.getMonth() == arrD[1]) && (d.getDate() == arrD[0])) {
     // return true;
    } else {
      alert(al);
      $('#fiskal_date').val('');
     // return false;
    }
  },
  placeholder: alte
};

//$('#fiskal_num').mask("9/9999/999", {placeholder: "_/____/___"});
$('#fiskal_date').mask("99-99-9999", options); 

}

});