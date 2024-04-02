<?php

require_once './require.php';

//TODO: GET REQUEST drive_categories
if ($_SERVER['REQUEST_METHOD'] == 'GET') {


    //TODO: fech all vehicle category
    if (isset($_GET['Message']) and $_GET['Message'] == 'category') {
        $distance = CommonFunctions::clean($_GET['distance']);
        $distance = str_replace(' km', '', $distance);
        echo json_encode($model->category($distance));
    }

    //TODO: fech all vehicle category
    if (isset($_GET['Message']) and $_GET['Message'] == 'driver') {
        $driverid = CommonFunctions::clean($_GET['driverid']);
        echo json_encode($modelDriver->getDetails($driverid));
    }
    
    //TODO: fech all vehicle category
    if (isset($_GET['Message']) and $_GET['Message'] == 'viewDriverLicence') {
        $driverid = CommonFunctions::clean($_GET['driverid']);
        echo json_encode($modelDriver->viewDetails($driverid));
    }

    //TODO: fech all license
    if (isset($_GET['Message']) and $_GET['Message'] == 'viewLicencePassport') {
        $driverid = CommonFunctions::clean($_GET['driverid']);
        echo json_encode($modelDriver->viewLicense($driverid));
    }
}
