<?php


function createPDOConnection() {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=express", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}


function getAdminRate(PDO $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT drate FROM admin");
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        return $admin['drate'];
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false; // Or handle the error as needed
    }
}
 
function fetchHugoAddress(PDO $pdo, $aid) {
    $query = $pdo->prepare("SELECT * FROM daddress WHERE aid=:aid");
    $query->bindParam(':aid', $aid, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        $address = $query->fetch(PDO::FETCH_ASSOC);
        return [
            "title"=>$address['dtitle'],
            "phone"=>$address['dphone'],
            "address"=>$address['daddress'],
        ];
    } else {
        return [
            "title"=>"",
            "phone"=>"",
            "address"=>"",
        ];
    }
}

function fetchMyAddress(PDO $pdo, $aid) {
    $query = $pdo->prepare("SELECT * FROM myaddress WHERE aid=:aid");
    $query->bindParam(':aid', $aid, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        $address = $query->fetch(PDO::FETCH_ASSOC);
        return [
            "name"=>$address['dname'],
            "location"=>$address['dlocation'],
            "address"=>$address['daddress'],
            "city"=>$address['dcity'],
            "state"=>$address['dstate']
        ];
    } else {
        return [
            "name"=>"",
            "location"=>"",
            "address"=>"",
            "city"=>"",
            "state"=>""
        ];
    }
}