<?php

include './required.php';

//
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $staffcode = CommonFunctions::clean($_GET['staffcode']);
    $status = CommonFunctions::clean($_GET['status']);

    //? check the status code
    if ($status == 'Clockout') {
        //? check if user has a clockin state before you can clockout
        $row = $query->table('attendance_clock')
            ->where("staffcode ='$staffcode' AND dlat_location2 ='' ")
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get(['staffcode, fullname, position '], true);
        $msg = "Sorry, you must clock-in before you can clock-out";
    } else {
        //* use this for clockin and enrollment
        $row = $query->table('hr_register')
            ->where("staffcode ='$staffcode'")
            ->get(['staffcode, fullname, email, lux_enrolled '], true);
        $msg = 'Sorry, Staff code not found';
    }

    if (!empty($row)) {
        $data = [
            'ACCESS_CODE' => 'GRANTED',
            'staff' => $row,
            'msg' => ''
        ];
    } else {
        http_response_code(400); // 
        $data = [
            'ACCESS_CODE' => 'DENIED',
            'msg' => $msg
        ];
    }

    echo json_encode($data);
}
