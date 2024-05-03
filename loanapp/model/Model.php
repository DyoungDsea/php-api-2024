<?php

class Model
{

    private $connection; // Instance of the Connection class
    private $pdo;
    private $query;
    private $helper;
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->pdo = $this->connection->getPDO();
        $this->unique = CommonFunctions::generateUniqueID();
        $this->date = CommonFunctions::getDate('1 hour');
        $this->query = new QueryBuilder($this->connection->getPDO());
        $this->helper = new Helper($this->connection->getPDO());
    }


    // User Login
    public function login($email, $password)
    { 
        $query = "SELECT * FROM `dlogin` WHERE demail = :email OR dphone = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

 
        if ($user) {
            $hashedPassword = CommonFunctions::hashPassword($password);
            if (hash_equals($hashedPassword , $user["dpassword"])) {
                $data = [
                    "userid" => $user["userid"],
                    "firstname" => $user["dfirstname"],
                    "lastname" => $user["dlastname"],
                    "phoneNumber" => $user["dphone"],
                    "emailAddress" => $user["demail"],                    

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

        $name = $data['dfirstname'];
        $pin = $data['dpin'];
        $userEmail =  $this->query->read('dlogin')
            ->where(['demail' => $email])
            ->get('demail', false);

        if (empty($userEmail)) {

            $userPhone =  $this->query->read('dlogin')
                ->where(['dphone' => $phone])
                ->get('dphone', false);
            if (empty($userPhone)) {
                if ($this->helper->create("dlogin", $data)) {
                    $subject = "Verification | ZAMOGOZA";
                    $msg = "
                    <b>Dear $name,</b>  welcome to ZAMOGOZA LTD.
                    <P>Use this code <b>$pin</b> to verify your account.</P>                    
                    ";
                    CommonFunctions::sendMail($msg, $email, $subject);
                    $result = [
                        'ACCESS_CODE' => 'GRANTED',
                        "userid" => $data['userid'],
                    ];
                } else {
                    http_response_code(400);
                    $result = [
                        'ACCESS_CODE' => 'DENIED',
                        'msg' => "Sorry, Something went wrong."
                    ];
                }
            } else {
                http_response_code(400);
                $result = [
                    'ACCESS_CODE' => 'DENIED',
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