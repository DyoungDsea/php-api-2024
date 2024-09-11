<?php

require_once __DIR__ . '/require.php';

$uploadPath = 'uploads/';

//TODO VALIDATE TOKEN BEFORE GRANTING ACCESS TO ANY DATA
$token =  CommonFunctions::getBearerToken();
$rest =  $jwtHandler->validateToken($token);
if ($rest == false) {
    echo json_encode(array('status' => 'error', 'message' => 'Invalid Token'));
    die;
}


//TODO: POST REQUEST 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    //TODO: UPDATE PERSONAL DETAILS
    if (isset($_POST['Message']) and $_POST['Message'] == 'personalDetails') {

        $compiterID = CommonFunctions::clean($_POST['compiterID']);
        $fullname = CommonFunctions::clean($_POST['fullname']);
        $phone = CommonFunctions::clean($_POST['phone']);
        $employment = CommonFunctions::clean($_POST['employment']);
        $year = CommonFunctions::clean($_POST['year']);
        $department = CommonFunctions::clean($_POST['department']);
        $designation = CommonFunctions::clean($_POST['designation']);
        $resident = CommonFunctions::clean($_POST['resident']);
        $userid = CommonFunctions::clean($rest['userid']);

        // $oldSlip = CommonFunctions::clean($_POST['oldSlip']);
        // $oldPassport = CommonFunctions::clean($_POST['oldPassport']);
        $accountName = CommonFunctions::clean($_POST['accountName']);
        $accountNumber = CommonFunctions::clean($_POST['accountNumber']);
        $bankName = CommonFunctions::clean($_POST['bankName']);



        $data = [
            "computerId" => $compiterID,
            "dfullname" => $fullname,
            "dphone" => $phone,
            "natureEmployed" => $employment,
            "yearEmployed" => $year,
            "ddepartment" => $department,
            "ddesignation" => $designation,
            "accountName" => $accountName,
            "accountNumber" => $accountNumber,
            "bankName" => $bankName,
            "residentAddress" => $resident

            // "dpassport" => $passport
        ];



        if (!empty($_FILES['passport']['name'])) {
            $uploadPath = '../uploads/';
            $uploadedFile = $_FILES['passport'];
            $filename = $uploadedFile['name'];
            $basename = basename($filename);
            // Get the file extension
            $extention = pathinfo($basename, PATHINFO_EXTENSION);

            $rename = hash("SHA256", rand(12345, 67890)) . '.' . $extention;
            $tmpName =  $uploadedFile['tmp_name'];
            $img = Intervention\Image\ImageManagerStatic::make($tmpName);
            $img->save($uploadPath . $rename);
            $pathSave = "uploads/$rename";
            $imageUpload = [
                "dpassport" => $pathSave,
            ];

            $data = array_merge($data, $imageUpload);
        }


        echo json_encode($model->updatePersonalDetails($data, ["userid" => $userid]));
    }



    //TODO: APPLY FOR LOAN
    if (isset($_POST['Message']) and $_POST['Message'] == 'applyForLoan') {


        $userid = CommonFunctions::clean($rest['userid']);
        $fullname = CommonFunctions::clean($rest['fullname']);
        $phone = CommonFunctions::clean($rest['phone']);
        $email = CommonFunctions::clean($rest['email']);

        $gross = CommonFunctions::clean($_POST['gross']);
        $net = CommonFunctions::clean($_POST['net']);
        $amountApply = CommonFunctions::clean($_POST['amountApply']);

        $level = CommonFunctions::clean($_POST['level']);
        $amountSpread = CommonFunctions::clean($_POST['amountSpread']);
        $spreadPeriod = CommonFunctions::clean($_POST['month']);
        $amountDeducted = CommonFunctions::clean($_POST['amountDeducted']);
        $totalInterest = CommonFunctions::clean($_POST['totalInterest']);
        $available = CommonFunctions::clean($_POST['available']);
        $StartDate = CommonFunctions::clean($_POST['startDate']);


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
            "drate"=> 6,
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
}
