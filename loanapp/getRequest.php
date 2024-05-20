<?php

require_once './require.php';

//TODO: GET REQUEST 
if ($_SERVER['REQUEST_METHOD'] == 'GET') {



    $userid = CommonFunctions::clean($_GET['userid']);
  

    if (isset($_GET['result']) and $_GET['result'] == 'getLoans') {
        echo json_encode($model->getLoans($userid));
    }

    if (isset($_GET['result']) and $_GET['result'] == 'getTotalLoans') {
        echo json_encode($model->getTotalLoans($userid));
    }

    if (isset($_GET['result']) and $_GET['result'] == 'resendToken') {
        echo json_encode($model->resendToken($userid));
    }
}
