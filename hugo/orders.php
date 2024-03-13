<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require("./requires.php");

$userid = clean($_GET['userid']);
$status = clean($_GET['status']);
$id = clean($_GET['sid']);

if ($status == 'undelivered') {
    $status = "processed";
}
$res = [];

try {
    // Create a PDO connection
    $pdo = createPDOConnection();
    if ($status == "unpaid") {
        $sql = "SELECT * FROM `dinternational_order` di JOIN droutes dr ON di.dshipping_type=dr.rid JOIN dservices ds ON di.dservice_type = ds.sid WHERE di.dservice_type = :id AND di.dpayment='pending' AND di.dstatus!='Cancelled' AND userid=:userid ORDER BY di.id DESC LIMIT 20";
    } else {
        $sql = "SELECT * FROM `dinternational_order` di JOIN droutes dr ON di.dshipping_type=dr.rid JOIN dservices ds ON di.dservice_type = ds.sid WHERE di.dservice_type = :id AND di.dstatus=:status AND userid=:userid ORDER BY di.id DESC LIMIT 20";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Fetch Hugo address
            $hugoAddress = fetchHugoAddress($pdo, $row['daddressx']);

            // Fetch my address
            $myAddress = fetchMyAddress($pdo, $row['daddressx'], 'myaddress');

            // Get International Info
            //?get International Info
            $result = [ 
                "orderId"=> $row['orderId'],
                "userid"=> $row['userid'],
                "dshipping_type"=> $row['dshipping_type'],
                "dtitlex"=> $row['dtitlex'],
                "dservice_type"=> $row['dservice_type'],
                "paddress"=> $row['paddress'],
                "daddressx"=> $row['daddressx'],
                "dextra"=> $row['dextra'],
                "dtracking"=> $row['dtracking'],
                "dmanifest"=> $row['dmanifest'],
                "qty"=> $row['qty'], 
                "hugoAddress"=> $hugoAddress,
                "myAddress"=> $myAddress,
                "dtotalPack"=> $row['dtotalPack'],
                "dstatus"=> $row['dstatus'],
                "dadmin_comment"=> $row['dadmin_comment'],
                "dpayment"=> $row['dpayment'],
                "darrival"=> $row['darrival'],
                "ddelivery"=> $row['ddelivery'],
                "ddatex"=> date("d M, Y", strtotime($row['ddatex'])),
                "dcanReasons"=> $row['dcanReasons'],
                "dexRate"=> $row['dexRate'],
                "dcexRate"=> $row['dcexRate'],
                "dresquestStatus"=> $row['dresquestStatus'],
                "deta"=> $row['deta'],
                "detd"=> $row['detd'],
                "statusDate"=> $row['statusDate'],  
                "droute"=> $row['droute'],
                "sid"=> $row['sid'],
                "dservice"=> html_entity_decode($row['dservice']),
                "drate"=> $row['drate'],
                "dunit"=> $row['dunit'],
                "dcustom"=> $row['dcustom'] 
                
            ];
            $res[] = $result;
        }
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "An error occurred: " . $e->getMessage()]);
}

echo json_encode($res);

