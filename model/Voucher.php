<?php
class Voucher {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Lấy voucher theo mã
     */
    public function getByCode($code) {
        $stmt = $this->conn->prepare("
            SELECT * FROM vouchers 
            WHERE code = :code AND active = 1 AND expires_at > NOW()
            LIMIT 1
        ");
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Lấy tất cả vouchers active
     */
    public function getAll() {
        $stmt = $this->conn->prepare("
            SELECT * FROM vouchers 
            WHERE active = 1 AND expires_at > NOW()
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Tạo voucher mới
     */
    public function create($id_voucher, $code, $discount_percent, $max_uses, $expires_at) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO vouchers (id_voucher, code, discount_percent, max_uses, used_count, expires_at, active, created_at)
                VALUES (:id_voucher, :code, :discount_percent, :max_uses, 0, :expires_at, 1, NOW())
            ");
            
            $stmt->bindParam(':id_voucher', $id_voucher, PDO::PARAM_STR);
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->bindParam(':discount_percent', $discount_percent, PDO::PARAM_INT);
            $stmt->bindParam(':max_uses', $max_uses, PDO::PARAM_INT);
            $stmt->bindParam(':expires_at', $expires_at, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Voucher create error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật số lần dùng
     */
    public function incrementUsageCount($code) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE vouchers 
                SET used_count = used_count + 1 
                WHERE code = :code
            ");
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Voucher increment error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vô hiệu hóa voucher
     */
    public function deactivate($code) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE vouchers 
                SET active = 0 
                WHERE code = :code
            ");
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Voucher deactivate error: " . $e->getMessage());
            return false;
        }
    }
}
?>
