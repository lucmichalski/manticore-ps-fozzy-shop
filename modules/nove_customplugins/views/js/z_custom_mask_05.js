$( document ).ready(function() {
$.jMaskGlobals = {
  maskElements: 'input,td,span,div',
  dataMaskAttr: '*[data-mask]',
  dataMask: true,
  watchInterval: 300,
  watchInputs: true,
  watchDataMask: false,
  byPassKeys: [9, 16, 17, 18, 36, 37, 38, 39, 40, 91],
  translation: {
    '7': {pattern: /\d/},
    '9': {pattern: /\d/, optional: true},
    '#': {pattern: /\d/, recursive: true},
    'A': {pattern: /[a-zA-Z0-9]/},
    'S': {pattern: /[a-zA-Z]/}
  }
};
$('#phone_mobile').mask('380999999999');
$('#phone').mask('380999999999'); 
$('#bon_order_phone').mask('380999999999');
$('#delivery_phone_mobile').mask('380999999999'); 
$('#delivery_phone').mask('380999999999'); 
$("#address input[name='phone']").mask('380999999999');  
$("#address input[name='phone_mobile']").mask('380999999999');
$("#ooc_form input[name='2']").mask('380999999999');

});