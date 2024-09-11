<?php

class Model
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
        $this->unique = CommonFunctions::generateUniqueID();
        $this->uniqueOrderID = CommonFunctions::generateOrderID();
        $this->date = CommonFunctions::getDate('1 hour');
        $this->query = new QueryBuilder($this->connection->getPDO());
        $this->helper = new Helper($this->connection->getPDO());
    }


    // User Login
    public function login($email, $password)
    {
        $query = "SELECT * FROM `manage_customers` WHERE email_address = :email OR phone_number = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($user) {
            $hashedPassword = md5($password);
            if ($hashedPassword  === $user['pword']) {
                $data =  [
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

        return $data; // Login failed
    }

    public function forgotPassword(string $email)
    {
        //TODO: Check if email exist
        $userEmail =  $this->query->read('manage_customers')
            ->where(['email_address' => $email])
            ->get('email_address, customer_name', false);

        if (!empty($userEmail)) {
            //TODO: Send security code
            $rand = rand(1234, 5678);
            $subject = "Reset Password";
            $name = $userEmail['customer_name'];
            $mailTemplate = "
            <b>Dear $name,</b> <br>
            <p>Use this code <b>$rand</b> to reset your password</p>
            <p>Kindly ignore this if the request is not from you</p>
            ";

            $test = "emailController/mailTemplate.php";
            include 'emailController/mailTemplateApi.php';

            $this->helper->update('manage_customers', ["vcode" => $rand], ["email_address" => $email]);
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
    public function resetPassword(string $email, string $token, string $pass)
    {
        //TODO: Check if email exist
        $userEmail =  $this->query->read('manage_customers')
            ->where(['email_address' => $email, 'vcode' => $token])
            ->get('email_address, customer_name', false);

        if (!empty($userEmail)) {

            $this->helper->update('manage_customers', ["pword" => md5($pass)], ["email_address" => $email]);
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

    //? new registration
    public function createNewUser(string $email, string $phone, array $data)
    {

        $userEmail =  $this->query->read('manage_customers')
            ->where(['email_address' => $email])
            ->get('email_address', false);

        if (empty($userEmail)) {

            $userPhone =  $this->query->read('manage_customers')
                ->where(['phone_number' => $phone])
                ->get('phone_number', false);

            if (empty($userPhone)) {



                if ($this->helper->create("manage_customers", $data)) {
                    $user =  $this->query->read('manage_customers')
                        ->where(['email_address' => $email])
                        ->get('id', false);
                    $customer_id = '10000' . $user['id'];


                    $this->helper->update("manage_customers", ["customer_id" => $customer_id], ["email_address" => $email]);

                    $user = $this->query->read('manage_customers')
                        ->where(['email_address' => $email, "customer_id" => $customer_id])
                        ->get('*', false);
                    $result =  [
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


    public function changePassword($userid, $oldPass, $newPass)
    {

        $user = $this->query->read('manage_customers')
            ->where(['customer_id' => $userid, "pword" => md5($oldPass)])
            ->get('pword', false);

        if (!empty($user)) {

            if ($this->helper->update("manage_customers", ["pword" => md5($newPass)], ["customer_id" => $userid])) {
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
                "cost" => "$finalTotal",
            ];
        }
        return $res;
    }

    public function updateUser(array $data, array $clause)
    {

        //TODO: check if email and phone number exist with ID
        $email = $data['email_address'];
        $phone = $data['phone_number'];

        $userEmail = $this->query->read('manage_customers')
            ->where(['email_address' => $email])
            ->not($clause)
            ->get('email_address', false);

        if (empty($userEmail)) {

            $userPhone =  $this->query->read('manage_customers')
                ->where(['phone_number' => $phone])
                ->not($clause)
                ->get('phone_number', false);

            if (empty($userPhone)) {

                //TODO: update details
                $this->helper->update("manage_customers", $data, $clause);

                $userid = $clause['customer_id'];
                $user = $this->helper->getSingleRecord('manage_customers', "WHERE customer_id = '$userid'");

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

    public function booking(array $data, string $transid)
    {
        if ($this->helper->create("manage_bookings", $data)) {
            $result = [
                'ACCESS_CODE' => 'GRANDED',
                "transid" => "$transid",
                'msg' => "Well done!"
            ];
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "Sorry, something went wrong."
            ];
        }

        return $result;
    }


    public function cancelBooking(array $data, array $clause)
    {
        //TODO: GET LAST BOOKING
        $user =  $this->query->read('manage_bookings')
            ->where($clause)
            ->orderBy("id DESC")
            ->limit(1)
            ->get('transid', false);

        if (!empty($user)) {
            $transid = $user['transid'];
            $merge = array_merge($clause, ["transid" => $transid]);
            if ($this->helper->update("manage_bookings", $data, $merge)) {
                $result = [
                    'ACCESS_CODE' => 'GRANDED',
                    'msg' => "Well done!"
                ];
            } else {
                http_response_code(400);
                $result = [
                    'ACCESS_CODE' => 'DENIED',
                    'msg' => "Sorry, something went wrong."
                ];
            }
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "Sorry, something went wrong."
            ];
        }


        return $result;
    }

    public function assignDriver($clause)
    {
        //TODO: GET LAST BOOKING
        $user =  $this->query->read('manage_bookings')
            ->where($clause)
            ->orderBy("id DESC")
            ->limit(1)
            ->get('id, driver_id, pickup_long, pickup_lat, driver_status', false);
        if (!empty($user)) {
            $lat = $user['pickup_lat'];
            $long = $user['pickup_long'];
            $id = $user['id'];
            $driver_id = $user['driver_id'];

            if (empty($user['driver_id'])) {

                //TODO: FIND DRIVER
                $driver = $this->helper->getClosestDriver($lat, $long, 30);
                if (!empty($driver)) {
                    $result = [
                        'ACCESS_CODE' => 'GRANTED',
                        'msg' => "Successful"
                    ];

                    //TODO: UPDATE DRIVER DETAILS ON BOOKING TABLE
                    $data = [
                        "driver_id" => $driver['driver_id'],
                        "driver_name" => $driver['driver_name'],
                        "phone_number" => $driver['phone_number'],
                        "car_type" => $driver['car_type'],
                        "car_category" => $driver['car_category'],
                        "driver_photo" => $driver['driver_photo'],
                        "plateNumber" => $driver['plateNumber'],
                    ];

                    $this->helper->update("manage_bookings", $data, ['id' => $id]);
                    $result = [
                        'ACCESS_CODE' => 'GRANTED',
                        'msg' => "Successful"
                    ];
                } else {
                    $result = [
                        'ACCESS_CODE' => 'DENIED',
                        'msg' => "Driver unavailable"
                    ];
                }
            } else {
                //TODO: MEANING DRIVER HAS BEEN ASSIGNED
                $result = [
                    'ACCESS_CODE' => 'GRANTED',
                    'msg' => "Successful"
                ];
            }
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "Sorry, record not found."
            ];
        }

        return $result;
    }

    public function getDriverResponse($clause)
    {
        //TODO: GET LAST BOOKING
        $booking =  $this->query->read('manage_bookings')
            ->where($clause)
            ->orderBy("id DESC")
            ->limit(1)
            ->get('id, driver_id, driver_name, driver_status, phone_number, car_type, car_category, driver_photo, plateNumber, pickup_lat, pickup_long ', false);
        if (!empty($booking)) {
            $driverid = $booking['driver_id'];
            $driverLatLng = $this->helper->getSingleRecord("manage_drivers", " WHERE driver_id='$driverid'");
            $lat = $driverLatLng['driver_latitude'];
            $lng = $driverLatLng['driver_longitude'];

            $userLat =  $booking['pickup_lat'];
            $userLng =  $booking['pickup_long'];

            //TODO: GET DISTANCE IN MINUTES
            $distance = $this->helper->haversineDistance($userLat, $userLng, $lat, $lng);
            $getMinutes = round($this->helper->calculateDrivingTime($distance, 60));
            $bookingID = $booking['id'];
            $data = [
                "id" => "$bookingID",
                "driverid" => $driverid,
                "driverName" => $booking['driver_name'],
                "driverPhone" => $booking['phone_number'],
                "carName" => $booking['car_type'],
                "carCategory" => $booking['car_category'],
                "driverStatus" => $booking['driver_status'],
                "getMinutes" => $getMinutes == 0 ? "1" : "$getMinutes",
                "photo" => $booking['driver_photo'],
                "plateNumber" => $booking['plateNumber'],
                "driverLatitude" => $lat,
                "driverLongitude" => $lng,
                "lat" => $userLat,
                "long" => $userLng,
            ];
            $result = [
                'ACCESS_CODE' => 'GRANTED',
                'data' =>  $data
            ];
        } else {
            // http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "Searching for driver"
            ];
        }

        return $result;
    }

    public function checkUserOnGoing($clause)
    {
        //TODO: GET LAST BOOKING
        $booking =  $this->query->read('manage_bookings')
            ->where($clause)
            ->orderBy("id DESC")
            ->limit(1)
            ->get('id, driver_id, driver_name, driver_status, phone_number, car_type, car_category, driver_photo, plateNumber, pickup_lat, pickup_long ', false);
        if (!empty($booking)) {
            $driverid = $booking['driver_id'];
            $driverLatLng = $this->helper->getSingleRecord("manage_drivers", " WHERE driver_id='$driverid'");
            $lat = $driverLatLng['driver_latitude'];
            $lng = $driverLatLng['driver_longitude'];

            $userLat =  $booking['pickup_lat'];
            $userLng =  $booking['pickup_long'];

            //TODO: GET DISTANCE IN MINUTES
            $distance = $this->helper->haversineDistance($userLat, $userLng, $lat, $lng);
            $getMinutes = round($this->helper->calculateDrivingTime($distance, 60));
            $bookingID = $booking['id'];
            $data = [
                "id" => "$bookingID",
                "driverid" => $driverid,
                "driverName" => $booking['driver_name'],
                "driverPhone" => $booking['phone_number'],
                "carName" => $booking['car_type'],
                "carCategory" => $booking['car_category'],
                "driverStatus" => $booking['driver_status'],
                "getMinutes" => $getMinutes == 0 ? "1" : "$getMinutes",
                "photo" => $booking['driver_photo'],
                "plateNumber" => $booking['plateNumber'],
                "driverLatitude" => $lat,
                "driverLongitude" => $lng,
                "lat" => $userLat,
                "long" => $userLng,
            ];
            $result = [
                'ACCESS_CODE' => 'GRANTED',
                'data' =>  $data
            ];
        } else {
            // http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "Searching for driver"
            ];
        }

        return $result;
    }


    public function fetchUserJobDone(string $id)
    {
        $jobs =  $this->query->read("manage_bookings")
            ->where(['customer_id' => $id])
            ->orderBy('id DESC')
            ->limit(10)
            ->get();
        $result = [];
        foreach ($jobs as $job) {
            $result[] = [
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
                "cost" => number_format($job["status"] != "completed" ? $job["dtotal_actual"] : $job["dtotal"], 2),
                "status" => $job["status"],
                "dateCreated" => CommonFunctions::formatDated($job["date_created"]),
                "timeCreated" => CommonFunctions::formatTime($job["date_created"]),
            ];
        }
        // print_r($job);


        return $result;
    }

    public function myRoute(string $userid): array
    {
        $routes = $this->query->read("manage_bookings")
            ->where(['customer_id' => $userid])
            ->groupBy('pickup_address, dropoff_address')
            ->orderBy('id DESC')
            ->limit(10)
            ->get('pickup_address, dropoff_address, pickup_lat, pickup_long, dropoff_lat, dropoff_long');
        $res = [];
        foreach ($routes as $row) {
            $res[] = [
                "pickupAddress" => $row['pickup_address'],
                "dropoffAddress" => $row['dropoff_address'],
                "pickupLat" => $row['pickup_lat'],
                "pickupLng" => $row['pickup_long'],
                "dropoffLat" => $row['dropoff_lat'],
                "dropoffLng" => $row['dropoff_long'],

            ];
        }
        return $res;
    }

    public function deleteAccount(string $userid): array
    {
        $this->helper->update("manage_customers", ["deleteAccount" => 'yes'], ["customer_id" => $userid]);
        return [
            "success" => "Success"
        ];
    }
}
