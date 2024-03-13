<?php

class InternationalOrderAPI
{
    private $connection; // Instance of the Connection class
    private $pdo;
    private $uniqueX;
    private $trackingId;
    private $date;
    private $transid;
    private $adminRate;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->pdo = $this->connection->getPDO();
        $this->uniqueX = $this->connection->getUniqueX();
        $this->trackingId = $this->connection->getTrackingId();
        $this->date = $this->connection->getDate();
        $this->transid = $this->connection->getTransid();
        $this->adminRate = $this->connection->getAdminRate();
    }

    public function createOrUpdateOrder()
    {
        // Retrieve and sanitize POST data
        $userid = $this->clean($_POST['userid']);
        $type = $this->clean($_POST['shipping']); //Shipping type
        $title = $this->clean($_POST['title']);
        $service = $this->clean($_POST['service']);
        $address = $this->clean($_POST['delivery']);
        $paddress = $this->clean($_POST['pickup']);
        $comment = $this->clean($_POST['extra']);
        $pairs = json_decode($_POST['pairs'], true);


        // Retrieve user details
        $use = $this->getUserDetails($userid);
        $name = $use['dfname'];
        $phone = $use['dphone'];
        $email = $use['demail'];

        if ($_POST['internationalOrder'] == "Create") {
            // Insert international order and associated waybills
            $orderId = $this->insertInternationalOrder($userid, $name, $phone, $email, $type, $title, $service, $address, $paddress, $comment, $pairs);

            $this->insertInvoice($orderId);
            $this->sendUserEmail($orderId);
            $this->sendAdminEmail($orderId);

            // print $this->getOrderData($orderId);
        } else {

            // Run update here
            $sql = $this->updateInternationalOrder($this->uniqueX, $userid, $name, $phone, $email, $type, $title, $service, $address, $paddress, $comment, $pairs);
        }
    }

    private function clean($value)
    {
        $value = trim($value);
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $value = strip_tags($value);
        return $value;
    }

    private function getUserDetails($userid)
    {
        $stmt = $this->pdo->prepare("SELECT demail, dfname, dphone FROM dlogin WHERE userid = :userid");
        $stmt->bindParam(':userid', $userid);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function insertInternationalOrder($userid, $name, $phone, $email, $type, $title, $service, $address, $paddress, $comment, $pairs)
    {
        $stmt = $this->pdo->prepare("INSERT INTO dinternational_order  SET orderId=:orderId, userid=:userid, dfname=:dfname, dphone=:dphone, demail=:demail, dshipping_type=:dshipping_type, dtitlex=:dtitlex, dservice_type=:dservice_type, daddressx=:daddressx, dextra=:dextra, ddatex=:ddatex, dtracking=:dtracking, paddress=:paddress");

        $stmt->bindParam(':orderId', $this->uniqueX);
        $stmt->bindParam(':userid', $userid);
        $stmt->bindParam(':dfname', $name);
        $stmt->bindParam(':dphone', $phone);
        $stmt->bindParam(':demail', $email);
        $stmt->bindParam(':dshipping_type', $type);
        $stmt->bindParam(':dtitlex', $title);
        $stmt->bindParam(':dservice_type', $service);
        $stmt->bindParam(':daddressx', $address);
        $stmt->bindParam(':dextra', $comment);
        $stmt->bindParam(':ddatex', $this->date);
        $stmt->bindParam(':dtracking', $this->trackingId);
        $stmt->bindParam(':paddress', $paddress);

        if ($stmt->execute()) {
            $orderId =  $this->uniqueX; // Retrieve the new order ID
            foreach ($pairs as $pair) {
                $waybill = $pair['waybill'];
                $courier = $pair['courier'];
                $qty = $pair['qty'];
                $newCode = md5(bin2hex(random_bytes(11)) . $this->transid);
                // Insert waybill details here using $orderId
                $this->insertWaybill($newCode, $orderId, $waybill, $courier, $qty);
            }
           
            return $this->uniqueX;
        } else {
            return false;
        }
    }

    private function updateInternationalOrder($orderId, $userid, $name, $phone, $email, $type, $title, $service, $address, $paddress, $comment, $pairs)
    {
        $stmt = $this->pdo->prepare("UPDATE international_orders SET orderId = :orderId, userid = :userid, dfname = :name, dphone = :phone, demail = :email, dshipping_type = :type, dtitlex = :title, dservice_type = :service, daddressx = :delivery, dpickup = :pickup, dextra = :extra WHERE orderId = :orderId");
        $stmt->bindParam(':orderId', $uniqueX, PDO::PARAM_STR);
        $stmt->bindParam(':userid', $userid);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':service', $service);
        $stmt->bindParam(':delivery', $address);
        $stmt->bindParam(':pickup', $paddress);
        $stmt->bindParam(':extra', $comment);
        $stmt->bindParam(':orderId', $orderId);

        $updateResult = $stmt->execute();

        // Query to fetch wid based on your criteria
        $stmt = $this->pdo->prepare("SELECT wid FROM dwaybills WHERE orderId = :orderId ");
        $stmt->bindParam(':orderId', $orderId);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize an array to store retrieved wids
        $wids = [];

        // Check if rows were found
        if ($results) {
            foreach ($results as $result) {
                $wids[] = $result['wid'];
            }
        }

        // Now, you have an array $wids containing all matching wid values
        foreach ($pairs as $pair) {
            $waybill = $pair['waybill'];
            $courier = $pair['courier'];
            $qty = $pair['qty'];

            // Loop through the retrieved wids and perform updates or inserts
            foreach ($wids as $wid) {
                // Check if a waybill with this orderId and wid exists
                $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM dwaybills WHERE orderId = :orderId AND wid = :wid");
                $stmt->bindParam(':orderId', $orderId);
                $stmt->bindParam(':wid', $wid);
                $stmt->execute();
                $waybillCount = $stmt->fetchColumn();

                if ($waybillCount > 0) {
                    // Update the existing waybill records for this orderId and wid
                    $this->updateWaybill($wid, $orderId, $waybill, $courier, $qty);
                } else {
                    // Insert a new waybill for this orderId
                    $wid = md5(bin2hex(random_bytes(11)) . $this->transid);
                    $this->insertWaybill($wid, $orderId, $waybill, $courier, $qty);
                }
            }
        }


        return $updateResult;
    }



    private function insertWaybill($wid, $orderId, $waybill, $courier, $qty)
    {
        $stmt = $this->pdo->prepare("INSERT INTO dwaybills SET wid=:wid, orderId=:orderId, dwaybill=:dwaybill, dcourier=:dcourier, dqty=:dqty");
        $stmt->bindParam(':wid', $wid);
        $stmt->bindParam(':orderId', $orderId);
        $stmt->bindParam(':dwaybill', $waybill);
        $stmt->bindParam(':dcourier', $courier);
        $stmt->bindParam(':dqty', $qty);
        return $stmt->execute();
    }

    private function updateWaybill($wid, $orderId, $waybill, $courier, $qty)
    {
        $stmt = $this->pdo->prepare("UPDATE dwaybills SET dwaybill=:waybill, dcourier = :courier, dqty = :qty WHERE orderId = :orderId AND wid = :wid");
        $stmt->bindParam(':orderId', $orderId);
        $stmt->bindParam(':wid', $wid);
        $stmt->bindParam(':waybill', $waybill);
        $stmt->bindParam(':courier', $courier);
        $stmt->bindParam(':qty', $qty);
        $stmt->execute();
    }


    private function insertInvoice($orderId)
    {
        $stmt = $this->pdo->prepare("INSERT INTO dinvoice SET orderId=:orderId");
        $stmt->bindParam(':orderId', $orderId);
        return $stmt->execute();
    }


    private function sendUserEmail($orderId)
    {
        // Implement sending an email to the user
    }

    private function sendAdminEmail($orderId)
    {
        // Implement sending an email to the admin
    }


    private function getOrderData($orderId)
    {

        $res = [];

        try {

            $sql = "SELECT * FROM `dinternational_order` di JOIN droutes dr ON di.dshipping_type=dr.rid JOIN dservices ds ON di.dservice_type = ds.sid WHERE di.orderId=:orderId";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':orderId', $orderId, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // Fetch Hugo address
                    $hugoAddress = $this->fetchHugoAddress($row['daddressx']);

                    // Fetch my address
                    $myAddress = $this->fetchMyAddress($row['daddressx'], 'myaddress');

                    // ?get International Info
                    $result = [
                        "orderId" => $row['orderId'],
                        "userid" => $row['userid'],
                        "dshipping_type" => $row['dshipping_type'],
                        "dtitlex" => $row['dtitlex'],
                        "dservice_type" => $row['dservice_type'],
                        "paddress" => $row['paddress'],
                        "daddressx" => $row['daddressx'],
                        "dextra" => $row['dextra'],
                        "dtracking" => $row['dtracking'],
                        "dmanifest" => $row['dmanifest'],
                        "qty" => $row['qty'],
                        "hugoAddress" => $hugoAddress,
                        "myAddress" => $myAddress,
                        "dtotalPack" => $row['dtotalPack'],
                        "dstatus" => $row['dstatus'],
                        "dadmin_comment" => $row['dadmin_comment'],
                        "dpayment" => $row['dpayment'],
                        "darrival" => $row['darrival'],
                        "ddelivery" => $row['ddelivery'],
                        "ddatex" => date("d M, Y", strtotime($row['ddatex'])),
                        "dcanReasons" => $row['dcanReasons'],
                        "dexRate" => $row['dexRate'],
                        "dcexRate" => $row['dcexRate'],
                        "dresquestStatus" => $row['dresquestStatus'],
                        "deta" => $row['deta'],
                        "detd" => $row['detd'],
                        "statusDate" => $row['statusDate'],
                        "droute" => $row['droute'],
                        "sid" => $row['sid'],
                        "dservice" => html_entity_decode($row['dservice']),
                        "drate" => $row['drate'],
                        "dunit" => $row['dunit'],
                        "dcustom" => $row['dcustom']
                    ];
                    $res[] = $result;
                }
            }
        } catch (PDOException $e) {
            echo json_encode(["error" => "An error occurred: " . $e->getMessage()]);
        }

        return json_encode($res);
    }


    private function fetchMyAddress($aid)
    {
        $query = $this->pdo->prepare("SELECT * FROM myaddress WHERE aid=:aid");
        $query->bindParam(':aid', $aid, PDO::PARAM_STR);
        $query->execute();
        $result = [];

        if ($query->rowCount() > 0) {
            $address = $query->fetch(PDO::FETCH_ASSOC);
            $result = [
                "name" => $address['dname'],
                "location" => $address['dlocation'],
                "address" => $address['daddress'],
                "city" => $address['dcity'],
                "state" => $address['dstate']
            ];
        }
        return $result;
    }

    private function fetchHugoAddress($aid)
    {
        $query = $this->pdo->prepare("SELECT * FROM daddress WHERE aid=:aid");
        $query->bindParam(':aid', $aid, PDO::PARAM_STR);
        $query->execute();
        $result = [];

        if ($query->rowCount() > 0) {
            $address = $query->fetch(PDO::FETCH_ASSOC);
            $result = [
                "title" => $address['dtitle'],
                "phone" => $address['dphone'],
                "address" => $address['daddress'],
            ];
        }
        return $result;
    }
}
