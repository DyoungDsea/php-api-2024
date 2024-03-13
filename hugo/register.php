<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
require("./requires.php");

$name = clean($_POST['name']);
$username = clean($_POST['username']);
$phone = clean($_POST['phone']);
$email = clean($_POST['email']);
$pass = md5(clean($_POST['pass']));

try {
    // Create a PDO connection
    $pdo = createPDOConnection();

    // Check if the username is already taken
    $stmt = $pdo->prepare("SELECT * FROM dlogin WHERE username = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        // Check if the email is already taken
        $stmt = $pdo->prepare("SELECT * FROM dlogin WHERE demail = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            // Generate a unique user ID and username
          
            // Insert user details
            $stmt = $pdo->prepare("INSERT INTO dlogin SET userid = :code, username = :username, demail = :email, dfname = :name, dphone = :phone, dpass = :pass, ddate = :date");
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR); // Make sure to define $date

            if ($stmt->execute()) {
               
                // Fetch user details
                $stmt = $pdo->prepare("SELECT id FROM dlogin WHERE userid = :code");
                $stmt->bindParam(':code', $code, PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                $myId = $user['id'] . rand(5466372, 8987655); 
            
                $stmt = $pdo->prepare("UPDATE dlogin SET drefCode = :myId WHERE userid = :code AND demail = :email");
                $stmt->bindParam(':myId', $myId, PDO::PARAM_STR);
                $stmt->bindParam(':code', $code, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();

                 // Fetch user details
                 $stmt = $pdo->prepare("SELECT userid, username, drefCode, dfname, demail, dphone, dwallet, dpass, ddob, dstatus, ddate FROM dlogin WHERE userid = :code");
                 $stmt->bindParam(':code', $code, PDO::PARAM_STR);
                 $stmt->execute();
                 $row = $stmt->fetch(PDO::FETCH_ASSOC);

                http_response_code(200); // OK
                $data = [
                    'success' => true,
                    'user' => $row,
                    'msg' => 'Success',
                    'error' => '',
                ];
            } else {
                http_response_code(400);
                $data = [
                    'success' => false,
                    'msg' => "Sorry, we're unable to create your account. Please try again later!",
                    'error' => 'user_not_found',
                ];
            }
        } else {
            http_response_code(400);
            $data = [
                'success' => false,
                'msg' => 'Email already taken!',
                'error' => 'user_not_found',
            ];
        }
    } else {
        http_response_code(400);
        $data = [
            'success' => false,
            'msg' => 'Username already taken!',
            'error' => 'user_not_found',
        ];
    }

    echo json_encode($data);
} catch (PDOException $e) {
    echo json_encode(["error" => "An error occurred: " . $e->getMessage()]);
}

