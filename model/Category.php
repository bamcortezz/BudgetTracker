<?php

require_once __DIR__ . "/../config/Database.php";

class Category
{
   private $conn;

   public function __construct()
   {
      $database = new Database();
      $this->conn = $database->getConnection();
   }

   public function getAllCategories()
   {
      $query = "SELECT id, name, created_at FROM categories ORDER BY name ASC";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   public function getCategoryById($id)
   {
      $query = "SELECT id, name, created_at FROM categories WHERE id = :id LIMIT 1";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":id", $id, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetch(PDO::FETCH_ASSOC);
   }

   public function getCategoryByName($name)
   {
      $query = "SELECT id, name, created_at FROM categories WHERE name = :name LIMIT 1";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":name", $name);
      $stmt->execute();

      return $stmt->fetch(PDO::FETCH_ASSOC);
   }

   public function createCategory($name)
   {
      if ($this->getCategoryByName($name)) {
         return false;
      }

      $query = "INSERT INTO categories (name) VALUES (:name)";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":name", $name);

      if ($stmt->execute()) {
         return $this->conn->lastInsertId();
      }
      return false;
   }

   public function updateCategory($id, $name)
   {
      $query = "UPDATE categories SET name = :name WHERE id = :id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":name", $name);
      $stmt->bindParam(":id", $id, PDO::PARAM_INT);

      return $stmt->execute();
   }

   public function deleteCategory($id)
   {
      $query = "DELETE FROM categories WHERE id = :id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":id", $id, PDO::PARAM_INT);

      return $stmt->execute();
   }

   public function categoryExists($id)
   {
      $query = "SELECT id FROM categories WHERE id = :id LIMIT 1";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":id", $id, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->rowCount() > 0;
   }
}
