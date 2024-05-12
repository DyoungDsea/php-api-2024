<?php

require_once './require.php';

//TODO: GET REQUEST 
if ($_SERVER['REQUEST_METHOD'] == 'GET') {


    $driverid = CommonFunctions::clean($_GET['userid']);
    echo json_encode($model->resendToken($userid));
}
