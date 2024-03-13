<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
require("./requires.php"); 
    $userid = clean($_GET['userid']);

    $pending = numRows(runQuery("SELECT * FROM dinternational_order WHERE dstatus='pending' AND userid='$userid' "));

    $delivered = numRows(runQuery("SELECT * FROM dinternational_order WHERE dstatus='Delivered' AND userid='$userid' "));

    $processed = numRows(runQuery("SELECT * FROM dinternational_order WHERE dstatus='Processed' AND userid='$userid' "));

    $unpaid = numRows(runQuery("SELECT * FROM dinternational_order WHERE dpayment='pending' AND userid='$userid' "));

    $cancelled = numRows(runQuery("SELECT * FROM dinternational_order WHERE dstatus='Cancelled' AND userid='$userid' "));

   
    $docPending = numRows(runQuery("SELECT * FROM ddomestic_order WHERE sstatus='pending' AND userid='$userid' "));
    $docDelivered = numRows(runQuery("SELECT * FROM ddomestic_order WHERE sstatus='delivered' AND userid='$userid' "));
    $docProcessed =  numRows(runQuery("SELECT * FROM ddomestic_order WHERE sstatus='Processed' AND userid='$userid' "));
    $docUnpaid = numRows(runQuery("SELECT * FROM ddomestic_order WHERE dpaymentStatus='pending' AND userid='$userid' "));
    $docCancelled = numRows(runQuery("SELECT * FROM ddomestic_order WHERE sstatus='cancelled' AND userid='$userid' "));

    $res1 = [
        "intPending" => $pending,
        "intDelivered" => $delivered,
        "intProcessed" => $processed,
        "intUnpaid" => $unpaid,
        "intCancelled" => $cancelled,
        //?domestic
        "docPending" => $docPending,
        "docDelivered" => $docDelivered,
        "docProcessed" => $docProcessed,
        "docUnpaid" => $docUnpaid,
        "docCancelled" => $docCancelled,
    ]; 

    $resRow = [ $res1 ];

    
    echo json_encode($resRow);