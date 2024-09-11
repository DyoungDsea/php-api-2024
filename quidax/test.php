<?php


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://www.quidax.com/api/v1/users/me/wallets/btc/addresses');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer FSfm6a2kJeAdWB3PBmnA5rsSvd5flT5xtnYM8ZWO';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
echo $result;
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);


// $response = json_decode($result, true);