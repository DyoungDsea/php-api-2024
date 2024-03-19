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
                $data = [
                    "driverId" => $user["driver_id"],
                    "driverName" => $user["driver_name"],
                    "phoneNumber" => $user["phone_number"],
                    "emailAddress" => $user["email_address"],
                    "contactAddress" => $user["daddress"],
                    "licenseNumber" => $user["license_number"],
                    "lastupdate" => $user["lastupdate"],
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

        return $data; 
    }



    //? new registration
    public function createNewDriver(string $email, string $phone, array $data)
    {

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
                    $result = [
                        "driverId" => $user["driver_id"],
                        "driverName" => $user["driver_name"],
                        "phoneNumber" => $user["phone_number"],
                        "emailAddress" => $user["email_address"],
                        "contactAddress" => $user["daddress"],
                        "licenseNumber" => $user["license_number"],
                        "lastupdate" => $user["lastupdate"],
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
                    $result = [
                        "driverId" => $user["driver_id"],
                        "driverName" => $user["driver_name"],
                        "phoneNumber" => $user["phone_number"],
                        "emailAddress" => $user["email_address"],
                        "contactAddress" => $user["daddress"],
                        "licenseNumber" => $user["license_number"],
                        "lastupdate" => $user["lastupdate"],
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
}
