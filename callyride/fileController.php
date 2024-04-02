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
                $result = [
                    'ACCESS_CODE' => 'GRANTED',
                    'msg' => "Sorry, something went wrong."
                ];
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

            $front = $helper->imagesUpload($_FILES['front'],'', 600, 600);
            $back = $helper->imagesUpload($_FILES['back'], '1', 600, 600);
            $driverid = CommonFunctions::clean($_POST['driverid']);
            $data = [
                "frontView" => $front,
                "backView" => $back,
            ];
            if ($helper->update('manage_drivers', $data, ["driver_id" => $driverid])) {
                $result = [
                    'ACCESS_CODE' => 'GRANTED',
                    'msg' => "Sorry, something went wrong."
                ];
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

            $front = $helper->imagesUpload($_FILES['front'],'', 600, 600);
            $back = $helper->imagesUpload($_FILES['back'], '1', 600, 600);
            $engine = $helper->imagesUpload($_FILES['engine'], '2', 600, 600);
            $driverid = CommonFunctions::clean($_POST['driverid']);
            $data = [
                "engineView" => $front,
                "insideFront" => $back,
                "insideBack" => $engine,
            ];
            if ($helper->update('manage_drivers', $data, ["driver_id" => $driverid])) {
                $result = [
                    'ACCESS_CODE' => 'GRANTED',
                    'msg' => "Sorry, something went wrong."
                ];
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
