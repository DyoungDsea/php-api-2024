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

            return $picid.'.jpg';
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

        // Prepare the query dynamically
        $setClause = implode(', ', array_map(fn ($col) => "$col = :$col", array_keys($values)));

        $whereClause = '';
        foreach ($clause as $column => $condition) {
            $whereClause .= "$column = :$column AND ";
        }
        $whereClause = rtrim($whereClause, ' AND ');

        $sql = "UPDATE $table SET $setClause WHERE $whereClause";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_merge($values, $clause));
            return true; // Record updated successfully
        } catch (PDOException $e) {
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
}
