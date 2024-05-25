<?php

class Helper
{
    private $connection; // Instance of the Connection class
    private $pdo;
    private $unique;
    private $query;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->unique = CommonFunctions::generateUniqueID();
    }



    public function getUser($userid, $column = "*")
    {
        $query = "SELECT $column FROM `dlogin` WHERE userid = :userid";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userid', $userid);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    function getKey($table, $column)
    {
        $stmt = $this->pdo->prepare("SELECT $column FROM $table");
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data[$column];
    }

    //get All Record
    public function getAllRecord($tableName, $clause = '')
    {
        $query = "SELECT * FROM $tableName";
        if (!empty($clause)) {
            $query .= " $clause";
        }
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllRecordWithSelector($tableName, $selector = "*", $clause = '')
    {
        $query = "SELECT $selector FROM $tableName";
        if (!empty($clause)) {
            $query .= " $clause";
        }
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




    //get single record from database
    public function getSingleRecord($tableName, $clause = '')
    {
        $query = "SELECT * FROM $tableName";
        if (!empty($clause)) {
            $query .= " $clause";
        }
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function imagesUpload($fileName, $id = '', $x = 400, $y = 400)
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
                $foo->Process('files');

                if ($foo->processed) {
                    $foo->Clean();
                }
            }

            return $picid . '.jpg';
        }
    }


    //TODO create new record
    public function create(string $table, array $values)
    {
        // Prepare the query dynamically
        $columns = implode(', ', array_keys($values));
        $setClause = implode(', ', array_map(fn ($col) => "$col = :$col", array_keys($values)));

        $sql = "INSERT INTO $table SET $setClause";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    //TODO update any table
    public function update(string $table, array $values, array $clause)
    {
        $setClause = implode(', ', array_map(fn ($col) => "$col = :$col", array_keys($values)));
        $whereClause = implode(' AND ', array_map(fn ($column) => "$column = :$column", array_keys($clause)));
        $sql = "UPDATE $table SET $setClause WHERE $whereClause";
        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($values as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            foreach ($clause as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            // Handle exceptions
            return false;
        }
    }


    //TODO delete from any table
    public function remove(string $table, array $clause)
    {

        // Prepare the query dynamically
        $whereClause = '';
        foreach ($clause as $column => $condition) {
            $whereClause .= "$column = :$column AND ";
        }
        $whereClause = rtrim($whereClause, ' AND ');

        $sql = "DELETE FROM $table WHERE $whereClause";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($clause);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }


    public  function getClosestDriver($lat, $lon, $distance)
    {

        // Haversine formula SQL query to find the closest driver
        $sql = "SELECT driver_id, driver_name, phone_number, driver_latitude, driver_longitude, car_type, driver_photo, plateNumber, car_category,  (6371 * acos(cos(radians(:lat)) * cos(radians(driver_latitude)) * cos(radians(driver_longitude) - radians(:lon)) + sin(radians(:lat)) *  sin(radians(driver_latitude)))) AS distance FROM manage_drivers WHERE availability='Free' ORDER BY distance ASC LIMIT 1";
        // $sql = "SELECT driver_id, driver_name, phone_number, driver_latitude, driver_longitude, car_type, driver_photo, plateNumber, car_category,  (6371 * acos(cos(radians(:lat)) * cos(radians(driver_latitude)) * cos(radians(driver_longitude) - radians(:lon)) + sin(radians(:lat)) *  sin(radians(driver_latitude)))) AS distance FROM manage_drivers WHERE availability='Free' HAVING distance <= :distance ORDER BY distance ASC LIMIT 1";


        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':lat', $lat);
        $stmt->bindParam(':lon', $lon);
        // $stmt->bindParam(':distance', $distance);
        $stmt->execute();

        $driver = $stmt->fetch(PDO::FETCH_ASSOC);

        return $driver;
    }


    public function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the Earth in kilometers

        // Convert latitude and longitude from degrees to radians
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        // Calculate the differences between the latitudes and longitudes
        $latDiff = $lat2Rad - $lat1Rad;
        $lonDiff = $lon2Rad - $lon1Rad;

        // Calculate the Haversine distance
        $a = sin($latDiff / 2) * sin($latDiff / 2) + cos($lat1Rad) * cos($lat2Rad) * sin($lonDiff / 2) * sin($lonDiff / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c; // Distance in kilometers

        return $distance;
    }


    public  function calculateDrivingTime($distanceKm, $averageSpeedKph)
    {
        $timeHours = $distanceKm / $averageSpeedKph;
        $timeMinutes = $timeHours * 60;

        return $timeMinutes;
    }
}
