<?php

include './required.php';

//
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staffcode = CommonFunctions::clean($_POST['staffcode']);

    //* check if staffcode has been enrolled
    //* if yes, remove the old image and re-enroll the staffcode
    $row = $query->table('hr_register')
    ->where("staffcode ='$staffcode'")
    ->get(['lux_enrolled, lux_image_profile, lux_uuid '], true);

    if($row['lux_enrolled']=='yes'){
        $uuid = $row['lux_uuid'];
        //* request to delete file from luxand database
        include_once './luxand_delete.php';
    }

    //? required luxand image upload curl request
    if(!empty($_FILES['img']['name'])){ 
        $imageFile = $_FILES['img'];
        $imageName = $imageFile['name'];
        $imageTmpName = $imageFile['tmp_name'];
        $imageType = $imageFile['type'];
    }

    //! uncomment the following line on production
    require './luxand_upload.php';
     $data = json_decode($response, true);
    $updateData = array(
        "lux_enrolled" => "yes",
        "lux_image_profile" => $data['url'],
        "lux_uuid" => $data['uuid'],
    );
    //********************************** */


    //update user record with staff details
   
    // $updateData = array(
    //     "lux_enrolled" => "no",
    //     "lux_image_profile" => null,
    //     "lux_uuid" => null
    // );

    $res = $model->updateRecord('hr_register', $updateData, "staffcode='$staffcode'");
    if ($res != false) {
        $data = [
            'ACCESS_CODE' => 'GRANTED', 
            'msg' => ''
        ];
    } else {
        http_response_code(400); // 
        $data = [
            'ACCESS_CODE' => 'DENIED',
            'msg' => 'Fail, We\'re unable to enroll your account, please try again'
        ];
    }

    echo json_encode($data);
}
