<?php

$result = [
    "driverId" => $user["driver_id"],
    "driverName" => $user["driver_name"],
    "phoneNumber" => $user["phone_number"],
    "emailAddress" => $user["email_address"],
    "photo" => empty($user["driver_photo"]) ? "" :$user["driver_photo"],
    "contactAddress" => empty($user["daddress"]) ? "" : $user["daddress"],
    "licenseNumber" => empty($user["licenseNumber"]) ? "" : $user["licenseNumber"],
    "frontView" => empty($user["frontView"]) ? "" : $user["frontView"],
    "lastupdate" => empty($user["lastupdate"]) ? "" : $user["lastupdate"],
    "walletBalance" => $user["wallet_balance"],
    "status" => $user["status"],
    "carVerification" => $user["carVerification"],
    "driverLicenceFront" => !empty($user["driverLicenceFront"]) ? $user["driverLicenceFront"] : "",
    "engineView" => !empty($user["engineView"]) ? $user["engineView"] : ""

];