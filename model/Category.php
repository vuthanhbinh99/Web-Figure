<?php
class Category {
    private $conn;
    private $table = "categories";

    public function __construct($db){
        $this->conn = $db;
    }

    public function getAll(){
        try {
            $sql = "SELECT * FROM " . $this->table . " ORDER BY name DESC";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id_categories){
        try {
            $sql = "SELECT * FROM " . $this->table . " WHERE id_categories = :id_categories";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_categories', $id_categories, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return null;
        }
    }

    public function getBySlug($slug) {
        try {
            $sql = "SELECT * FROM " . $this->table . " WHERE slug = :slug LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return null;
        }
    }

    public function create($id_categories, $name, $slug){
        try {
            $sql = "INSERT INTO " . $this->table . "(id_categories, name, slug) VALUES (:id_categories, :name, :slug)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_categories', $id_categories, PDO::PARAM_STR);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }

    public function update($id_categories, $name, $slug){
        try {
            $sql = "UPDATE " . $this->table . " SET name = :name, slug = :slug WHERE id_categories = :id_categories";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->bindParam(':id_categories', $id_categories, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id_categories){
        try {
            $sql = "DELETE FROM " . $this->table . " WHERE id_categories = :id_categories";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_categories', $id_categories, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
