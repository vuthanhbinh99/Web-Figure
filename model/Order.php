<?php
class Order {
    private $conn;
    private $table = "orders";

    public function __construct($db){
        $this->conn = $db;
    }

    public function createOrder($id_accounts, $cart, $payment_method, $full_name = '', $phone = '', $address = ''){
        if (!is_array($cart) || count($cart) === 0) return false;

        try {
            $total = 0;
            foreach ($cart as $item) {
                $total += (float)$item['price'] * (int)$item['quantity'];
            }

            $id_order = uniqid('ORD');

            $sql = "INSERT INTO $this->table 
                    (id_order, id_accounts, total, status, payment_method, full_name, phone, address, created_at) 
                    VALUES (:id_order, :id_accounts, :total, 'pending', :payment_method, :full_name, :phone, :address, NOW())";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_order', $id_order, PDO::PARAM_STR);
            $stmt->bindParam(':id_accounts', $id_accounts, PDO::PARAM_STR);
            $stmt->bindParam(':total', $total);
            $stmt->bindParam(':payment_method', $payment_method, PDO::PARAM_STR);
            $stmt->bindParam(':full_name', $full_name, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':address', $address, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                throw new Exception("Lỗi execute Order");
            }

            // ✅ Tạo order items
            $order_items = new OrderItem($this->conn);
            foreach ($cart as $item) {
                $id_order_item = uniqid('ITEM');
                $qty = (int)$item['quantity'];
                $prc = (float)$item['price'];
                
                $id_products = $item['id_products'] ?? $item['id'] ?? null;
                
                if (!$id_products) {
                    throw new Exception("Thiếu ID sản phẩm trong giỏ hàng");
                }
                
                $order_items->create($id_order_item, $id_order, $id_products, $qty, $prc);
            }

            return $id_order;
        } catch (Exception $e) {
            error_log("Order creation error: " . $e->getMessage());
            return false;
        }
    }

    public function getById($id_order){
        try {
            $sql = "SELECT * FROM $this->table WHERE id_order = :id_order";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_order', $id_order, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return null;
        }
    }

    public function getByUserId($id_accounts){
        try {
            $sql = "SELECT * FROM $this->table WHERE id_accounts = :id_accounts";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_accounts', $id_accounts, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return [];
        }
    }

    public function getAllOrders() {
        try {
            $sql = "SELECT o.*, a.username 
                    FROM orders o
                    LEFT JOIN accounts a ON o.id_accounts = a.id_accounts
                    ORDER BY o.created_at DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return [];
        }
    }

    public function updateStatus($id_order, $status){
        try {
            $sql = "UPDATE orders SET status = :status WHERE id_order = :id_order";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':id_order', $id_order, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }

    public function getItemsByOrderId($id_order) {
        try {
            $sql = "SELECT oi.*, p.name
                    FROM order_items oi
                    LEFT JOIN products p ON oi.id_products = p.id_products
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