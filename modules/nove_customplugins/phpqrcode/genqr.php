<?php 

    include('qrlib.php'); 
         
    $param = $_GET['id']; // remember to sanitize that - it is user input! 
    // we need to be sure ours script does not output anything!!! 
    // otherwise it will break up PNG binary! 
    $param2 = $_GET['id_order'];
    $param3 = '00e06a584a9aa636ac09d1930630ff8e'; 
    ob_start(); 
     
    // here DB request or some processing 
    $codeText = $param.'&id_order='.$param2.'&token='.$param3; 
     
    // end of processing here 
    $debugLog = ob_get_contents(); 
    ob_end_clean(); 
     
    // outputs image directly into browser, as PNG stream 
    QRcode::png($codeText,false, QR_ECLEVEL_L, 2, 1);
    