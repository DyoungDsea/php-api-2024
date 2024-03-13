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

        $name = "$fname $lname";

        $data = [
            "customer_name" => $name,
            "phone_number" => $phone,
            "email_address" => $email,
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
}


//TODO: PUT REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

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

        $data = [
            "customer_name" => $fullname,
            "phone_number" => $phone,
            "email_address" => $email
        ];

        echo json_encode($model->updateUser($data, ["customer_id" => $userid]));
    }
}
