<?php
require '../image_php/class.upload.php';
require_once './require.php';



//TODO: POST REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['Message']) and $_POST['Message'] == 'licence') {
        $result = [];
        if (!empty($_FILES['front']['name']) and !empty($_FILES['back']['name']) and !empty($_FILES['passport']['name'])) {

            $front = $helper->imagesUpload($_FILES['front']);
            $back = $helper->imagesUpload($_FILES['back'], '1');
            $passport = $helper->imagesUpload($_FILES['passport'], '2', 200, 200);
            $driverid = CommonFunctions::clean($_POST['driverid']);
            $data = [
                "driverLicenceFront" => $front,
                "driverLicenceBack" => $back,
                "driver_photo" => $passport,
            ];
            if ($helper->update('manage_drivers', $data, ["driver_id" => $driverid])) {
                $user = $helper->getSingleRecord('manage_drivers', "WHERE driver_id = '$driverid'");

                if (!empty($user)) {
                    $result = [
                        "driverId" => $user["driver_id"],
                        "driverName" => $user["driver_name"],
                        "phoneNumber" => $user["phone_number"],
                        "emailAddress" => $user["email_address"],
                        "contactAddress" => empty($user["daddress"]) ? "" : $user["daddress"],
                        "licenseNumber" => empty($user["licenseNumber"]) ? "" : $user["licenseNumber"],
                        "frontView" => empty($user["frontView"]) ? "" : $user["frontView"],
                        "lastupdate" => empty($user["lastupdate"]) ? "" : $user["lastupdate"],
                        "walletBalance" => $user["wallet_balance"],
                        "carVerification" => $user["carVerification"],
                        "driverLicenceFront" => !empty($user["driverLicenceFront"]) ? $user["driverLicenceFront"] : "",
                        "engineView" => !empty($user["engineView"]) ? $user["engineView"] : ""

                    ];
                }
            } else {
                http_response_code(400);
                $result = [
                    'ACCESS_CODE' => 'DENIED',
                    'msg' => "Sorry, something went wrong."
                ];
            }
        }

        echo json_encode($result);
    }



    if (isset($_POST['Message']) and $_POST['Message'] == 'frontback') {
        $result = [];
        if (!empty($_FILES['front']['name']) and !empty($_FILES['back']['name'])) {

            $front = $helper->imagesUpload($_FILES['front'], '', 600, 600);
            $back = $helper->imagesUpload($_FILES['back'], '1', 600, 600);
            $driverid = CommonFunctions::clean($_POST['driverid']);
            $data = [
                "frontView" => $front,
                "backView" => $back,
            ];
            if ($helper->update('manage_drivers', $data, ["driver_id" => $driverid])) {
                $user = $helper->getSingleRecord('manage_drivers', "WHERE driver_id = '$driverid'");

                if (!empty($user)) {
                    $result = [
                        "driverId" => $user["driver_id"],
                        "driverName" => $user["driver_name"],
                        "phoneNumber" => $user["phone_number"],
                        "emailAddress" => $user["email_address"],
                        "contactAddress" => empty($user["daddress"]) ? "" : $user["daddress"],
                        "licenseNumber" => empty($user["licenseNumber"]) ? "" : $user["licenseNumber"],
                        "frontView" => empty($user["frontView"]) ? "" : $user["frontView"],
                        "lastupdate" => empty($user["lastupdate"]) ? "" : $user["lastupdate"],
                        "walletBalance" => $user["wallet_balance"],
                        "carVerification" => $user["carVerification"],
                        "driverLicenceFront" => !empty($user["driverLicenceFront"]) ? $user["driverLicenceFront"] : "",
                        "engineView" => !empty($user["engineView"]) ? $user["engineView"] : ""

                    ];
                }
            } else {
                http_response_code(400);
                $result = [
                    'ACCESS_CODE' => 'DENIED',
                    'msg' => "Sorry, something went wrong."
                ];
            }
        }

        echo json_encode($result);
    }

    if (isset($_POST['Message']) and $_POST['Message'] == 'restFile') {
        $result = [];
        if (!empty($_FILES['front']['name']) and !empty($_FILES['back']['name']) and !empty($_FILES['engine']['name'])) {

            $front = $helper->imagesUpload($_FILES['front'], '', 600, 600);
            $back = $helper->imagesUpload($_FILES['back'], '1', 600, 600);
            $engine = $helper->imagesUpload($_FILES['engine'], '2', 600, 600);
            $driverid = CommonFunctions::clean($_POST['driverid']);
            $data = [
                "engineView" => $front,
                "insideFront" => $back,
                "insideBack" => $engine,
            ];
            if ($helper->update('manage_drivers', $data, ["driver_id" => $driverid])) {
                $user = $helper->getSingleRecord('manage_drivers', "WHERE driver_id = '$driverid'");

                if (!empty($user)) {
                    $result = [
                        "driverId" => $user["driver_id"],
                        "driverName" => $user["driver_name"],
                        "phoneNumber" => $user["phone_number"],
                        "emailAddress" => $user["email_address"],
                        "contactAddress" => empty($user["daddress"]) ? "" : $user["daddress"],
                        "licenseNumber" => empty($user["licenseNumber"]) ? "" : $user["licenseNumber"],
                        "frontView" => empty($user["frontView"]) ? "" : $user["frontView"],
                        "lastupdate" => empty($user["lastupdate"]) ? "" : $user["lastupdate"],
                        "walletBalance" => $user["wallet_balance"],
                        "carVerification" => $user["carVerification"],
                        "driverLicenceFront" => !empty($user["driverLicenceFront"]) ? $user["driverLicenceFront"] : "",
                        "engineView" => !empty($user["engineView"]) ? $user["engineView"] : ""

                    ];
                }
            } else {
                http_response_code(400);
                $result = [
                    'ACCESS_CODE' => 'DENIED',
                    'msg' => "Sorry, something went wrong."
                ];
            }
        }

        echo json_encode($result);
    }
}
