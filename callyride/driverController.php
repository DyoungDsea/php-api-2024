<?php
require_once './require.php';
$jsonData = json_decode(file_get_contents("php://input"));

//TODO: POST REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    //TODO: REGISTER
    if (isset($jsonData->Message) and $jsonData->Message == 'register') {

        $fname = CommonFunctions::clean($jsonData->fname);
        $lname = CommonFunctions::clean($jsonData->lname);
        $phone = CommonFunctions::clean($jsonData->phone);
        $email = CommonFunctions::clean($jsonData->email);
        $pass = CommonFunctions::clean($jsonData->pass);

        $data = [
            "driver_name" => "$fname $lname",
            "phone_number" => $phone,
            "email_address" => $email,
            "password" => md5($pass)
        ];

        echo json_encode($modelDriver->createNewDriver($email, $phone, $data));
    }

    //TODO: lOGIN
    if (isset($jsonData->Message) and $jsonData->Message == 'login') {
        $user = CommonFunctions::clean($jsonData->user);
        $pass = CommonFunctions::clean($jsonData->pass);
        echo json_encode($modelDriver->login($user, $pass));
    }
}


//TODO: PUT REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    //TODO: RESET PASSWORD
    if (isset($jsonData->Message) and $jsonData->Message == 'resetPassword') {
        $token1 = CommonFunctions::clean($jsonData->token1);
        $token2 = CommonFunctions::clean($jsonData->token2);
        $token3 = CommonFunctions::clean($jsonData->token3);
        $token4 = CommonFunctions::clean($jsonData->token4);
        $pass = CommonFunctions::clean($jsonData->pass);
        $email = CommonFunctions::clean($jsonData->email);

        $token = "$token1$token2$token3$token4";

        echo json_encode($modelDriver->resetPassword($email, $token, $pass));
    }


    //TODO: CHANGE PASSWORD
    if (isset($jsonData->Message) and $jsonData->Message == 'changePassword') {

        $current = CommonFunctions::clean($jsonData->current);
        $pass = CommonFunctions::clean($jsonData->pass);
        $userid = CommonFunctions::clean($jsonData->userid);

        echo json_encode($modelDriver->changePassword($userid, $current, $pass));
    }

    //TODO: CHANGE PASSWORD
    if (isset($jsonData->Message) and $jsonData->Message == 'driverLatLong') {

        $lat = CommonFunctions::clean($jsonData->lat);
        $long = CommonFunctions::clean($jsonData->long);
        $userid = CommonFunctions::clean($jsonData->userid);
        $data = [
            "driver_latitude" => $lat,
            "driver_longitude" => $long
        ];

        echo json_encode($modelDriver->driverLatLong($data, ["driver_id" => $userid]));
    }

    //TODO: UPDATE PROFILE
    if (isset($jsonData->Message) and $jsonData->Message == 'updateDriver') {

        $fullname = CommonFunctions::clean($jsonData->fullname);
        $phone = CommonFunctions::clean($jsonData->phone);
        $email = CommonFunctions::clean($jsonData->email);
        $userid = CommonFunctions::clean($jsonData->userid);

        $data = [
            "driver_name" => $fullname,
            "phone_number" => $phone,
            "email_address" => $email
        ];

        echo json_encode($modelDriver->updateDriver($data, ["driver_id" => $userid]));
    }

    //TODO: UPDATE VEHICLE DETAILS
    if (isset($jsonData->Message) and $jsonData->Message == 'vehicleDetails') {

        $license = CommonFunctions::clean($jsonData->license);
        $nin = CommonFunctions::clean($jsonData->nin);
        $plateNo = CommonFunctions::clean($jsonData->plateNo);
        $color = CommonFunctions::clean($jsonData->color);
        $enginNo = CommonFunctions::clean($jsonData->enginNo);
        $desc = htmlentities($jsonData->desc);
        $userid = CommonFunctions::clean($jsonData->userid);

        $data = [
            "nin" => $nin,
            "carDesc" => $desc,
            "carColor" => $color,
            "plateNunmber" => $plateNo,
            "engineNumber" => $enginNo,
            "licenseNumber" => $license,
        ];

        echo json_encode($modelDriver->updateVehicleInfo($data, ["driver_id" => $userid]));
    }
}
