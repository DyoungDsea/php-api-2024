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
            "vcode" => rand(1234, 5678),
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

    //TODO: FORGOT PASSWORD
    if (isset($jsonData->Message) and $jsonData->Message == 'forgot') {
        $email = CommonFunctions::clean($jsonData->email);
        echo json_encode($modelDriver->forgotPassword($email));
    }
}


//TODO: PUT REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    //TODO: MARK JOB STATUS
    if (isset($jsonData->Message) and $jsonData->Message == 'cancelledTrip') {
        $id = CommonFunctions::clean($jsonData->tripId);
        $message = CommonFunctions::clean($jsonData->message);
        $data = ["status" => 'cancelled'];
        echo json_encode($modelDriver->updateChanges($data, ["id" => $id]));
    }

    //TODO: MARK JOB STATUS
    if (isset($jsonData->Message) and $jsonData->Message == 'hasArrived') {
        $id = CommonFunctions::clean($jsonData->tripId);
        $message = CommonFunctions::clean($jsonData->message);
        $data = ["driverHasArrived" => 'yes'];
        echo json_encode($modelDriver->updateChanges($data, ["id" => $id]));
    }


    //TODO: MARK JOB STATUS
    if (isset($jsonData->Message) and $jsonData->Message == 'endTrip') {
        $id = CommonFunctions::clean($jsonData->tripId);
        $cost = CommonFunctions::clean($jsonData->cost);
        $currentAddress = CommonFunctions::clean($jsonData->currentAddress);
        $lat = CommonFunctions::clean($jsonData->lat);
        $lng = CommonFunctions::clean($jsonData->lng);
        $driverid = CommonFunctions::clean($jsonData->userid);

        $data = [
            "driver_status" => 'completed',
            "status" => 'completed',
            "dtotal" => $cost,
            "dropoff_address" => $currentAddress,
            "dropoff_lat" => $lat,
            "dropoff_long" => $lng,
        ];

        echo json_encode($modelDriver->updateChanges($data, ["id" => $id]));
    }

    //TODO: MARK JOB STATUS
    if (isset($jsonData->Message) and $jsonData->Message == 'markJobStatus') {
        $id = CommonFunctions::clean($jsonData->id);
        $status = CommonFunctions::clean($jsonData->status);
        if ($status == 'accepted') {
            $data = ["driver_status" => 'accepted'];
        } else {
            $data = [
                "driver_id" => NULL,
                "driver_name" => NULL,
                "phone_number" => NULL,
                "car_type" => NULL,
                "car_category" => NULL,
                "driver_photo" => NULL,
                "plateNumber" => NULL,
                "driver_status" => 'pending'
            ];
        }

        echo json_encode($modelDriver->updateChanges($data, ["id" => $id]));
    }
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
            "driver_longitude" => $long,
            "log_status" => 'online',
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

    //TODO: UPDATE BASIC INFORMATION
    if (isset($jsonData->Message) and $jsonData->Message == 'basicDriverInformation') {

        $gender = CommonFunctions::clean($jsonData->gender);
        $state = CommonFunctions::clean($jsonData->state);
        $lga = CommonFunctions::clean($jsonData->lga);
        $city = CommonFunctions::clean($jsonData->city);
        $address = htmlentities($jsonData->address);
        $nin = CommonFunctions::clean($jsonData->nin);
        $userid = CommonFunctions::clean($jsonData->userid);

        $data = [
            "dgender" => $gender,
            "nin" => $nin,
            "state" => $state,
            "dlga" => $lga,
            "dcity" => $city,
            "daddress" => $address
        ];

        echo json_encode($modelDriver->updateVehicleInfo($data, ["driver_id" => $userid]));
    }


    //TODO: UPDATE VEHICLE DETAILS
    if (isset($jsonData->Message) and $jsonData->Message == 'vehicleDetails') {

        $carName = CommonFunctions::clean($jsonData->carName);
        $plateNo = CommonFunctions::clean($jsonData->plateNo);
        $color = CommonFunctions::clean($jsonData->color);
        $enginNo = CommonFunctions::clean($jsonData->enginNo);
        $desc = htmlentities($jsonData->desc);
        $userid = CommonFunctions::clean($jsonData->userid);

        $data = [
            "car_type" => $carName,
            "carDesc" => $desc,
            "carColor" => $color,
            "plateNumber" => $plateNo,
            "engineNumber" => $enginNo
        ];

        echo json_encode($modelDriver->updateVehicleInfo($data, ["driver_id" => $userid]));
    }

    //TODO: VERIFY ACCOUNT
    if (isset($jsonData->Message) and $jsonData->Message == 'verifyAccount') {
        $pin = CommonFunctions::clean($jsonData->pin);
        $driverid = CommonFunctions::clean($jsonData->driverid);
        echo json_encode($modelDriver->verifyAccount($driverid, $pin));
    }
}
