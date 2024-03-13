<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require("./requires.php"); 
    $userid = clean($_GET['userid']);
    $status = clean($_GET['status']);
    $request = clean($_GET['request']);


    if($status=='undelivered'){
        $state = "processed";
    } else{
        $state = $status;
    }

    $res = [];
    if($request =="inter"){
        $int = runQuery("SELECT * FROM `dservices` WHERE dtype='International' ORDER BY dservice ");
        if(numRows($int)>0){
            while($ints = fetchAssoc($int)){
                $idxx = $ints['sid'];
                $totalRow =  $status !="unpaid"? numRows(runQuery("SELECT * FROM dinternational_order WHERE dstatus='$state' AND dservice_type='$idxx' AND userid='$userid'")): numRows(runQuery("SELECT * FROM dinternational_order WHERE dpayment='pending' AND dservice_type='$idxx' AND userid='$userid'"));
                $res[] = [
                    "res"=>[
                        'id'=>$ints['id'],
                        'sid'=>$ints['sid'],
                        'dtype'=>$ints['dtype'],
                        'drate'=>$ints['drate'],
                        'dservice'=>html_entity_decode($ints['dservice']),
                        'dunit'=>$ints['dunit'],
                        'dcustom'=>$ints['dcustom'],
                        'dimg'=>$ints['dimg'],
                    ], 
                    "totalRow"=>$totalRow];
                
            }
            // $data = [$res];
        }
    } 

    echo json_encode($res);

 