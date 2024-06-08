<?php

require_once __DIR__.'/require.php';

//TODO VALIDATE TOKEN BEFORE GRANTING ACCESS TO ANY DATA
$token =  CommonFunctions::getBearerToken();
$rest =  $jwtHandler->validateToken($token);
if ($rest == false) {
    echo json_encode(array('status' => 'error', 'message' => 'Invalid Token'));
    die;
}

//TODO: GET REQUEST 
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $userid = $rest['userid'];

    if (isset($_GET['result']) and $_GET['result'] == 'getLoans') {
        echo json_encode($model->getLoans($userid));
    }

    if (isset($_GET['result']) and $_GET['result'] == 'getTotalLoans') {
        echo json_encode($model->getTotalLoans($userid));
    }

    if (isset($_GET['result']) and $_GET['result'] == 'resendToken') {        
        echo json_encode($model->resendToken($userid));
    }

    if (isset($_GET['terms'])) {
        echo json_encode($model->terms());
    }
}
