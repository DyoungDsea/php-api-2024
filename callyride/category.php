<?php

require_once './require.php';

//TODO: GET REQUEST drive_categories
if ($_SERVER['REQUEST_METHOD'] == 'GET') {


    //TODO: fech about us info
    if (isset($_GET['Message']) and $_GET['Message'] == 'support') {
        $user = $helper->getSingleRecord('dcontact', "WHERE id = 1");
        echo json_encode([
            "support" => html_entity_decode($user["text"])
        ]);
    }

    //TODO: fech about us info
    if (isset($_GET['Message']) and $_GET['Message'] == 'aboutUs') {
        $user = $helper->getSingleRecord('dabout', "WHERE id = 2");
        echo json_encode([
            "aboutUs" => html_entity_decode($user["text"])
        ]);
    }

    if (isset($_GET['Message']) and $_GET['Message'] == 'getUserRecord') {
        $userid = CommonFunctions::clean($_GET['userid']);
        $user = $helper->getSingleRecord('manage_customers', "WHERE customer_id = '$userid'");

        if (!empty($user)) {
            $result = [
                "customerId" => $user["customer_id"],
                "customerName" => $user["customer_name"],
                "phoneNumber" => $user["phone_number"],
                "emailAddress" => $user["email_address"],
                "contactAddress" => $user["contact_address"],
                "dob" => $user["ddob"],
                "gender" => $user["dgender"],
                "nin" => $user["dnin"],
                "state" => $user["dstate"],
                "city" => $user["dcity"],
                "avatar" => $user["avatar"],
                "dtime" => $user["dtime"],
                "walletBalance" => $user["wallet_balance"]
            ];
            echo json_encode($result);
        }
    }
    //TODO: fech all route
    if (isset($_GET['Message']) and $_GET['Message'] == 'deleteAccount') {
        $userid = CommonFunctions::clean($_GET['userid']);
        echo json_encode($model->deleteAccount($userid));
    }

    //TODO: fech all route
    if (isset($_GET['Message']) and $_GET['Message'] == 'myRoute') {
        $userid = CommonFunctions::clean($_GET['userid']);
        echo json_encode($model->myRoute($userid));
    }

    //TODO: fech all vehicle category
    if (isset($_GET['Message']) and $_GET['Message'] == 'category') {
        $distance = CommonFunctions::clean($_GET['distance']);
        $distance = str_replace(' km', '', $distance);
        echo json_encode($model->category($distance));
    }

    //TODO: fech driverLatLng
    if (isset($_GET['Message']) and $_GET['Message'] == 'completedJobStatus') {
        $id = CommonFunctions::clean($_GET['id']);
        echo json_encode($modelDriver->completedJobStatus($id));
    }

    //TODO: fech driverLatLng
    if (isset($_GET['Message']) and $_GET['Message'] == 'checkStatus') {
        $id = CommonFunctions::clean($_GET['id']);
        echo json_encode($modelDriver->checkStatus($id));
    }

    //TODO: fech driverLatLng
    if (isset($_GET['Message']) and $_GET['Message'] == 'checkOnGoing') {
        $driverid = CommonFunctions::clean($_GET['driverid']);
        echo json_encode($modelDriver->checkOnGoing($driverid, 'ongoing'));
    }
    //TODO: fech driverLatLng
    if (isset($_GET['Message']) and $_GET['Message'] == 'checkAccepted') {
        $driverid = CommonFunctions::clean($_GET['driverid']);
        echo json_encode($modelDriver->checkOnGoing($driverid, 'accepted'));
    }

    //TODO: fech driverLatLng
    if (isset($_GET['Message']) and $_GET['Message'] == 'driverLatLng') {
        $driverid = CommonFunctions::clean($_GET['driverid']);
        echo json_encode($modelDriver->driverLatLng($driverid));
    }

    //TODO: get Driver Pending Job
    if (isset($_GET['Message']) and $_GET['Message'] == 'getDriverPendingJob') {
        $driverid = CommonFunctions::clean($_GET['driverid']);
        echo json_encode($modelDriver->getDriverPendingJob($driverid));
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
        echo json_encode($model->assignDriver(["customer_id" => $userid, "transid" => $transid]));
    }


    //TODO: Get driver response
    if (isset($_GET['Message']) and $_GET['Message'] == 'getDriverResponse') {
        $userid = CommonFunctions::clean($_GET['userid']);
        $transid = CommonFunctions::clean($_GET['transid']);
        echo json_encode($model->getDriverResponse(["customer_id" => $userid, "driver_status" => "accepted", "transid" => $transid]));
    }

    //TODO: Get driver response
    if (isset($_GET['Message']) and $_GET['Message'] == 'checkUserOnGoing') {
        $userid = CommonFunctions::clean($_GET['userid']);
        echo json_encode($model->checkUserOnGoing(["customer_id" => $userid, "driver_status" => "ongoing",]));
    }

    //TODO: Get driver response
    if (isset($_GET['Message']) and $_GET['Message'] == 'checkUserAccept') {
        $userid = CommonFunctions::clean($_GET['userid']);
        echo json_encode($model->checkUserOnGoing(["customer_id" => $userid, "driver_status" => "accepted",]));
    }

    //TODO: fech user job done
    if (isset($_GET['Message']) and $_GET['Message'] == 'fetchUserJobDone') {
        $userid = CommonFunctions::clean($_GET['userid']);
        echo json_encode($model->fetchUserJobDone($userid));
    }

    //TODO: fech user job done
    if (isset($_GET['Message']) and $_GET['Message'] == 'reasons') {
        echo json_encode($modelDriver->fetchReason('driver'));
    }

    if (isset($_GET['Message']) and $_GET['Message'] == 'resendDriverToken') {
        $userid = CommonFunctions::clean($_GET['userid']);
        echo json_encode($modelDriver->resendToken($userid));
    }
}
