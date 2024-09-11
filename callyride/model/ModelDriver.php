<?php



class ModelDriver
{

    private $connection; // Instance of the Connection class
    private $pdo;
    private $unique;
    private $uniqueOrderID;
    private $date;
    private $query;
    private $helper;
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->pdo = $this->connection->getPDO();
        $this->query = new QueryBuilder($this->connection->getPDO());
        $this->helper = new Helper($this->connection->getPDO());
    }


    // User Login
    public function login(string $email, string $password): array
    {
        $data = [];
        $query = "SELECT * FROM `manage_drivers` WHERE email_address = :email OR phone_number = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($user) {
            $hashedPassword = md5($password);
            if ($hashedPassword  === $user['password']) {

                // print_r($user);
                // die;

                include './result.php';

                if ($user["status"] == 'pending') {
                    $name = $user["driver_name"];
                    $driver_id = $user["driver_id"];
                    $pin = rand(1234, 5678);
                    //TODO: SEND PIN TO EMAIL & SMS
                    $subject = "Verification | CallyRiver";
                    $msg = "
                        <b>Dear $name,</b>  welcome to CallyRiver.
                        <P>Use this code <b>$pin</b> to verify your account.</P>                    
                        ";
                    CommonFunctions::sendMail($msg, $email, $subject);
                    $this->helper->update("manage_drivers", ["vcode" => $pin], ["driver_id" => $driver_id]);
                    // http_response_code(401);
                    $data = [
                        'accessCode' => 'VERIFICATION',
                        "data" => $result,
                    ];
                } elseif ($user["status"] == 'banned') {
                    http_response_code(400);
                    $data = [
                        'ACCESS_CODE' => 'DENIED',
                        'msg' => "Your account has been banned, you cannot login to your account."
                    ];
                } else {

                    $data = [
                        "accessCode" => 'GRANTED',
                        "data" => $result,
                    ];
                }
            } else {
                http_response_code(400);
                $data = [
                    'ACCESS_CODE' => 'DENIED',
                    'staff' => null,
                    'msg' => "Incorect password provided."
                ];
            }
        } else {
            http_response_code(400);
            $data = [
                'ACCESS_CODE' => 'DENIED',
                'staff' => null,
                'msg' => "Sorry, we don't have the information you provided."
            ];
        }

        return $data;
    }

    //? new registration
    public function createNewDriver(string $email, string $phone, array $data)
    {

        $name = $data['driver_name'];
        $pin = $data['vcode'];

        $userEmail =  $this->query->read('manage_drivers')
            ->where(['email_address' => $email])
            ->get('email_address', false);

        if (empty($userEmail)) {

            $userPhone =  $this->query->read('manage_drivers')
                ->where(['phone_number' => $phone])
                ->get('phone_number', false);

            if (empty($userPhone)) {

                if ($this->helper->create("manage_drivers", $data)) {
                    $user =  $this->query->read('manage_drivers')
                        ->where(['email_address' => $email])
                        ->get('id', false);
                    $driver_id = '10000' . $user['id'];


                    $this->helper->update("manage_drivers", ["driver_id" => $driver_id], ["email_address" => $email]);

                    $user = $this->query->read('manage_drivers')
                        ->where(['email_address' => $email, "driver_id" => $driver_id])
                        ->get('*', false);

                    $subject = "Verification | CallyRiver";
                    $msg = "
                        <b>Dear $name,</b>  welcome to CallyRiver.
                        <P>Use this code <b>$pin</b> to verify your account.</P>                    
                        ";
                    CommonFunctions::sendMail($msg, $email, $subject);

                    $result = [
                        'data' => [
                            'accessCode' => 'GRANTED',
                            'userid' => $driver_id,
                            'msg' => "Verify your account with the code sent to your email address"
                        ],
                    ];
                }
            } else {
                http_response_code(400);
                $result = [
                    'ACCESS_CODE' => 'DENIED',
                    'user' => null,
                    'msg' => "Sorry, Phone number already taken."
                ];
            }
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'user' => null,
                'msg' => "Sorry, Email address already taken."
            ];
        }


        return $result;
    }


    //  TODO:VERIFY ACCOUNT
    public function verifyAccount($driver_id, $pin)
    {
        $user = $this->query->read('manage_drivers')
            ->where(["driver_id" => $driver_id, "vcode" => $pin])
            ->get('*', false);

        if ($user) {

            $this->helper->update("manage_drivers", ["status" => 'active'], ["driver_id" => $driver_id]);
            $user = $this->helper->getSingleRecord("manage_drivers", "WHERE driver_id='$driver_id'");
            include './result.php';
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "Sorry, invalid code provided."
            ];
        }

        return $result;
    }

    public function forgotPassword(string $email)
    {
        //TODO: Check if email exist
        $userEmail =  $this->query->read('manage_drivers')
            ->where(['email_address' => $email])
            ->get('email_address, driver_name', false);

        if (!empty($userEmail)) {
            //TODO: Send security code
            $rand = rand(1234, 5678);
            $subject = "Reset Password";
            $name = $userEmail['driver_name'];
            $mailTemplate = "
            <b>Dear $name,</b> <br>
            <p>Use this code <b>$rand</b> to reset your password</p>
            <p>Kindly ignore this if the request is not from you</p>
            ";

            $test = "emailController/mailTemplate.php";
            include 'emailController/mailTemplateApi.php';

            $this->helper->update('manage_drivers', ["vcode" => $rand], ["email_address" => $email]);
            $result = [
                'ACCESS_CODE' => 'GRANTED',
                'user' => null,
                'msg' => "Reset code has been sent to your email."
            ];
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'user' => null,
                'msg' => "Sorry, Email address does not exist."
            ];
        }

        return $result;
    }

    public function changePassword($userid, $oldPass, $newPass)
    {

        $user = $this->query->read('manage_drivers')
            ->where(['driver_id' => $userid, "password" => md5($oldPass)])
            ->get('password', false);

        if (!empty($user)) {

            if ($this->helper->update("manage_drivers", ["password" => md5($newPass)], ["driver_id" => $userid])) {
                $result = [
                    'ACCESS_CODE' => 'ACCESS',
                    'user' => null,
                    'msg' => "updated successfully."
                ];
            }
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'user' => null,
                'msg' => "Sorry, Incorrect password."
            ];
        }

        return $result;
    }

    public function resetPassword(string $email, string $token, string $pass)
    {
        //TODO: Check if email exist
        $userEmail =  $this->query->read('manage_drivers')
            ->where(['email_address' => $email, 'vcode' => $token])
            ->get('email_address, driver_name', false);

        if (!empty($userEmail)) {

            $this->helper->update('manage_drivers', ["password" => md5($pass)], ["email_address" => $email]);
            $result = [
                'ACCESS_CODE' => 'GRANTED',
                'user' => null,
                'msg' => "Reset successfully."
            ];
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'user' => null,
                'msg' => "Sorry, invalid token submitted."
            ];
        }

        return $result;
    }


    public function updateChanges(array $data, array $clause)
    {

        if ($this->helper->update('manage_bookings', $data, $clause)) {

            $result = [
                'ACCESS_CODE' => 'GRANTED',
                'msg' => "Success"
            ];
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "Sorry, something went wrong"
            ];
        }

        return $result;
    }



    public function category(string $distance): array
    {
        $category = $this->query->read("drive_categories")
            ->get("dcategory, dpercent, start_fee, km_fee, minute_fee, hourly_fee, dpercent_hourly");
        $res = [];
        foreach ($category as $key => $row) {
            $start_fee = $row['start_fee'];
            $km_fee = $row['km_fee'];
            $finalTotal = ($km_fee * $distance) + $start_fee;
            $res[] = [
                "category" => $row['dcategory'],
                "fare" => CommonFunctions::withNaira($finalTotal),
            ];
        }
        return $res;
    }


    public function getDriverPendingJob(string $id)
    {
        $row =  $this->query->read("manage_bookings")
            ->where(['driver_id' => $id, "driver_status" => 'pending'])
            ->orderBy("id DESC")
            ->limit(1)
            ->get("*", false);

        if (!empty($row)) {
            //TODO: GET THE VEHICLE CATEGORY 
            $categoryId = $row["car_category"];
            $category = $this->helper->getSingleRecord('drive_categories', "WHERE dcategory = '$categoryId'");
            $catData = [
                "dcategory" => $category["dcategory"],
                "dpercent" => $category["dpercent"],
                "start_fee" => $category["start_fee"],
                "km_fee" => $category["km_fee"],
                "minute_fee" => $category["minute_fee"],
                "hourly_fee" => $category["hourly_fee"],
                "dpercent_hourly" => $category["dpercent_hourly"],
            ];

            $result = [
                "id" => $row["id"],
                "customerName" => $row["customer_name"],
                "customerPhone" => $row["phone_number"],
                "customerAddress" => $row["email_address"],
                "pickupAddress" => $row["pickup_address"],
                "dropoffAddress" => $row["dropoff_address"],
                "pickupLat" => $row["pickup_lat"],
                "pickupLong" => $row["pickup_long"],
                "dropoffLat" => $row["dropoff_lat"],
                "dropoffLong" => $row["dropoff_long"],
                "driverName" => $row["driver_name"],
                "cost" => CommonFunctions::withNaira($row["dtotal_actual"]),
                "status" => $row["status"],
                "dateCreated" => CommonFunctions::formatDated($row["date_created"]),
                "timeCreated" => CommonFunctions::formatTime($row["date_created"]),
                "data" => $catData


            ];
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "No pending job for you."
            ];
        }

        return $result;
    }




    public function getDetails(string $id)
    {
        $user =  $this->query->read("manage_drivers")
            ->where(['driver_id' => $id])
            ->get("licenseNumber, driverLicenceFront, frontView, engineView", false);

        $result = [
            "licenseNumber" => !empty($user["licenseNumber"]) ? $user["licenseNumber"] : "",
            "driverLicenceFront" => !empty($user["driverLicenceFront"]) ? $user["driverLicenceFront"] : "",
            "frontView" => !empty($user["frontView"]) ? $user["frontView"] : "",
            "engineView" => !empty($user["engineView"]) ? $user["engineView"] : ""
        ];

        return $result;
    }



    public function viewDetails(string $id)
    {
        $user =  $this->query->read("manage_drivers")
            ->where(['driver_id' => $id])
            ->get("licenseNumber, nin, plateNumber, engineNumber, carColor, carDesc ", false);

        $result = [
            "licenseNumber" => !empty($user["licenseNumber"]) ? $user["licenseNumber"] : "",
            "nin" => !empty($user["nin"]) ? $user["nin"] : "",
            "plateNumber" => !empty($user["plateNumber"]) ? $user["plateNumber"] : "",
            "engineNumber" => !empty($user["engineNumber"]) ? $user["engineNumber"] : "",
            "carColor" => !empty($user["carColor"]) ? $user["carColor"] : "",
            "carDesc" => !empty($user["carDesc"]) ? html_entity_decode($user["carDesc"]) : ""
        ];

        return $result;
    }

    public function viewLicense(string $id)
    {
        $user =  $this->query->read("manage_drivers")
            ->where(['driver_id' => $id])
            ->get("driverLicenceFront, driverLicenceBack, driver_photo", false);

        $result = [
            "driverLicenceFront" => !empty($user["driverLicenceFront"]) ? $user["driverLicenceFront"] : "",
            "driverLicenceBack" => !empty($user["driverLicenceBack"]) ? $user["driverLicenceBack"] : "",
            "driver_photo" => !empty($user["driver_photo"]) ? $user["driver_photo"] : "",
        ];

        return $result;
    }

    public function viewCarPicture(string $id)
    {
        $user =  $this->query->read("manage_drivers")
            ->where(['driver_id' => $id])
            ->get("frontView, backView, engineView, insideFront, insideBack", false);

        $result = [
            "frontView" => !empty($user["frontView"]) ? $user["frontView"] : "",
            "backView" => !empty($user["backView"]) ? $user["backView"] : "",
            "engineView" => !empty($user["engineView"]) ? $user["engineView"] : "",
            "insideFront" => !empty($user["insideFront"]) ? $user["insideFront"] : "",
            "insideBack" => !empty($user["insideBack"]) ? $user["insideBack"] : "",
        ];

        return $result;
    }

    public function updateDriver(array $data, array $clause)
    {

        //TODO: check if email and phone number exist with ID
        $email = $data['email_address'];
        $phone = $data['phone_number'];

        $userEmail = $this->query->read('manage_drivers')
            ->where(['email_address' => $email])
            ->not($clause)
            ->get('email_address', false);

        if (empty($userEmail)) {

            $userPhone =  $this->query->read('manage_drivers')
                ->where(['phone_number' => $phone])
                ->not($clause)
                ->get('phone_number', false);

            if (empty($userPhone)) {

                //TODO: update details
                $this->helper->update("manage_drivers", $data, $clause);

                $userid = $clause['driver_id'];
                $user = $this->helper->getSingleRecord('manage_drivers', "WHERE driver_id = '$userid'");

                if (!empty($user)) {                    
                include './result.php';
                }
            } else {
                http_response_code(400);
                $result = [
                    'ACCESS_CODE' => 'DENIED',
                    'user' => null,
                    'msg' => "Sorry, Phone number already taken."
                ];
            }
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'user' => null,
                'msg' => "Sorry, Email address already taken."
            ];
        }
        return $result;
    }

    public function driverLatLong(array $data, array $clause)
    {

        //TODO: LATLONG
        if ($this->helper->update("manage_drivers", $data, $clause)) {
            $result = [
                'ACCESS_CODE' => 'GRANTED',
                'msg' => "Success, updated"
            ];
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "Sorry, something wrong"
            ];
        }

        return $result;
    }

    public function  updateVehicleInfo(array $data, array $clause)
    {

        if ($this->helper->update("manage_drivers", $data, $clause)) {

            $userid = $clause['driver_id'];
            $user = $this->helper->getSingleRecord('manage_drivers', "WHERE driver_id = '$userid'");

            

            if (!empty($user)) {
                include './result.php';
            }
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "Sorry, Something wrong occur, try again later."
            ];
        }
        return $result;
    }

    public function fetchJobDone(string $id)
    {
        $jobs =  $this->query->read("manage_bookings")
            ->where(['driver_id' => $id])
            ->orderBy('id DESC')
            ->get();
        $result = [];
        foreach ($jobs as $job) {
            $result[] = [
                "id" => $job["id"],
                "customerName" => $job["customer_name"],
                "customerPhone" => $job["phone_number"],
                "customerAddress" => $job["email_address"],
                "pickupAddress" => $job["pickup_address"],
                "dropoffAddress" => $job["dropoff_address"],
                "pickupLat" => $job["pickup_lat"],
                "pickupLong" => $job["pickup_long"],
                "dropoffLat" => $job["dropoff_lat"],
                "dropoffLong" => $job["dropoff_long"],
                "driverName" => $job["driver_name"],
                "cost" => $job["dtotal"],
                "status" => $job["status"],
                "dateCreated" => CommonFunctions::formatDated($job["date_created"]),
                "timeCreated" => CommonFunctions::formatTime($job["date_created"]),
            ];
        }
        // print_r($job);


        return $result;
    }

    public function fetchAvalaibleJob()
    {
        $jobs =  $this->query->read("manage_bookings")
            ->orderBy('id DESC')
            ->limit(3)
            ->get();
        $result = [];
        foreach ($jobs as $job) {
            $result[] = [
                "id" => $job["id"],
                "customerName" => $job["customer_name"],
                "customerPhone" => $job["phone_number"],
                "customerAddress" => $job["email_address"],
                "pickupAddress" => $job["pickup_address"],
                "dropoffAddress" => $job["dropoff_address"],
                "pickupLat" => $job["pickup_lat"],
                "pickupLong" => $job["pickup_long"],
                "dropoffLat" => $job["dropoff_lat"],
                "dropoffLong" => $job["dropoff_long"],
                "driverName" => $job["driver_name"],
                "cost" => CommonFunctions::withNaira($job["dtotal"]),
                "status" => $job["status"],
                "dateCreated" => CommonFunctions::formatDated($job["date_created"]),
                "timeCreated" => CommonFunctions::formatTime($job["date_created"]),
            ];
        }
        // print_r($job);


        return $result;
    }


    public function driverLatLng($driverid)
    {
        $row = $this->helper->getSingleRecordWithSelector("manage_drivers", 'driver_latitude, driver_longitude', " WHERE driver_id='$driverid'");
        $result = [];
        if (!empty($row)) {
            $result = [
                "driverLat" => $row["driver_latitude"],
                "driverLng" => $row["driver_longitude"],
            ];
        }

        return $result;
    }
    public function checkStatus($id)
    {
        $row = $this->helper->getSingleRecordWithSelector("manage_bookings", 'driver_status,status', " WHERE id='$id'");
        $result = [];
        if (!empty($row)) {
            $result = [
                "status" => $row["status"],
                "driveStatus" => $row["driver_status"],
            ];
        }

        return $result;
    }

    public function completedJobStatus($id)
    {
        $row = $this->helper->getSingleRecordWithSelector("manage_bookings", 'driver_status,status,dtotal, dtotal_actual', " WHERE id='$id'");
        $result = [];
        if (!empty($row)) {
            $result = [
                "status" => $row["status"],
                "driveStatus" => $row["driver_status"],
                "actualTotal" => number_format($row["dtotal_actual"]),
                "total" => $row["dtotal"],
            ];
        }

        return $result;
    }

    public function resendToken(string $userid)
    {
        $user = $this->query->read("manage_drivers")
            ->where(['driver_id' => $userid])
            ->get("*", false);

        if (!empty($user)) {
            $name = $user["driver_name"];
            $driver_id = $user["driver_id"];
            $email = $user["email_address"];
            $pin = rand(1234, 5678);
            //TODO: SEND PIN TO EMAIL & SMS
            $subject = "Verification | CallyRiver";
            $msg = "
                <b>Dear $name,</b>  welcome to CallyRiver.
                <P>Use this code <b>$pin</b> to verify your account.</P>                    
                ";
            CommonFunctions::sendMail($msg, $email, $subject);
            $this->helper->update("manage_drivers", ["vcode" => $pin], ["driver_id" => $driver_id]);
            // http_response_code(401);
            $result = [
                'ACCESS_CODE' => 'SUCCESS',
                'msg' => "Verification code has been sent to your email address."
            ];
        }
        return $result;
    }

    public function fetchReason()
    {
        $rows = $this->query->read("dreasons")->get("*", true);
        $result = [];
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $result[] = [
                    "id" => $row["id"],
                    "title" => $row["dtitle"],
                ];
            }
        }

        return $result;
    }



    public function checkOnGoing(string $id, string $status)
    {
        $row =  $this->query->read("manage_bookings")
            ->where(['driver_id' => $id, "driver_status" => $status])
            ->orderBy("id DESC")
            ->limit(1)
            ->get("*", false);

        if (!empty($row)) {
            //TODO: GET THE VEHICLE CATEGORY 
            $categoryId = $row["car_category"];
            $category = $this->helper->getSingleRecord('drive_categories', "WHERE dcategory = '$categoryId'");
            $catData = [
                "dcategory" => $category["dcategory"],
                "dpercent" => $category["dpercent"],
                "start_fee" => $category["start_fee"],
                "km_fee" => $category["km_fee"],
                "minute_fee" => $category["minute_fee"],
                "hourly_fee" => $category["hourly_fee"],
                "dpercent_hourly" => $category["dpercent_hourly"],
            ];

            $data = [
                "id" => $row["id"],
                "customerName" => $row["customer_name"],
                "customerPhone" => $row["phone_number"],
                "customerAddress" => $row["email_address"],
                "pickupAddress" => $row["pickup_address"],
                "dropoffAddress" => $row["dropoff_address"],
                "pickupLat" => $row["pickup_lat"],
                "pickupLong" => $row["pickup_long"],
                "dropoffLat" => $row["dropoff_lat"],
                "dropoffLong" => $row["dropoff_long"],
                "driverName" => $row["driver_name"],
                "cost" => CommonFunctions::withNaira($row["dtotal_actual"]),
                "status" => $row["status"],
                "dateCreated" => CommonFunctions::formatDated($row["date_created"]),
                "timeCreated" => CommonFunctions::formatTime($row["date_created"]),
                "data" => $catData

            ];
            $result = [
                'ACCESS_CODE' => 'GRANTED',
                'data' =>  $data
            ];
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "No ongoing job for you."
            ];
        }

        return $result;
    }
}
