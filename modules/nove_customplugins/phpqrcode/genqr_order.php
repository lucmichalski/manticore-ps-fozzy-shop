<?php 

    include('qrlib.php'); 
         
    $param = $_GET['id_order']; 
    QRcode::png($param,false, 15, 10, 0);
    
?>
    