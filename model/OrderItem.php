<?php
class OrderItem {
    private $conn;
    private $table = "order_items";

    public function __construct($db){
        $this->conn = $db;
    }

    public function create($id_order_items, $id_order, $id_products, $quantity, $price){
        try {
            $sql = "INSERT INTO $this->table 
                    (id_order_items, id_order, id_products, quantity, price) 
                    VALUES (:id_order_items, :id_order, :id_products, :quantity, :price)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_order_items', $id_order_items, PDO::PARAM_STR);
            $stmt->bindParam(':id_order', $id_order, PDO::PARAM_STR);
            $stmt->bindParam(':id_products', $id_products, PDO::PARAM_STR);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindParam(':price', $price);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("OrderItem creation error: " . $e->getMessage());
            return false;
        }
    }

    public function getByOrderId($id_order){
        try {
            $sql = "SELECT oi.*, p.name 
                    FROM order_items oi
                    JOIN products p ON p.id_products = oi.id_products
                    WHERE oi.id_order = :id_order";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_order', $id_order, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return [];
        }
    }
}
?>
