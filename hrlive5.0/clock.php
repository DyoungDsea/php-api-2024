<?php

include './required.php';

//
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $staffcode = CommonFunctions::clean($_POST['staffcode']);
    $status = CommonFunctions::clean($_POST['status']);
    $latitude = CommonFunctions::clean($_POST['latitude']);
    $longitude = CommonFunctions::clean($_POST['longitude']);
    $address = CommonFunctions::clean($_POST['address']);
    $date = CommonFunctions::getDateTime(1);

    //? required luxand image upload curl request
    if (!empty($_FILES['img']['name'])) {
        $imageFile = $_FILES['img'];
        $imageName = $imageFile['name'];
        $imageTmpName = $imageFile['tmp_name'];
        $imageType = $imageFile['type'];
    }
    //! uncomment the following line on production
    // require './luxand_upload.php';    
    // $data = json_decode($response, true);
    //! *********************************
    
    //**** OFFLINE TEST */
    $data = [
        "uuid" => "3c10db21-aa4b-11ee-91bf-0242ac120002",
        "url" => "https://faces.nyc3.digitaloceanspaces.com/3c10db21-aa4b-11ee-91bf-0242ac120002.jpg",
    ];    
    //***************CLOSE OFFLINE TEST******************* */

    //? Check Status
    if ($status == "Clockin") {
       require_once './clockin_process.php';
      $res =   $model->createNewRecord('attendance_clock', $info);
      if($res !== false){
        $data = [
            'ACCESS_CODE' => 'GRANTED', 
            'msg' => ''
        ];
      }else{
        http_response_code(400);
        $data = [
            'ACCESS_CODE' => 'DENIED',  
            'msg' => 'Something went wrong'
        ];
      }

    } else {
        //* clockout staff
        $info = array(
            //!from luxand response data
            "lux_clockout_image" => $data['url'],
            "lux_clockout_uuid" => $data['uuid'],
        );
    }

    echo json_encode($data);
}
