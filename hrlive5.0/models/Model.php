<?php

class Model {

    private $connection; // Instance of the Connection class
    private $pdo;
    private $unique;
    private $date;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->pdo = $this->connection->getPDO();
        $this->unique = CommonFunctions::generateUniqueID();
        $this->date = CommonFunctions::getDateTime(1); 
    }

 
    // User Login
    public function login($email, $password) {
        $query = "SELECT * FROM `dlogin` WHERE demail = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Verify the MD5-hashed password
            $hashedPassword = md5($password);
            if ($hashedPassword === $user['dpass']) {
                return $user['userid']; // Return the user ID upon successful login
            }
        }
        
        return false; // Login failed
    }

    public function getUser($userid, $column="*") {
        $query = "SELECT $column FROM `dlogin` WHERE userid = :userid";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userid', $userid);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
 

   
    //get All Record
    public function getAllRecord($tableName, $clause = '') {
        $query = "SELECT * FROM $tableName";
        if (!empty($clause)) {
            $query .= " $clause";
        }
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    //get single record from database
    public function getSingleRecord($tableName, $selector='*',  $clause = '') {
        $query = "SELECT $selector FROM $tableName";
        if (!empty($clause)) {
            $query .= " $clause";
        }
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    // Delete Anything from
    public function deleteRecord($tableName, $row, $rowId) {
        $query = "DELETE FROM $tableName WHERE $row = :rowId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':rowId', $rowId);
        $stmt->execute();         
    }
   
  
    public  function updateRecord($tableName, $updateData, $condition) {
        try {
            // Build SQL query
            $sql = "UPDATE $tableName SET ";
    
            $setValues = array();
            foreach ($updateData as $column => $value) {
                $setValues[] = "$column = :$column";
            }
            $sql .= implode(", ", $setValues);
    
            $sql .= " WHERE $condition";
    
            // Prepare and execute the statement
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($updateData);
    
            // Return the number of affected rows
            return $stmt->rowCount();
        } catch (PDOException $e) {
            // Handle exceptions here
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
   

    function createNewRecord($tableName, $data) {
        try {   
            // Prepare SQL statement
            $sql = "INSERT INTO $tableName SET ";
            $sql .= implode(', ', array_map(function ($key) {
                return "$key = :$key";
            }, array_keys($data)));
    
            // Prepare the statement
            $stmt = $this->pdo->prepare($sql);
    
            // Bind parameters dynamically
            foreach ($data as $key => $value) {
                $stmt->bindParam(":$key", $data[$key]);
            }
    
            // Execute the statement
            $stmt->execute();
    
            // Optionally, you can return the last inserted ID
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            // Handle exceptions here
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    

}



