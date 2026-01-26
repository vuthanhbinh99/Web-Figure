<?php 
class Review {
    private $conn;
    private $table = "reviews";

    public function __construct($db){
        $this->conn = $db;
    }

    public function create($id_reviews, $id_products, $id_users, $rating, $comment){
        try {
            $sql = "INSERT INTO " . $this->table . "(id_reviews, id_products, id_users, rating, comment, created_at) 
                    VALUES (:id_reviews, :id_products, :id_users, :rating, :comment, NOW())";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_reviews', $id_reviews, PDO::PARAM_STR);
            $stmt->bindParam(':id_products', $id_products, PDO::PARAM_STR);
            $stmt->bindParam(':id_users', $id_users, PDO::PARAM_STR);
            $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }

    public function getByProductId($id_products){
        try {
            $sql = "SELECT * FROM " . $this->table . " WHERE id_products = :id_products";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_products', $id_products, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return [];
        }
    }
}
?>
