<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require("./requires.php");

$email = clean($_POST['user']);
$pass = md5(clean($_POST['pass']));

try {
    // Create a PDO connection
    $pdo = createPDOConnection();

    $sql = "SELECT userid, username, drefCode, dfname, demail, dphone, dwallet, dpass, ddob, dstatus, ddate FROM dlogin WHERE (demail=:email OR username=:email) AND dpass=:pass";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $res = [];
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['dstatus'] == 'banned') {
            http_response_code(400); // Banned
            $data = [
                'success' => true,
                'user' => 'banned',
                'msg' => 'Your account has been banned',
                'error' => 'user_not_found',
            ];
        } else {
            http_response_code(200); // OK
            $res = $row;
            $data = [
                'success' => true,
                'user' => $res,
                'msg' => 'Success',
                'error' => '',
            ];
        }
    } else {
        http_response_code(400); // OK
        $data = [
            'success' => false,
            'msg' => 'Username or Email not found',
            'error' => 'user_not_found',
        ];
    }

    echo json_encode($data);
} catch (PDOException $e) {
    echo json_encode(["error" => "An error occurred: " . $e->getMessage()]);
}
