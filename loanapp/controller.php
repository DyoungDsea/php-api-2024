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

        $fullname = "$fname $lname";

        $data = [
            "dfirstname" => $fname,
            "dlastname" => $lname,
            "dfullname" => $fullname,
            "dphone" => $phone,
            "demail" => $email,
            "dpin" => rand(1234, 5678),
            "userid" => CommonFunctions::generateUniqueID(),
            "dpassword" => CommonFunctions::hashPassword($pass),
            "ddatetime" => CommonFunctions::getDateTime(1),
            "ddate" => CommonFunctions::getDate('1 hour'),
        ];

        echo json_encode($model->createNewUser($email, $phone, $data));
    }

    //TODO: APPLY FOR LOAN
    if (isset($jsonData->Message) and $jsonData->Message == 'applyForLoan') {

        $userid = CommonFunctions::clean($jsonData->userid);
        $duration = CommonFunctions::clean($jsonData->duration);
        $gross = CommonFunctions::clean($jsonData->gross);
        $net = CommonFunctions::clean($jsonData->net);
        $amountApply = CommonFunctions::clean($jsonData->amountApply);
        $amountInWords = CommonFunctions::clean($jsonData->amountInWords);

        $level = CommonFunctions::clean($jsonData->level);
        $amountSpread = CommonFunctions::clean($jsonData->amountSpread);
        $spreadPeriod = CommonFunctions::clean($jsonData->month);
        $amountDeducted = CommonFunctions::clean($jsonData->amountDeducted);
        $available = CommonFunctions::clean($jsonData->available);
        $StartDate = CommonFunctions::clean($jsonData->startDate);

        
        $fullname = CommonFunctions::clean($jsonData->fullname);
        $phone = CommonFunctions::clean($jsonData->phone);
        $email = CommonFunctions::clean($jsonData->email);

       

        $data = [            
            "rid" => CommonFunctions::generateUniqueID(),
            "userid" => $userid,
            "dduration" => $duration,
            "grossMonthly" => $gross,
            "netMonthly" => $net,
            "amountApply" => $amountApply,
            "amountInWords" => $amountInWords,
            "dlevel" => $level,
            "amountRequest" => $amountApply,
            "amountSpread" => $amountSpread,
            "spreadPeriod" => $spreadPeriod,
            "amountDeducted" => $amountDeducted,
            "deductionDate" => $StartDate,
            "amountAvailable" => $amountSpread,
            "ddate" => CommonFunctions::getDateTime(1),
            "approveDate" => CommonFunctions::getDateTime(1),
        ];

        echo json_encode($model->applyForLoan($email, $phone, $fullname, $data));
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
        $token = CommonFunctions::clean($jsonData->token);
        $pass = CommonFunctions::clean($jsonData->pass);
        $email = CommonFunctions::clean($jsonData->email);

        echo json_encode($model->resetPassword($email, $token, $pass));
    }

    //TODO: VERIFY ACCOUNT
    if (isset($jsonData->Message) and $jsonData->Message == 'verifyAccount') {
        $userid = CommonFunctions::clean($jsonData->userid);
        $pin = CommonFunctions::clean($jsonData->pin);
        echo json_encode($model->verifyAccount($userid, $pin));
    }

    //TODO: CHANGE PASSWORD
    if (isset($jsonData->Message) and $jsonData->Message == 'changePassword') {

        $current = CommonFunctions::clean($jsonData->current);
        $pass = CommonFunctions::clean($jsonData->pass);
        $userid = CommonFunctions::clean($jsonData->userid);

        echo json_encode($model->changePassword($userid, $current, $pass));
    }

    //TODO: UPDATE NEXT OF KIN
    if (isset($jsonData->Message) and $jsonData->Message == 'nextOfKin') {

        $fullname = CommonFunctions::clean($jsonData->fullname);
        $phone = CommonFunctions::clean($jsonData->phone);
        $gender = CommonFunctions::clean($jsonData->gender);
        $relationship = CommonFunctions::clean($jsonData->relationship);
        $userid = CommonFunctions::clean($jsonData->userid);

        $data = [
            "nextOfKinName" => $fullname,
            "nextOfKinGender" => $gender,
            "nextOfKinRelationship" => $relationship,
            "nextOfKinPhone" => $phone,
        ];

        echo json_encode($model->updatePersonalDetails($data, ["userid" => $userid]));
    }

    //TODO: UPDATE ACCOUNT DETAILS
    if (isset($jsonData->Message) and $jsonData->Message == 'accountDetails') {

        $accountName = CommonFunctions::clean($jsonData->accountName);
        $accountNumber = CommonFunctions::clean($jsonData->accountNumber);
        $bankName = CommonFunctions::clean($jsonData->bankName);
        $userid = CommonFunctions::clean($jsonData->userid);

        $data = [
            "accountName" => $accountName,
            "accountNumber" => $accountNumber,
            "bankName" => $bankName,
        ];

        echo json_encode($model->updatePersonalDetails($data, ["userid" => $userid]));
    }
}
