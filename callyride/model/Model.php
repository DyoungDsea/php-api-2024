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
        $this->date = CommonFunctions::geDate('1 hour');
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
                $data = [
                    "customerId" => $user["customer_id"],
                    "customerName" => $user["customer_name"],
                    "phoneNumber" => $user["phone_number"],
                    "emailAddress" => $user["email_address"],
                    "contactAddress" => $user["contact_address"],
                    "avatar" => $user["avatar"],
                    "dtime" => CommonFunctions::formatDate($user["dtime"]),
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
 
    public function imagesUpload($fileName, $clause, $tableName, $id = '', $filePath, $x = 400, $y = 400)
    {

        @list(,, $imtype,) = getimagesize($fileName['tmp_name']);

        if ($imtype == 3 || $imtype == 2 || $imtype == 1) {
            $picid = $this->unique . $id;
            $foo = new Upload($fileName);

            if ($foo->uploaded) {
                // Save uploaded image with a new name
                $foo->file_new_name_body = $picid;
                $foo->image_resize = true;
                $foo->image_convert = 'jpg';
                $foo->image_x = $x;
                $foo->image_y = $y;
                $foo->Process($filePath);

                if ($foo->processed) {
                    $foo->Clean();
                }
            }

            // Update the database with the new image information using PDO
            $query = "UPDATE $tableName SET dimg$id = :picid WHERE $clause";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':picid', $picid);

            if ($stmt->execute()) {
                // The update was successful
            }
        }
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
                    $result = [
                        "customerId" => $user["customer_id"],
                        "customerName" => $user["customer_name"],
                        "phoneNumber" => $user["phone_number"],
                        "emailAddress" => $user["email_address"],
                        "contactAddress" => $user["contact_address"],
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
}
