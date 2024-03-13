<?php

include './required.php';

//
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $companyAccess = CommonFunctions::clean($_POST['companyAccess']);
    $row = $query->table('hr_subscriptions')
        ->where("pword='$companyAccess'")
        ->get(['pword'], true);
    if (!empty($row)) {
        $data = [
            'ACCESS_CODE' => 'GRANTED',
            'token' => $companyAccess,
            'msg' => ''
        ];
    } else {
        http_response_code(400); // 
        $data = [
            'ACCESS_CODE' => 'DENIED',
            'msg' => 'Sorry, Company access code not found'
        ];
    }

    echo json_encode($data);
}
