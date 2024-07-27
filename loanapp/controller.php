<?php
require_once __DIR__ . '/require.php';
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
        //TODO VALIDATE TOKEN BEFORE GRANTING ACCESS TO ANY DATA
        $token =  CommonFunctions::getBearerToken();
        $rest =  $jwtHandler->validateToken($token);
        if ($rest == false) {
            echo json_encode(array('status' => 'error', 'message' => 'Invalid Token'));
            die;
        }


        $userid = CommonFunctions::clean($rest['userid']);
        $fullname = CommonFunctions::clean($rest['fullname']);
        $phone = CommonFunctions::clean($rest['phone']);
        $email = CommonFunctions::clean($rest['email']);

        $gross = CommonFunctions::clean($jsonData->gross);
        $net = CommonFunctions::clean($jsonData->net);
        $amountApply = CommonFunctions::clean($jsonData->amountApply);

        $level = CommonFunctions::clean($jsonData->level);
        $amountSpread = CommonFunctions::clean($jsonData->amountSpread);
        $spreadPeriod = CommonFunctions::clean($jsonData->month);
        $amountDeducted = CommonFunctions::clean($jsonData->amountDeducted);
        $totalInterest = CommonFunctions::clean($jsonData->totalInterest);
        $available = CommonFunctions::clean($jsonData->available);
        $StartDate = CommonFunctions::clean($jsonData->startDate);




        $totalBalance = ($amountApply + $totalInterest);

        $data = [
            "rid" => CommonFunctions::generateUniqueID(),
            "userid" => $userid,
            "grossMonthly" => $gross,
            "netMonthly" => $net,
            "amountApply" => $amountApply,
            "dlevel" => $level,
            "amountRequest" => $amountApply,
            "amountSpread" => $amountSpread,
            "spreadPeriod" => $spreadPeriod,
            "amountDeducted" => $amountDeducted,
            "totalInterest" => $totalInterest,
            "totalPayment" => '0.00',
            "totalBalance" => $totalBalance,
            "deductionDate" => $StartDate,
            "amountAvailable" => $amountSpread,
            "ddate" => CommonFunctions::getDateTime(1),
            "searchDate" => date("Y-m-d"),
            "processingFee" => '5000',
            "approveDate" => CommonFunctions::getDateTime(1),
        ]; 

        
        //TODO: PASSPORT AND SLIP
        if (!empty($_FILES['slip']['name'])) {
            $uploadPath = '../uploads/'; // Specify your upload directory
            $uploadedFile = $_FILES['slip'];
            $filename = $uploadedFile['name'];
            $basename = basename($filename);
            $extention = pathinfo($basename, PATHINFO_EXTENSION);

            $rename = hash("SHA256", time() . rand(12345, 67890)) . '.' . $extention;
            $tmpName =  $uploadedFile['tmp_name'];
            $img = Intervention\Image\ImageManagerStatic::make($tmpName);
            $img->save($uploadPath . $rename);
            $pathSave = "uploads/$rename";
            $imageUpload = [
                "paymentSlip" => $pathSave,
            ];

            $data = array_merge($data, $imageUpload);
        }


        echo json_encode($model->applyForLoan($email, $phone, $fullname, $data));
    }

    //TODO: lOGIN
    if (isset($jsonData->Message) and $jsonData->Message == 'login') {
        $user = CommonFunctions::clean($jsonData->user);
        $pass = CommonFunctions::clean($jsonData->pass);
        echo json_encode($model->login($user, $pass));
    }



    if (isset($jsonData->Message) and $jsonData->Message == 'letterRequest') {
        //TODO VALIDATE TOKEN BEFORE GRANTING ACCESS TO ANY DATA
        $token =  CommonFunctions::getBearerToken();
        $rest =  $jwtHandler->validateToken($token);
        if ($rest == false) {
            echo json_encode(array('status' => 'error', 'message' => 'Invalid Token'));
            die;
        }

        $userid = CommonFunctions::clean($rest['userid']);
        $fullname = CommonFunctions::clean($rest['fullname']);
        $phone = CommonFunctions::clean($rest['phone']);
        $email = CommonFunctions::clean($rest['email']);
        $letter = CommonFunctions::clean($jsonData->letter);

        $data = [
            "lid" => CommonFunctions::generateUniqueID(),
            "userid" => $userid,
            "dletter" => $letter,
            "dstate" => "Letter of indetedness",
            "ddate" => CommonFunctions::getDateTime(1),
        ];
        echo json_encode($model->letterRequest($email, $fullname, $data));
    }
}


//TODO: PUT REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    if (isset($jsonData->Message) and $jsonData->Message == 'resetPassword') {
        $token = CommonFunctions::clean($jsonData->token);
        $pass = CommonFunctions::clean($jsonData->pass);
        $email = CommonFunctions::clean($jsonData->email);

        echo json_encode($model->resetPassword($email, $token, $pass));
        die;
    }

    //TODO: VERIFY ACCOUNT
    if (isset($jsonData->Message) and $jsonData->Message == 'verifyAccount') {
        $pin = CommonFunctions::clean($jsonData->pin);
        $userid = CommonFunctions::clean($jsonData->userid);
        echo json_encode($model->verifyAccount($userid, $pin));
        die;
    }

    //TODO VALIDATE TOKEN BEFORE GRANTING ACCESS TO ANY DATA
    $token =  CommonFunctions::getBearerToken();
    $rest =  $jwtHandler->validateToken($token);
    if ($rest == false) {
        echo json_encode(array('status' => 'error', 'message' => 'Invalid Token'));
        die;
    }

    $userid = CommonFunctions::clean($rest['userid']);
    $fullname = CommonFunctions::clean($rest['fullname']);
    $phone = CommonFunctions::clean($rest['phone']);
    $email = CommonFunctions::clean($rest['email']);



    //TODO: CHANGE PASSWORD
    if (isset($jsonData->Message) and $jsonData->Message == 'changePassword') {

        $current = CommonFunctions::clean($jsonData->current);
        $pass = CommonFunctions::clean($jsonData->pass);
        echo json_encode($model->changePassword($userid, $current, $pass));
    }

    //TODO: UPDATE NEXT OF KIN
    // if (isset($jsonData->Message) and $jsonData->Message == 'nextOfKin') {

    //     $fullname = CommonFunctions::clean($jsonData->fullname);
    //     $phone = CommonFunctions::clean($jsonData->phone);
    //     $gender = CommonFunctions::clean($jsonData->gender);
    //     $relationship = CommonFunctions::clean($jsonData->relationship);
    //     $userid = CommonFunctions::clean($jsonData->userid);

    //     $data = [
    //         "nextOfKinName" => $fullname,
    //         "nextOfKinGender" => $gender,
    //         "nextOfKinRelationship" => $relationship,
    //         "nextOfKinPhone" => $phone,
    //     ];

    //     echo json_encode($model->updatePersonalDetails($data, ["userid" => $userid]));
    // }

    //TODO: UPDATE ACCOUNT DETAILS
    if (isset($jsonData->Message) and $jsonData->Message == 'accountDetails') {



        echo json_encode($model->updatePersonalDetails($data, ["userid" => $userid]));
    }
}
