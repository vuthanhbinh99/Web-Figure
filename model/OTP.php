<?php
class OTP {
    private $conn;
    private $table = "otps";

    public function __construct($db){
        $this->conn = $db;
    }

    // Tạo mã OTP mới
    public function create($id_otp, $email, $otp_code){
        try {
            // Xóa OTP cũ của email này
            $this->deleteByEmail($email);

            $sql = "INSERT INTO $this->table 
                    (id_otp, email, otp_code, created_at, expires_at) 
                    VALUES (:id_otp, :email, :otp_code, NOW(), DATE_ADD(NOW(), INTERVAL 2 MINUTE))";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_otp', $id_otp, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':otp_code', $otp_code, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }

    // Xác thực OTP
    public function verify($email, $otp_code){
        try {
            $sql = "SELECT * FROM $this->table 
                    WHERE email = :email 
                    AND otp_code = :otp_code 
                    AND expires_at > NOW()
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':otp_code', $otp_code, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch();
            
            if ($result) {
                // Xóa OTP sau khi xác thực thành công
                $this->deleteByEmail($email);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }

    // Lấy OTP hiện tại
    public function getByEmail($email){
        try {
            $sql = "SELECT * FROM $this->table 
                    WHERE email = :email 
                    AND expires_at > NOW()
                    ORDER BY created_at DESC
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return null;
        }
    }

    // Xóa OTP cũ
    public function deleteByEmail($email){
        try {
            $sql = "DELETE FROM $this->table WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }

    // Xóa OTP hết hạn
    public function deleteExpired(){
        try {
            $sql = "DELETE FROM $this->table WHERE expires_at < NOW()";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }
}
?>