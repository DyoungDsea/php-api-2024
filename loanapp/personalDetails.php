<?php

require_once './require.php';

require './vendor/autoload.php';



$uploadPath = 'uploads/';

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
        $userid = CommonFunctions::clean($_POST['userid']);

        $oldSlip = CommonFunctions::clean($_POST['oldSlip']);
        $oldPassport = CommonFunctions::clean($_POST['oldPassport']);


        $data = [
            "computerId" => $compiterID,
            "dfullname" => $fullname,
            "dphone" => $phone,
            "natureEmployed" => $employment,
            "yearEmployed" => $year,
            "ddepartment" => $department,
            "ddesignation" => $designation,
            "residentAddress" => $resident

            // "dpassport" => $passport
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
            $img->save($uploadPath .$rename);
            $pathSave = "uploads/$rename";
            $imageUpload = [
                "paymentSlip" => $pathSave,
            ];

            $data = array_merge($data, $imageUpload);
        }



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
            $img->save($uploadPath .$rename);
            $pathSave = "uploads/$rename";
            $imageUpload = [
                "dpassport" => $pathSave,
            ];

            $data = array_merge($data, $imageUpload);
        }


        echo json_encode($model->updatePersonalDetails($data, ["userid" => $userid]));
    }
}
