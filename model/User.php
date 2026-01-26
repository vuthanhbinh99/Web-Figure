<?php
class User {
    private $conn;
    private $table = "users";

    public function __construct($db){
        $this->conn = $db;
    }

    public function create($id_users, $full_name, $email, $phone, $address, $id_accounts){
        try {
            $sql = "INSERT INTO $this->table 
                    (id_users, full_name, email, phone, address, id_accounts, created_at)
                    VALUES (:id_users, :full_name, :email, :phone, :address, :id_accounts, NOW())";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_users', $id_users, PDO::PARAM_STR);
            $stmt->bindParam(':full_name', $full_name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':address', $address, PDO::PARAM_STR);
            $stmt->bindParam(':id_accounts', $id_accounts, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }

    public function getByAccountId($id_accounts){
        try {
            $sql = "SELECT * FROM $this->table WHERE id_accounts = :id_accounts";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_accounts', $id_accounts, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return null;
        }
    }

    public function getById($id_users){
        try {
            $sql = "SELECT * FROM $this->table WHERE id_users = :id_users";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_users', $id_users, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return null;
        }
    }

    public function update($id_users, $full_name, $email, $phone, $address){
        try {
            $sql = "UPDATE $this->table 
                    SET full_name = :full_name, email = :email, phone = :phone, address = :address 
                    WHERE id_users = :id_users";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':full_name', $full_name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':address', $address, PDO::PARAM_STR);
            $stmt->bindParam(':id_users', $id_users, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
