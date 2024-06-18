<?php


class Model
{

    private $connection; // Instance of the Connection class
    private $pdo;
    private $query;
    private $helper;
    private $jwt;
    public function __construct(Database $connection)
    {
        $this->connection = $connection;
        $this->pdo = $this->connection->getPDO();
        $this->unique = CommonFunctions::generateUniqueID();
        $this->date = CommonFunctions::getDate('1 hour');
        $this->query = new QueryBuilder($this->connection->getPDO());
        $this->helper = new Helper($this->connection->getPDO());
        $this->jwt = new JWTHandler();
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
            $result = [
                "userid" => $user["userid"],
                "firstname" => $user["dfirstname"],
                "lastname" => $user["dlastname"],
                "fullname" => $user["dfullname"],
                "phone" => $user["dphone"],
                "email" => $user["demail"],
                "computerId" => $user["computerId"],
                "natureEmployed" => $user["natureEmployed"],
                "yearEmployed" => $user["yearEmployed"],
                "department" => $user["ddepartment"],
                "designation" => $user["ddesignation"],
                "residentAddress" => $user["residentAddress"],
                "paymentSlip" => $user["paymentSlip"],
                "passport" => $user["dpassport"],
                "nextOfKinName" => $user["nextOfKinName"],
                "nextOfKinGender" => $user["nextOfKinGender"],
                "nextOfKinRelationship" => $user["nextOfKinRelationship"],
                "nextOfKinPhone" => $user["nextOfKinPhone"],
                "accountName" => $user["accountName"],
                "accountNumber" => $user["accountNumber"],
                "bankName" => $user["bankName"],
                "accountStatus" => $user["account_status"],
                "status" => $user["dstatus"],
            ];
            if (hash_equals($hashedPassword, $user["dpassword"])) {
                //TODO: CHECK THE STATUS OF THE ACCOUNT
                if ($user["dstatus"] == 'pending') {
                    $name = $user["dfirstname"];
                    $userid = $user["userid"];
                    $pin = rand(1234, 5678);
                    //TODO: SEND PIN TO EMAIL & SMS
                    $subject = "Verification | SAMOGOZA";
                    $msg = "
                    <b>Dear $name,</b>  welcome to SAMOGOZA LTD.
                    <P>Use this code <b>$pin</b> to verify your account.</P>                    
                    ";
                    CommonFunctions::sendMail($msg, $email, $subject);
                    $this->helper->update("dlogin", ["dpin" => $pin], ["userid" => $userid]);
                    // http_response_code(401);
                    $data = [
                        'accessCode' => 'VERIFICATION',
                        "data" => $result,
                    ];
                } elseif ($user["dstatus"] == 'banned') {
                    http_response_code(400);
                    $data = [
                        'ACCESS_CODE' => 'DENIED',
                        'msg' => "Your account has been banned, you cannot login to your account."
                    ];
                } else {
                    $token = $this->jwt->generateToken([
                        'userid' => $user["userid"],
                        "fullname" => $user["dfullname"],
                        'email' => $user["demail"],
                        'phone' => $user["dphone"]
                    ]);
                    $data = [
                        "accessCode" => 'GRANTED',
                        "data" => $result,
                        'token' => $token,
                    ];
                }
            } else {
                http_response_code(400);
                $data = [
                    'ACCESS_CODE' => 'DENIED',
                    'msg' => "Incorect password provided."
                ];
            }
        } else {
            http_response_code(400);
            $data = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "Sorry, we don't have the information you provided."
            ];
        }

        return $data; // Login failed
    }


    //  TODO:VERIFY ACCOUNT
    public function resendToken($userid)
    {
        $user = $this->query->read('dlogin')
            ->where(['userid' => $userid])
            ->get('dfirstname, demail, userid', false);

        if (!empty($user)) {

            $name = $user["dfirstname"];
            $email = $user["demail"];
            $userid = $user["userid"];
            $pin = rand(1234, 5678);
            //TODO: SEND PIN TO EMAIL & SMS
            $subject = "Verification | SAMOGOZA";
            $msg = "
            <b>Dear $name,</b>  welcome to SAMOGOZA LTD.
            <P>Use this code <b>$pin</b> to verify your account.</P>                    
            ";
            CommonFunctions::sendMail($msg, $email, $subject);
            $this->helper->update("dlogin", ["dpin" => $pin], ["userid" => $userid]);
            $data = [
                'ACCESS_CODE' => 'Success',
                'msg' => "Token has been sent to your account"
            ];
        } else {
            http_response_code(400);
            $data = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "Sorry, invalid code provided."
            ];
        }

        return $data;
    }

    //  TODO:GET TOTAL APPROVED LOANS
    public function getTotalLoans($userid)
    {
        $result = $this->helper->getRecord('drequest', "SUM(totalBalance) AS total", " WHERE userid='$userid' AND (dstatus ='pending' OR dstatus='approved')");
        $getPendng = $this->helper->getRecord('drequest', "dstatus", " WHERE userid='$userid'");


        if (!empty($result)) {
            $data = [
                "totalApply" => is_null($result["total"]) ? '0' : $result["total"],
                "getPendng" => empty($getPendng["dstatus"]) ? '' : $getPendng["dstatus"],
            ];
        }
        return  $data;
    }

    //  TODO:GET ALL LOANS REQUEST
    public function getLoans($userid)
    {
        $result = $this->query->read('drequest')
            ->where(['userid' => $userid])
            ->orderBy("id DESC")
            ->get('*', true);

        $data = [];
        if (!empty($result)) {
            foreach ($result as $row) {
                $data[] = [
                    "dduration" => $row['dduration'],
                    "amountApply" => $row['amountApply'],
                    "amountRequest" => $row['amountRequest'],
                    "amountSpread" => $row['amountSpread'],
                    "spreadPeriod" => $row['spreadPeriod'],
                    "amountDeducted" => $row['amountDeducted'],
                    "totalInterest" => $row['totalInterest'],
                    "totalPayment" => $row['totalPayment'],
                    "totalBalance" => $row['totalBalance'],
                    "dstatus" => $row['dstatus'],
                    "ddate" => $row['ddate'],
                    "approveDate" => $row['approveDate'],
                ];
                # code...
            }
        }
        return  $data;
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
                "fullname" => $user["dfullname"],
                "phone" => $user["dphone"],
                "email" => $user["demail"],
                "computerId" => $user["computerId"],
                "natureEmployed" => $user["natureEmployed"],
                "yearEmployed" => $user["yearEmployed"],
                "department" => $user["ddepartment"],
                "designation" => $user["ddesignation"],
                "residentAddress" => $user["residentAddress"],
                "paymentSlip" => $user["paymentSlip"],
                "passport" => $user["dpassport"],
                "nextOfKinName" => $user["nextOfKinName"],
                "nextOfKinGender" => $user["nextOfKinGender"],
                "nextOfKinRelationship" => $user["nextOfKinRelationship"],
                "nextOfKinPhone" => $user["nextOfKinPhone"],
                "accountName" => $user["accountName"],
                "accountNumber" => $user["accountNumber"],
                "bankName" => $user["bankName"],
                "accountStatus" => $user["account_status"],
                "status" => 'active',
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
                    $subject = "Verification | SAMOGOZA";
                    $msg = "
                    <b>Dear $name,</b>  welcome to SAMOGOZA LTD.
                    <P>Use this code <b>$pin</b> to verify your account.</P>                    
                    ";
                    CommonFunctions::sendMail($msg, $email, $subject);

                    $token = $this->jwt->generateToken([
                        'userid' => $data["userid"],
                        "fullname" => $data["dfullname"],
                        'email' => $data["demail"],
                        'phone' => $data["dphone"]
                    ]);
                    $result = [
                        'token' => $token,
                        'data' => [
                            'accessCode' => 'GRANTED',
                            'userid' => $data["userid"],
                            'msg' => "Verify your account with the code sent to your email address or phone number."
                        ],
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
            ->where(['userid' => $userid, "dpassword" => CommonFunctions::hashPassword($oldPass)])
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



    public function applyForLoan(string $email, string $phone, string $name, array $data)
    {

        //TODO: check if email and phone number exist with ID 
        $amount = CommonFunctions::formatNaira($data['amountApply']);

        if ($this->helper->create("drequest", $data)) {
            $rid = $data['rid'];

            $row =  $this->query->read('drequest')
                ->where(['rid' => $rid])
                ->get('*', false);
            $data = [
                "dduration" => $row['dduration'],
                "amountApply" => $row['amountApply'],
                "amountRequest" => $row['amountRequest'],
                "amountSpread" => $row['amountSpread'],
                "spreadPeriod" => $row['spreadPeriod'],
                "amountDeducted" => $row['amountDeducted'],
                "totalInterest" => $row['totalInterest'],
                "totalPayment" => $row['totalPayment'],
                "totalBalance" => $row['totalBalance'],
                "dstatus" => $row['dstatus'],
                "ddate" => $row['ddate'],
                "approveDate" => $row['approveDate'],
            ];

            $subject = "LOAN REQUEST | SAMOGOZA";
            $msg = "
            <b>Dear $name,</b>
            <P>Your request for a loan of <b>$amount</b> has been received, 
            you will receive a message once your loan request has been approved.</P>                    
            ";
            CommonFunctions::sendMail($msg, $email, $subject);
        } else {
            http_response_code(400);
            $data = [
                'accessCode' => 'DENIED',
                'msg' => "Sorry, Something went wrong."
            ];
        }
        return $data;
    }

    public function letterRequest(string $email, string $name, array $data)
    {
        $letter = $data['dletter'];
        if ($this->helper->create("dletter", $data)) {
            $subject = "LETTER REQUEST | SAMOGOZA";
            $msg = "
            <b>Dear $name,</b>
            <P>Your request for <b>$letter</b> has been received, 
            your letter will be sent to this email once the admin confirm your request.</P>                    
            ";
            CommonFunctions::sendMail($msg, $email, $subject);
            $data = [
                'accessCode' => 'SUCCESS',
                'msg' => "Message sent to your email."
            ];
        }else{
            http_response_code(400);
            $data = [
                'accessCode' => 'DENIED',
                'msg' => "Sorry, Something went wrong."
            ];
        }
    }

    public function updatePersonalDetails(array $data, array $clause)
    {

        //TODO: update details
        if ($this->helper->update("dlogin", $data, $clause)) {
            $userid = $clause['userid'];
            $user = $this->helper->getSingleRecord('dlogin', "WHERE userid = '$userid'");

            if (!empty($user)) {
                $result = [
                    "userid" => $user["userid"],
                    "firstname" => $user["dfirstname"],
                    "lastname" => $user["dlastname"],
                    "fullname" => $user["dfullname"],
                    "phone" => $user["dphone"],
                    "email" => $user["demail"],
                    "computerId" => $user["computerId"],
                    "natureEmployed" => $user["natureEmployed"],
                    "yearEmployed" => $user["yearEmployed"],
                    "department" => $user["ddepartment"],
                    "designation" => $user["ddesignation"],
                    "residentAddress" => $user["residentAddress"],
                    "paymentSlip" => $user["paymentSlip"],
                    "passport" => $user["dpassport"],
                    "nextOfKinName" => $user["nextOfKinName"],
                    "nextOfKinGender" => $user["nextOfKinGender"],
                    "nextOfKinRelationship" => $user["nextOfKinRelationship"],
                    "nextOfKinPhone" => $user["nextOfKinPhone"],
                    "accountName" => $user["accountName"],
                    "accountNumber" => $user["accountNumber"],
                    "bankName" => $user["bankName"],
                    "accountStatus" => $user["account_status"],
                    "status" => $user["dstatus"],
                ];
            } else {
                http_response_code(400);
                $result = [
                    'ACCESS_CODE' => 'DENIED',
                    'msg' => "Sorry, we\'re unable to submit your request."
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

    public function getData(string $table)
    {
        $row =  $this->query->read($table)
            ->get('*', false);

        if (!empty($row)) {
            $result = [
                "text" => $row["text"],
            ];
        } else {
            http_response_code(400);
            $result = [
                'ACCESS_CODE' => 'DENIED',
                'msg' => "Sorry, we\'re unable to fetch data."
            ];
        }

        return $result;
    }
}
