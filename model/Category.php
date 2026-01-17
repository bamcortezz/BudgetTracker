<?php

require_once __DIR__ . "/../config/Database.php";

class Category
{
   private $conn;
   private $table_name = "categories";

   public function __construct()
   {
      $database = new Database();
      $this->conn = $database->getConnection();
   }

   public function getAllCategories()
   {
      $query = "SELECT id, name, created_at FROM " . $this->table_name . " ORDER BY name ASC";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   public function categoryExists($id)
   {
      $query = "SELECT id FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":id", $id, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->rowCount() > 0;
   }
}