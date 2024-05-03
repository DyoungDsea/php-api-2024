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

        $data = [
            "dfirstname" => $fname,
            "dlastname" => $lname,
            "dphone" => $phone,
            "demail" => $email,
            "dpin" => rand(1234,6789),
            "userid" => CommonFunctions::generateUniqueID(),
            "dpassword" => CommonFunctions::hashPassword($pass),
            "ddatetime" => CommonFunctions::getDateTime(1),
            "ddate" => CommonFunctions::getDate('1 hour'),
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

        $data = [
            "customer_name" => $fullname,
            "phone_number" => $phone,
            "email_address" => $email
        ];

        echo json_encode($model->updateUser($data, ["customer_id" => $userid]));
    }

     
}
