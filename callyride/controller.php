<?php
require_once './require.php';
$jsonData = json_decode(file_get_contents("php://input"));

//TODO: POST REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    //TODO: FORGOT PASSWORD
    if (isset($jsonData->Message) and $jsonData->Message == 'forgot') {
        $email = CommonFunctions::clean($jsonData->email);
        echo json_encode($model->forgotPassword($email));
    }


    //TODO: REGISTER
    if (isset($jsonData->Message) and $jsonData->Message == 'register') {

        $fname = CommonFunctions::clean($jsonData->fname);
        $lname = CommonFunctions::clean($jsonData->lname);
        $phone = CommonFunctions::clean($jsonData->phone);
        $email = CommonFunctions::clean($jsonData->email);
        $pass = CommonFunctions::clean($jsonData->pass);

        $name = "$fname $lname";

        $data = [
            "customer_name" => $name,
            "phone_number" => $phone,
            "email_address" => $email,
            "dtime" => CommonFunctions::getDateTime(1),
            "pword" => md5($pass)
        ];

        echo json_encode($model->createNewUser($email, $phone, $data));
    }

    //TODO: lOGIN
    if (isset($jsonData->Message) and $jsonData->Message == 'login') {
        $user = CommonFunctions::clean($jsonData->user);
        $pass = CommonFunctions::clean($jsonData->pass);
        echo json_encode($model->login($user, $pass));
    }

    //TODO: BOOKING
    if (isset($jsonData->Message) and $jsonData->Message == 'booking') {
        $userid = CommonFunctions::clean($jsonData->userid);
        $name = CommonFunctions::clean($jsonData->name);
        $phone = CommonFunctions::clean($jsonData->phone);
        $email = CommonFunctions::clean($jsonData->email);
        $category = CommonFunctions::clean($jsonData->category);
        $pickup = CommonFunctions::clean($jsonData->pickup);
        $dropoff = CommonFunctions::clean($jsonData->dropoff);
        $pickupLat = CommonFunctions::clean($jsonData->pickupLat);
        $pickupLong = CommonFunctions::clean($jsonData->pickupLong);
        $dropoffLat = CommonFunctions::clean($jsonData->dropoffLat);
        $dropoffLong = CommonFunctions::clean($jsonData->dropoffLong);
        $cost = CommonFunctions::clean($jsonData->cost);
        $transid = CommonFunctions::generateUniqueID();
        $date = CommonFunctions::getDate('1 hour');

        echo json_encode($model->booking([
            "customer_id" => $userid,
            "customer_name" => $name,
            "phone_number" => $phone,
            "email_address" => $email,
            "car_category" => $category,
            "pickup_address" => $pickup,
            "dropoff_address" => $dropoff,
            "pickup_lat" => $pickupLat,
            "pickup_long" => $pickupLong,
            "dropoff_lat" => $dropoffLat,
            "dropoff_long" => $dropoffLong,
            "dtotal_actual" => $cost,
            "date_created" => $date,
            "transid" => $transid,
        ], $transid));
    }
}


//TODO: PUT REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    if (isset($jsonData->Message) and $jsonData->Message == 'resetPassword') {
        $token1 = CommonFunctions::clean($jsonData->token1);
        $token2 = CommonFunctions::clean($jsonData->token2);
        $token3 = CommonFunctions::clean($jsonData->token3);
        $token4 = CommonFunctions::clean($jsonData->token4);
        $pass = CommonFunctions::clean($jsonData->pass);
        $email = CommonFunctions::clean($jsonData->email);

        $token = "$token1$token2$token3$token4";

        echo json_encode($model->resetPassword($email, $token, $pass));
    }

    //TODO: CHANGE PASSWORD
    if (isset($jsonData->Message) and $jsonData->Message == 'changePassword') {

        $current = CommonFunctions::clean($jsonData->current);
        $pass = CommonFunctions::clean($jsonData->pass);
        $userid = CommonFunctions::clean($jsonData->userid);

        echo json_encode($model->changePassword($userid, $current, $pass));
    }

    //TODO: UPDATE PROFILE
    if (isset($jsonData->Message) and $jsonData->Message == 'updateUser') {

        $fullname = CommonFunctions::clean($jsonData->fullname);
        $phone = CommonFunctions::clean($jsonData->phone);
        $email = CommonFunctions::clean($jsonData->email);
        $userid = CommonFunctions::clean($jsonData->userid);

        $dob = CommonFunctions::clean($jsonData->dob);
        $gender = CommonFunctions::clean($jsonData->gender);
        $nin = CommonFunctions::clean($jsonData->nin);
        $state = CommonFunctions::clean($jsonData->state);
        $city = CommonFunctions::clean($jsonData->city);
        $address = CommonFunctions::clean($jsonData->address);

        $data = [
            "customer_name" => $fullname,
            "phone_number" => $phone,
            "email_address" => $email,
            "ddob" => date("Y-m-d", strtotime($dob)),
            "dgender" => $gender,
            "dnin" => $nin,
            "dstate" => $state,
            "dcity" => $city,
            "contact_address" => $address,
        ];

        echo json_encode($model->updateUser($data, ["customer_id" => $userid]));
    }

    //TODO: CANCEL BOOKING 
    if (isset($jsonData->Message) and $jsonData->Message == 'cancelBooking') {
        $userid = CommonFunctions::clean($jsonData->userid);
        $transid = CommonFunctions::clean($jsonData->transid);
        echo json_encode($model->cancelBooking(["status" => "cancelled"], ["customer_id" => $userid, "transid" => $transid]));
    }
}
