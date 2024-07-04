<?php
// require '../image_php/class.upload.php';
require_once './require.php';



//TODO: POST REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['Message']) and $_POST['Message'] == 'licence') {
        $result = [];
        if (!empty($_FILES['front']['name']) and !empty($_FILES['back']['name']) and !empty($_FILES['passport']['name'])) {

            // $front = $helper->imagesUpload($_FILES['front']);
            // $back = $helper->imagesUpload($_FILES['back'], '1');
            // $passport = $helper->imagesUpload($_FILES['passport'], '2', 200, 200);

            $front = $uploader->uploadImage($_FILES['front']);
            $back = $uploader->uploadImage($_FILES['back'], 1);
            $passport = $uploader->uploadImage($_FILES['passport'], 2, 600);

            $driverid = CommonFunctions::clean($_POST['driverid']);
            $data = [
                "driverLicenceFront" => str_replace('../', '', $front),
                "driverLicenceBack" => str_replace('../', '', $back),
                "driver_photo" => str_replace('../', '', $passport,),
            ];
            if ($helper->update('manage_drivers', $data, ["driver_id" => $driverid])) {
                $user = $helper->getSingleRecord('manage_drivers', "WHERE driver_id = '$driverid'");

                if (!empty($user)) {
                    include './result.php';
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

            // $front = $helper->imagesUpload($_FILES['front'], '', 600, 600);
            // $back = $helper->imagesUpload($_FILES['back'], '1', 600, 600);

            $front = $uploader->uploadImage($_FILES['front']);
            $back = $uploader->uploadImage($_FILES['back'], 1);

            $driverid = CommonFunctions::clean($_POST['driverid']);
            $data = [
                "frontView" => str_replace('../', '', $front),
                "backView" => str_replace('../', '', $back),
            ];
            if ($helper->update('manage_drivers', $data, ["driver_id" => $driverid])) {
                $user = $helper->getSingleRecord('manage_drivers', "WHERE driver_id = '$driverid'");

                if (!empty($user)) {
                    include './result.php';
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


            $front = $uploader->uploadImage($_FILES['front']);
            $back = $uploader->uploadImage($_FILES['back'], 1);
            $engine = $uploader->uploadImage($_FILES['engine'], 2);

            // $front = $helper->imagesUpload($_FILES['front'], '', 600, 600);
            // $back = $helper->imagesUpload($_FILES['back'], '1', 600, 600);
            // $engine = $helper->imagesUpload($_FILES['engine'], '2', 600, 600);


            $driverid = CommonFunctions::clean($_POST['driverid']);
            $data = [
                "engineView" => str_replace('../', '', $front),
                "insideFront" => str_replace('../', '', $back),
                "insideBack" => str_replace('../', '', $engine),
            ];
            if ($helper->update('manage_drivers', $data, ["driver_id" => $driverid])) {
                $user = $helper->getSingleRecord('manage_drivers', "WHERE driver_id = '$driverid'");

                if (!empty($user)) {
                    include './result.php';
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
