<?php
// require '../image_php/class.upload.php';
require_once './require.php';



//TODO: POST REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['Message']) and $_POST['Message'] == 'selfie') {

        $selfie = $uploader->uploadImage($_FILES['selfie'], 2, 600);
        $driverid = CommonFunctions::clean($_POST['driverid']);
        $data = [
            "driver_selfie" => str_replace('../', '', $selfie,),
        ];

        if ($helper->update('manage_drivers', $data, ["driver_id" => $driverid])) {
            $user = $helper->getSingleRecord('manage_drivers', "WHERE driver_id = '$driverid'");

            if (!empty($user)) {
                include './result.php';
            }
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "Sorry, something went wrong."
            ];
        }

        echo json_encode($result);
    }


    if (isset($_POST['Message']) and $_POST['Message'] == 'uploadProfileImage') {

        $selfie = $uploader->uploadImage($_FILES['image']);
        $userid = CommonFunctions::clean($_POST['userid']);
        $data = [
            "avatar" => str_replace('../', '', $selfie,),
        ];

        if ($helper->update('manage_customers', $data, ["customer_id" => $userid])) {
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
            }
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "Sorry, something went wrong."
            ];
        }

        echo json_encode($result);
    }


    if (isset($_POST['Message']) and $_POST['Message'] == 'passport') {

        $passport = $uploader->uploadImage($_FILES['passport'], 2, 600);
        $driverid = CommonFunctions::clean($_POST['driverid']);
        $data = [
            "driver_photo" => str_replace('../', '', $passport,),
        ];

        if ($helper->update('manage_drivers', $data, ["driver_id" => $driverid])) {
            $user = $helper->getSingleRecord('manage_drivers', "WHERE driver_id = '$driverid'");

            if (!empty($user)) {
                include './result.php';
            }
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "Sorry, something went wrong."
            ];
        }

        echo json_encode($result);
    }



    if (isset($_POST['Message']) and $_POST['Message'] == 'licence') {
        $result = [];
        if (!empty($_FILES['front']['name'])) {


            $front = $uploader->uploadImage($_FILES['front']);

            $driverid = CommonFunctions::clean($_POST['driverid']);
            $expireDate = CommonFunctions::clean($_POST['expireDate']);
            $data = [
                "driverLicenceFront" => str_replace('../', '', $front),
                "licenseExpireDate" => $expireDate
            ];
            if ($helper->update('manage_drivers', $data, ["driver_id" => $driverid])) {
                $user = $helper->getSingleRecord('manage_drivers', "WHERE driver_id = '$driverid'");

                if (!empty($user)) {
                    include './result.php';
                }
            } else {
                http_response_code(400);
                $result = [
                    'ACCESS_CODE' => 'DENIED',
                    'msg' => "Sorry, something went wrong."
                ];
            }
        }

        echo json_encode($result);
    }



    if (isset($_POST['Message']) and $_POST['Message'] == 'licenceBack') {
        $result = [];
        if (!empty($_FILES['back']['name'])) {

            $back = $uploader->uploadImage($_FILES['back'], 1);

            $driverid = CommonFunctions::clean($_POST['driverid']);
            $data = [
                "driverLicenceBack" => str_replace('../', '', $back)
            ];
            if ($helper->update('manage_drivers', $data, ["driver_id" => $driverid])) {
                $user = $helper->getSingleRecord('manage_drivers', "WHERE driver_id = '$driverid'");

                if (!empty($user)) {
                    include './result.php';
                }
            } else {
                http_response_code(400);
                $result = [
                    'ACCESS_CODE' => 'DENIED',
                    'msg' => "Sorry, something went wrong."
                ];
            }
        }

        echo json_encode($result);
    }



    if (isset($_POST['Message']) and $_POST['Message'] == 'carFront') {
        $result = [];
        if (!empty($_FILES['carFront']['name'])) {

            $front = $uploader->uploadImage($_FILES['carFront']);
            $driverid = CommonFunctions::clean($_POST['driverid']);
            $data = [
                "frontView" => str_replace('../', '', $front)
            ];
            if ($helper->update('manage_drivers', $data, ["driver_id" => $driverid])) {
                $user = $helper->getSingleRecord('manage_drivers', "WHERE driver_id = '$driverid'");

                if (!empty($user)) {
                    include './result.php';
                }
            } else {
                http_response_code(400);
                $result = [
                    'ACCESS_CODE' => 'DENIED',
                    'msg' => "Sorry, something went wrong."
                ];
            }
        }

        echo json_encode($result);
    }



    if (isset($_POST['Message']) and $_POST['Message'] == 'carBack') {
        $result = [];
        if (!empty($_FILES['carBack']['name'])) {

            $back = $uploader->uploadImage($_FILES['carBack'], 1);

            $driverid = CommonFunctions::clean($_POST['driverid']);
            $data = [
                "backView" => str_replace('../', '', $back),
            ];
            if ($helper->update('manage_drivers', $data, ["driver_id" => $driverid])) {
                $user = $helper->getSingleRecord('manage_drivers', "WHERE driver_id = '$driverid'");

                if (!empty($user)) {
                    include './result.php';
                }
            } else {
                http_response_code(400);
                $result = [
                    'ACCESS_CODE' => 'DENIED',
                    'msg' => "Sorry, something went wrong."
                ];
            }
        }

        echo json_encode($result);
    }

    if (isset($_POST['Message']) and $_POST['Message'] == 'carEngine') {
        $result = [];
        if (!empty($_FILES['carEngine']['name'])) {

            $back = $uploader->uploadImage($_FILES['carEngine'], 1);

            $driverid = CommonFunctions::clean($_POST['driverid']);
            $data = [
                "engineView" => str_replace('../', '', $back),
            ];
            if ($helper->update('manage_drivers', $data, ["driver_id" => $driverid])) {
                $user = $helper->getSingleRecord('manage_drivers', "WHERE driver_id = '$driverid'");

                if (!empty($user)) {
                    include './result.php';
                }
            } else {
                http_response_code(400);
                $result = [
                    'ACCESS_CODE' => 'DENIED',
                    'msg' => "Sorry, something went wrong."
                ];
            }
        }

        echo json_encode($result);
    }

    if (isset($_POST['Message']) and $_POST['Message'] == 'carInside') {
        $result = [];
        if (!empty($_FILES['carInside']['name'])) {

            $back = $uploader->uploadImage($_FILES['carInside'], 1);

            $driverid = CommonFunctions::clean($_POST['driverid']);
            $data = [
                "insideFront" => str_replace('../', '', $back),
            ];
            if ($helper->update('manage_drivers', $data, ["driver_id" => $driverid])) {
                $user = $helper->getSingleRecord('manage_drivers', "WHERE driver_id = '$driverid'");

                if (!empty($user)) {
                    include './result.php';
                }
            } else {
                http_response_code(400);
                $result = [
                    'ACCESS_CODE' => 'DENIED',
                    'msg' => "Sorry, something went wrong."
                ];
            }
        }

        echo json_encode($result);
    }


    if (isset($_POST['Message']) and $_POST['Message'] == 'restFile') {
        $result = [];
        if (!empty($_FILES['front']['name']) and !empty($_FILES['back']['name']) and !empty($_FILES['engine']['name'])) {


            $front = $uploader->uploadImage($_FILES['front']);
            $back = $uploader->uploadImage($_FILES['back'], 1);
            $engine = $uploader->uploadImage($_FILES['engine'], 2);

            // $front = $helper->imagesUpload($_FILES['front'], '', 600, 600);
            // $back = $helper->imagesUpload($_FILES['back'], '1', 600, 600);
            // $engine = $helper->imagesUpload($_FILES['engine'], '2', 600, 600);


            $driverid = CommonFunctions::clean($_POST['driverid']);
            $data = [
                "engineView" => str_replace('../', '', $front),
                "insideFront" => str_replace('../', '', $back),
                "insideBack" => str_replace('../', '', $engine),
            ];
            if ($helper->update('manage_drivers', $data, ["driver_id" => $driverid])) {
                $user = $helper->getSingleRecord('manage_drivers', "WHERE driver_id = '$driverid'");

                if (!empty($user)) {
                    include './result.php';
                }
            } else {
                http_response_code(400);
                $result = [
                    'ACCESS_CODE' => 'DENIED',
                    'msg' => "Sorry, something went wrong."
                ];
            }
        }

        echo json_encode($result);
    }
}
