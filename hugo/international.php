<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require("./requires.php");

try {
    // Create a PDO instance
    $pdo = createPDOConnection();

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        // Sanitize and retrieve POST data
        $type = clean($_POST['type']); // Shipping type
        $title = clean($_POST['title']);
        $service = clean($_POST['service']);
        $address = clean($_POST['address']);
        $paddress = clean($_POST['paddress']);
        $comment = clean($_POST['comment']);

        $dateArrive = clean($_POST['dateArrive']);
        $dateDelivery = clean($_POST['dateDelivery']);

        $link = clean($_POST['link']);
        $waybillNumber = $_POST['waybillNumber'];
        $courierName = $_POST['courierName'];
        $qty = $_POST['qty'];

        //** get user details */
        $use = runQuery("SELECT demail, dfname, dphone FROM dlogin WHERE userid='$userid'")->fetch_assoc();
        $name = $use['dfname'];
        $phone = $use['dphone'];
        $email = $use['demail'];

        if ($_POST['internationalOrder'] == "Create") {
            foreach ($waybillNumber as $key => $waybill) {
                $newCode = md5(bin2hex(random_bytes(11)) . $transid);
                $waybill = $waybill;
                $courier = $courierName[$key];
                $qtyRes = $qty[$key];

                // Prepare and execute the SQL statement to insert details into the waybill table
                $stmt = $pdo->prepare("INSERT INTO dwaybills (wid, orderId, dwaybill, dcourier, dqty) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$newCode, $uniqueX, $waybill, $courier, $qtyRes]);
            }

            // Prepare and execute the SQL statement to insert details into the international_order table
            $stmt = $pdo->prepare("INSERT INTO dinternational_order (orderId, userid, dfname, dphone, demail, dshipping_type, dtitlex, dservice_type, daddressx, dextra, ddatex, dtracking, paddress) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$uniqueX, $userid, $name, $phone, $email, $type, $title, $service, $address, $comment, $date, $trackingId, $paddress]);

            if ($stmt->rowCount() > 0) {
                // Insert successful
                runQuery("INSERT INTO dinvoice SET orderId='$uniqueX'");
                $_SESSION['msgs'] = 'Order created successfully';
                $link = "order/$uniqueX/";

                //** user Email */
                $test = $testOrder;
                include $testOrderApi;
                //** Admin Email */
                $test = $testAdmin;
                include $testAdminApi;
            } else {
                $_SESSION['msg'] = 'Failed to create the order. Please try again later.';
            }
        } else {
            $uniqueX = clean($_POST['id']);

            $waybillNum = $_POST['waybillNum'];
            $courierNam = $_POST['courierNam'];

            // Insert new waybills here
            foreach ($waybillNum as $key => $waybill) {
                $newCode = md5(bin2hex(random_bytes(11)) . $transid);
                $courier = $courierNam[$key];
                $qtyRes = $qty[$key];

                // Prepare and execute the SQL statement to insert details into the waybill table
                $stmt = $pdo->prepare("INSERT INTO dwaybills (wid, orderId, dwaybill, dcourier, dqty) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$newCode, $uniqueX, $waybill, $courier, $qtyRes]);
            }

            // Update old waybills if any
            foreach ($waybillNumber as $key => $waybillOld) {
                $courierOld = $courierName[$key];
                $qtyResOld = $qty[$key];

                // Prepare and execute the SQL statement to update the waybill table
                $stmt = $pdo->prepare("UPDATE dwaybills SET dwaybill=?, dcourier=?, dqty=? WHERE wid=? AND orderId=?");
                $stmt->execute([$waybillOld, $courierOld, $qtyResOld, $key, $uniqueX]);
            }

            // Prepare and execute the SQL statement to update the international_order table
            $stmt = $pdo->prepare("UPDATE dinternational_order SET userid=?, dfname=?, dphone=?, demail=?, dshipping_type=?, dtitlex=?, dservice_type=?, daddressx=?, dextra=?, ddatex=?, paddress=? WHERE orderId=?");
            $stmt->execute([$userid, $name, $phone, $email, $type, $title, $service, $address, $comment, $date, $paddress, $uniqueX]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['msgs'] = 'Order updated successfully';
            } else {
                $_SESSION['msg'] = 'Failed to update the order. Please try again later.';
            }
        }

        
    }
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
