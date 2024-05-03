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

    //TODO: fech all license
    if (isset($_GET['Message']) and $_GET['Message'] == 'viewCarPicture') {
        $driverid = CommonFunctions::clean($_GET['driverid']);
        echo json_encode($modelDriver->viewCarPicture($driverid));
    }

    //TODO: fech all Avaliable Job
    if (isset($_GET['Message']) and $_GET['Message'] == 'fetchAvalaibleJob') {
        echo json_encode($modelDriver->fetchAvalaibleJob());
    }

    //TODO: fech job done by driver
    if (isset($_GET['Message']) and $_GET['Message'] == 'fetchJobDone') {
        $driverid = CommonFunctions::clean($_GET['driverid']);
        echo json_encode($modelDriver->fetchJobDone($driverid));
    }
    //TODO: assign driver to user
    if (isset($_GET['Message']) and $_GET['Message'] == 'assignDriver') {
        $userid = CommonFunctions::clean($_GET['userid']);
        $transid = CommonFunctions::clean($_GET['transid']);
        echo json_encode($model->assignDriver(["customer_id" => $userid,"transid"=>$transid]));
    }
    //TODO: Get driver response
    if (isset($_GET['Message']) and $_GET['Message'] == 'getDriverResponse') {
        $userid = CommonFunctions::clean($_GET['userid']);
        $transid = CommonFunctions::clean($_GET['transid']);
        echo json_encode($model->getDriverResponse(["customer_id" => $userid, "driver_status" => "accepted", "transid"=>$transid]));
    }

    //TODO: fech user job done
    if (isset($_GET['Message']) and $_GET['Message'] == 'fetchUserJobDone') {
        $userid = CommonFunctions::clean($_GET['userid']);
        echo json_encode($model->fetchUserJobDone($userid));
    }
}
