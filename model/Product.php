<?php
class Product {
    private $conn;
    private $table = "products";

    public function __construct($db){
        $this->conn = $db;
    }

    // Lấy toàn bộ sản phẩm
    public function getAll(){
        try {
            $sql = "SELECT * FROM products";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return [];
        }
    }

    // Lấy sản phẩm theo ID
    public function getById($id_products){
        try {
            $sql = "SELECT * FROM $this->table WHERE id_products = :id_products";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_products', $id_products, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return null;
        }
    }

    public function getByCategorySlug($slug) {
        try {
            $sql = "SELECT p.* 
                    FROM products p
                    JOIN categories c ON p.id_categories = c.id_categories
                    WHERE c.slug = :slug";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return [];
        }
    }

    // Hàm tìm sản phẩm theo tên với phân trang
    public function searchByNamePaginated($q, $limit, $offset) {
        try {
            $q = "%".$q."%";
            $sql = "SELECT * FROM {$this->table} WHERE name LIKE :q LIMIT :limit OFFSET :offset";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':q', $q, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return [];
        }
    }

    public function searchByName($q) {
        try {
            $q = "%".$q."%";
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE name LIKE :q";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':q', $q, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return 0;
        }
    }

    // Lấy tất cả sản phẩm có phân trang
    public function getAllPaginated($limit, $offset){
        try {
            $sql = "SELECT * FROM $this->table ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return [];
        }
    }

    // Lấy sản phẩm theo category có phân trang
    public function getByCategoryPaginated($slug, $limit, $offset){
        try {
            $sql = "SELECT p.* 
                    FROM products p
                    JOIN categories c ON p.id_categories = c.id_categories
                    WHERE c.slug = :slug
                    ORDER BY p.created_at DESC
                    LIMIT :limit OFFSET :offset";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return [];
        }
    }

    // Lấy tổng số sản phẩm
    public function countAll(){
        try {
            $sql = "SELECT COUNT(*) as total FROM $this->table";
            $stmt = $this->conn->query($sql);
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return 0;
        }
    }

    // Lấy tổng sản phẩm theo category
    public function countByCategory($slug){
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM products p
                    JOIN categories c ON p.id_categories = c.id_categories
                    WHERE c.slug = :slug";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return 0;
        }
    }

    public function getSuggestions($limit = 4) {
        try {
            $sql = "SELECT id_products, name, image, price FROM products ORDER BY RAND() LIMIT :limit";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return [];
        }
    }

    // Thêm sản phẩm mới
    public function create($id_products, $name, $price, $description, $image, $stock, $id_categories, $id_featured){
        try {
            $sql = "INSERT INTO $this->table 
                    (id_products, name, price, description, image, stock, id_categories, created_at, id_featured)
                    VALUES (:id_products, :name, :price, :description, :image, :stock, :id_categories, NOW(), :id_featured)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_products', $id_products, PDO::PARAM_STR);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':price', $price, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':image', $image, PDO::PARAM_STR);
            $stmt->bindParam(':stock', $stock, PDO::PARAM_STR);
            $stmt->bindParam(':id_categories', $id_categories, PDO::PARAM_STR);
            $stmt->bindParam(':id_featured', $id_featured, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }

    public function getFeatured() {
        try {
            $sql = "SELECT * FROM products WHERE id_featured = 1 ORDER BY created_at DESC";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return [];
        }
    }

    // Cập nhật sản phẩm
    public function update($id_products, $name, $price, $description, $image, $stock, $id_categories, $id_featured){
        try {
            $sql = "UPDATE $this->table 
                    SET name = :name, price = :price, description = :description, image = :image, stock = :stock, id_categories = :id_categories, id_featured = :id_featured
                    WHERE id_products = :id_products";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':image', $image, PDO::PARAM_STR);
            $stmt->bindParam(':stock', $stock, PDO::PARAM_STR);
            $stmt->bindParam(':id_categories', $id_categories, PDO::PARAM_STR);
            $stmt->bindParam(':id_featured', $id_featured, PDO::PARAM_INT);
            $stmt->bindParam(':id_products', $id_products, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }

    // Xóa sản phẩm
    public function delete($id_products){
        try {
            $sql = "DELETE FROM $this->table WHERE id_products = :id_products";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_products', $id_products, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
