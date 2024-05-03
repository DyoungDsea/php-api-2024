<?php

class Helper
{
    private $pdo;
    private $unique;
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


 
    //TODO: get All Record
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

    //TODO: RECORD WITH SELECTOR
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




    //TODO get single record from database
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
 


    //TODO create new record
    public function create(string $table, array $values)
    {
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

        $whereClause = '';
        foreach ($clause as $column => $condition) {
            $whereClause .= "$column = :$column AND ";
        }
        $whereClause = rtrim($whereClause, ' AND ');

        $sql = "UPDATE $table SET $setClause WHERE $whereClause";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_merge($values, $clause));
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    //TODO delete from any table
    public function remove(string $table, array $clause)
    {
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
