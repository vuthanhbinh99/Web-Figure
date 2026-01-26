<?php
class Cart {
    private $conn;
    private $table = "carts";

    public function __construct($db){
        $this->conn = $db;
    }

    public function create($id_carts, $id_users, $id_products, $quantity){
        try {
            $sql = "INSERT INTO " . $this->table . "(id_carts, id_users, id_products, quantity, created_at) 
                    VALUES (:id_carts, :id_users, :id_products, :quantity, NOW())";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_carts', $id_carts, PDO::PARAM_STR);
            $stmt->bindParam(':id_users', $id_users, PDO::PARAM_STR);
            $stmt->bindParam(':id_products', $id_products, PDO::PARAM_STR);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }

    public function getByUserId($id_users){
        try {
            $sql = "SELECT * FROM " . $this->table . " WHERE id_users = :id_users";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_users', $id_users, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return [];
        }
    }

    public function updateQuantity($id_carts, $quantity){
        try {
            $sql = "UPDATE " . $this->table . " SET quantity = :quantity WHERE id_carts = :id_carts";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindParam(':id_carts', $id_carts, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id_carts){
        try {
            $sql = "DELETE FROM " . $this->table . " WHERE id_carts = :id_carts";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_carts', $id_carts, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }
}
?>