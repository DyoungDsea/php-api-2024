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



    //TODO CREATE RECORD
    public function create(string $table, array $values)
    {
        $columns = implode(', ', array_keys($values));
        $setClause = implode(', ', array_map(fn ($col) => "$col = :$col", array_keys($values)));
        $sql = "INSERT INTO $table SET $setClause"; // Using SET clause

        try {
            // Prepare the statement
            $stmt = $this->pdo->prepare($sql);
            foreach ($values as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            // Handle exceptions
            return false;
        }
    }



    //TODO UPDATE ANY TABLE
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


    //TODO DELETE RECORD
    public function remove(string $table, array $clause)
    {
        $whereClause = implode(' AND ', array_map(fn ($column) => "$column = :$column", array_keys($clause)));
        $sql = "DELETE FROM $table WHERE $whereClause";

        try {
            $stmt = $this->pdo->prepare($sql);
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
}
