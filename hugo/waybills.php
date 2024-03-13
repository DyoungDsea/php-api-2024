<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include your database connection and other required files here
require_once("./requires.php"); 
// $admin = runQuery("SELECT drate FROM admin")->fetch_assoc();
  // Establish a database connection using PDO
  $pdo = createPDOConnection();
 
    $adminRate = getAdminRate($pdo);

// Function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

try {
  
    

    // Sanitize input parameters
    $orderId = sanitizeInput($_GET['orderId']);

    // Waybill received
    $waybills = [];
    $waybillQuery = $pdo->prepare("SELECT * FROM dwaybills WHERE orderId = :orderId");
    $waybillQuery->bindParam(':orderId', $orderId, PDO::PARAM_STR);
    $waybillQuery->execute();

    if ($waybillQuery->rowCount() > 0) {
        while ($bill = $waybillQuery->fetch(PDO::FETCH_ASSOC)) {
            $billData = [
                "waybill" => $bill['dwaybill'],
                "courier" => $bill['dcourier'],
                "qty" => $bill['dqty'],
                "received" => $bill['dreceived'],
                "date" => ($bill['dreceived'] == 'yes') ? date("d M, Y | H:s", strtotime($bill['wdate'])) : 'Null',
            ];
            $waybills[] = $billData;
        }
    }

    // Send waybill
    $sender = [];
    $sendQuery = $pdo->prepare("SELECT dw.dwaybill, dw.dcourier, dw.dqty, dw.dreceived, ds.sdate FROM dwaybills dw JOIN dsend ds ON dw.dwaybill=ds.wid WHERE ds.orderId = :orderId");
    $sendQuery->bindParam(':orderId', $orderId, PDO::PARAM_STR);
    $sendQuery->execute();

    if ($sendQuery->rowCount() > 0) {
        while ($bills = $sendQuery->fetch(PDO::FETCH_ASSOC)) {
            $waybillSend = [
                "waybill" => $bills['dwaybill'],
                "courier" => $bills['dcourier'],
                "qty" => $bills['dqty'],
                "received" => $bills['dreceived'],
                "date" => date("d M, Y | H:s", strtotime($bills['sdate'])),
            ];
            $sender[] = $waybillSend;
        }
    }

    // Tracking status
    $tracking = [];
    $trackQuery = $pdo->prepare("SELECT * FROM dtracking_list WHERE orderId = :orderId");
    $trackQuery->bindParam(':orderId', $orderId, PDO::PARAM_STR);
    $trackQuery->execute();

    if ($trackQuery->rowCount() > 0) {
        while ($tracks = $trackQuery->fetch(PDO::FETCH_ASSOC)) {
            $status = [
                "title" => $tracks['dtitle'],
                "location" => $tracks['dlocation'],
                "comment" => $tracks['dcomment'],
                "date" => date("d M Y | H:i", strtotime($tracks['ddate'])),
            ];
            $tracking[] = $status;
        }
    }

    // Invoice details
    $invoice = [];
    $volume = 0;
    $inQuery = $pdo->prepare("SELECT ds.drate, di.dexRate, di.dcexRate, ds.dcustom, ds.dunit, dv.dvolume, dv.dcustom, dv.ddiscount FROM dinternational_order di JOIN dinvoice dv ON di.orderId = dv.orderId JOIN droutes dr ON di.dshipping_type=dr.rid JOIN dservices ds ON di.dservice_type = ds.sid WHERE dv.orderId = :orderId");
    $inQuery->bindParam(':orderId', $orderId, PDO::PARAM_STR);
    $inQuery->execute();

    if ($inQuery->rowCount() > 0) {
        $in = $inQuery->fetch(PDO::FETCH_ASSOC);
        $volume = $in['dvolume'];
        $totalUSD = (empty($in['dexRate']) ? $in['drate'] : $in['dexRate']) * $volume;
        $totalNGN = $adminRate * $totalUSD;
        $cvolume = $in['dcustom'];
        $totalCNGN = ($cvolume * (empty($in['dcexRate']) ? $in['dcustom'] : $in['dcexRate']));
        $disc = $in['ddiscount'];
        $finalTotal = $totalNGN + $totalCNGN;
        $discount = $disc != 0 ? $in['ddiscount'] : 0;
        $finalTotal = $finalTotal - $discount;

        $invoice[] = [
            "volume" => $volume,
            "rate" => empty($in['dexRate']) ? $in['drate'] : $in['dexRate'] . " USD/" . $in['dunit'],
            "totalInDollar" => "$" . number_format($totalUSD, 2),
            "lineTotal" => "₦" . number_format($totalNGN, 2),
            "cvolume" => $cvolume,
            "crate" => "NGN/" . $in['dunit'],
            "ctotal" => "₦" . number_format($totalCNGN, 2),
            "clineTotal" => "₦" . number_format($totalCNGN, 2),
            "discount" => "$discount",
            "finalNaira" => "₦" . number_format($finalTotal, 2),
            "finalTotal" => number_format($finalTotal, 2),
        ];
    }

    // Additional charges
    $amount = 0;
    $charges = [];
    $addQuery = $pdo->prepare("SELECT * FROM daddtional_charges WHERE orderId = :orderId");
    $addQuery->bindParam(':orderId', $orderId, PDO::PARAM_STR);
    $addQuery->execute();

    if ($addQuery->rowCount() > 0) {
        while ($adds = $addQuery->fetch(PDO::FETCH_ASSOC)) {
            $amt = $adds['damount'];
            $amount += $amt;
            $res = [
                'reason' => $adds['dreasons'],
                'amount' => $adds['damount'],
            ];
            $charges[] = $res;
        }
    }

    $finalResTotal = number_format($finalTotal + $amount, 2);

    echo json_encode([
        "invoice" => $invoice,
        "charges" => $charges,
        "amount" => "₦ $finalResTotal",
        "received" => $waybills,
        "sender" => $sender,
        "tracking" => $tracking,
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "An error occurred: " . $e->getMessage()]);
}
