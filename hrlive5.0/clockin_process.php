<?php
    //*get staff enrollment image from hr_register
    $row = $query->table('hr_register')
        ->where("staffcode ='$staffcode'")
        ->get(['account, fullname, position, joblocation, lux_image_profile, lux_uuid '], true);

    //? get schedule from schedule table
    $timer = $query->table('attendance_schedules')
        ->where("staffcode ='$staffcode'")
        ->where("dpresent='no'", '')
        ->orderBy('id', 'DESC')
        ->limit(1)
        ->get(['dtimein, dtimeout, dgrace, schedule_id'], true);

    if (empty($timer)) {
        //check timer from attendance_settings table
        $timer = $model->getSingleRecord('attendance_settings', 'dtimein, dtimeout, dgrace');
        $scheduleId = NULL;
    } else {
        $scheduleId = $timer['schedule_id'];
        //update the schedule to yes 
        $model->updateRecord('attendance_schedules', ['dpresent' => 'yes'], "schedule_id ='$scheduleId'");
    }
    // print_r($timer);
    // die;
    
    $mins = $minsLate = 0;

    $time = gmdate("H:i:s", strtotime("+1hour"));
    $timeInSet = $timer['dtimein'];
    $grace = $timer['dgrace'];

    $workTime = gmdate("H:i:s", strtotime("$timeInSet +$grace minutes"));
    $workTime1 = gmdate("H:i:s", strtotime("$timeInSet"));

    if (strtotime($time) < strtotime($timeInSet)) {
        $dsignInStatus = 'early';
        $mins = CommonFunctions::minuteRange($workTime1, $time);
    } elseif (strtotime($time) >= strtotime($workTime)) {
        $dsignInStatus = 'late';
        $minsLate =  CommonFunctions::minuteRange($workTime, $time);
    } else {
        $dsignInStatus = 'base';
    }

    //record to create new attendance
    $info = array(
        "attendance_schedule_id" => "$scheduleId",
        "account" => $row['account'],
        "staffcode" => $staffcode,
        "fullname" => $row['fullname'],
        "position" => $row['position'],
        "joblocation" => $row['joblocation'],
        "dlat_location" => $latitude,
        "dlong_location" => $longitude,
        "daddress_location" => $address,
        "ddate" => $date,
        //! time in and out with grace period
        "req_timein" => $timer['dtimein'],
        "dgrace" => $timer['dgrace'],
        "req_timeout" => $timer['dtimeout'],
        "dclock_status" => $dsignInStatus,
        "act_timein" => $time,
        "dtime_early" => $mins,
        "dtime_base" => $mins,
        "dtime_late" => $minsLate,
        //!staff image and uuid
        "lux_profile_image" => $row['lux_image_profile'],
        "lux_profile_uuid" => $row['lux_uuid'],
        //!from luxand response data
        "lux_clockin_image" => $data['url'],
        "lux_clockin_uuid" => $data['uuid'],
    );
