<?php
class Account {
    private $conn;
    private $table = "accounts";

    public function __construct($db){
        $this->conn = $db;
    }

    public function register($id_accounts, $username, $password, $role = "customer"){
        $sql = "INSERT INTO accounts(id_accounts, username, password, role, created_at) 
                VALUES (:id_accounts, :username, :password, :role, NOW())";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_accounts', $id_accounts, PDO::PARAM_STR);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }

    public function getByUsername($username){
        $sql = "SELECT * FROM $this->table WHERE username = :username";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return null;
        }
    }

    public function getById($id_accounts){
        $sql = "SELECT * FROM $this->table WHERE id_accounts = :id_accounts";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_accounts', $id_accounts, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return null;
        }
    }

    public function getAccountWithUser($username){
        $sql = "SELECT a.*, u.full_name, u.email 
                FROM accounts a
                LEFT JOIN users u ON a.id_accounts = u.id_accounts
                WHERE a.username = :username";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return null;
        }
    }
}
?>
