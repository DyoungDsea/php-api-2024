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
            $result =  [
                "userid" => $user["userid"],
                "firstname" => $user["dfirstname"],
                "lastname" => $user["dlastname"],
                "phoneNumber" => $user["dphone"],
                "emailAddress" => $user["demail"],
                "status" => $user["dstatus"],

            ];
            if (hash_equals($hashedPassword, $user["dpassword"])) {
                //TODO: CHECK THE STATUS OF THE ACCOUNT
                if ($user["dstatus"] == 'pending') {
                    $name = $user["dfirstname"];
                    $userid = $user["userid"];
                    $pin = rand(1234, 5678);
                    //TODO: SEND PIN TO EMAIL & SMS
                    $subject = "Verification | ZAMOGOZA";
                    $msg = "
                    <b>Dear $name,</b>  welcome to ZAMOGOZA LTD.
                    <P>Use this code <b>$pin</b> to verify your account.</P>                    
                    ";
                    CommonFunctions::sendMail($msg, $email, $subject);
                    $this->helper->update("dlogin", ["dpin" => $pin], ["userid" => $userid]);
                    // http_response_code(401);
                    $data = [
                        'accessCode' => 'VERIFICATION',
                        "data" => $result,
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

        return $data; // Login failed
    }


    //  TODO:VERIFY ACCOUNT
    public function verifyAccount($userid, $pin)
    {
        $user = $this->query->read('dlogin')
            ->where(['userid' => $userid, "dpin" => $pin])
            ->get('*', false);

        if ($user) {

            $data = [
                "userid" => $user["userid"],
                "firstname" => $user["dfirstname"],
                "lastname" => $user["dlastname"],
                "phoneNumber" => $user["dphone"],
                "emailAddress" => $user["demail"],
                "status" => 'verified',

            ];
            $this->helper->update("dlogin", ["dstatus" => 'active'], ["userid" => $userid]);
        } else {
            http_response_code(400);
            $data = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "Sorry, invalid code provided."
            ];
        }

        return $data;
    }

    public function forgotPassword(string $email)
    {
        //TODO: Check if email exist
        $userEmail =  $this->query->read('dlogin')
            ->where(['demail' => $email])
            ->get('demail, dfirstname, dstatus', false);

        if (!empty($userEmail) && $userEmail['dstatus'] == 'active') {
            //TODO: Send security code
            $rand = rand(1234, 5678);
            $subject = "Reset Password";
            $name = $userEmail['dfirstname'];
            $msg = "
            <b>Dear $name,</b> <br>
            <p>Use this code <b>$rand</b> to reset your password</p>
            <p>Kindly ignore this if the request is not from you</p>
            ";

            CommonFunctions::sendMail($msg, $email, $subject);

            $this->helper->update('dlogin', ["dpin" => $rand], ["demail" => $email]);
            $result = [
                'ACCESS_CODE' => 'GRANTED',
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
        $userEmail =  $this->query->read('dlogin')
            ->where(['demail' => $email, 'dpin' => $token])
            ->get('demail, dfirstname', false);

        if (!empty($userEmail)) {
            $pass = CommonFunctions::hashPassword($pass);
            $this->helper->update('dlogin', ["dpassword" => $pass], ["demail" => $email]);
            $result = [
                'ACCESS_CODE' => 'GRANTED',
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
                        'accessCode' => 'GRANTED',
                        "userid" => $data['userid'],
                        'msg' => "Verify your account with the code sent to your email address or phone number."
                    ];
                } else {
                    http_response_code(400);
                    $result = [
                        'accessCode' => 'DENIED',
                        'msg' => "Sorry, Something went wrong."
                    ];
                }
            } else {
                http_response_code(400);
                $result = [
                    'accessCode' => 'DENIED',
                    'msg' => "Sorry, Phone number already taken."
                ];
            }
        } else {
            http_response_code(400);
            $result = [
                'accessCode' => 'DENIED',
                'user' => null,
                'msg' => "Sorry, Email address already taken."
            ];
        }


        return $result;
    }


    public function changePassword($userid, $oldPass, $newPass)
    {

        $user = $this->query->read('dlogin')
            ->where(['userid' => $userid, "dpassword" => md5($oldPass)])
            ->get('dpassword', false);

        if (!empty($user)) {

            if ($this->helper->update("dlogin", ["dpassword" => CommonFunctions::hashPassword($newPass)], ["userid" => $userid])) {
                $result = [
                    'ACCESS_CODE' => 'ACCESS',
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
