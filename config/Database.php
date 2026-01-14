<?php

class Database
{
   private $db_name = "budget_tracker";
   private $username = "root";
   private $password = "";
   private $socket = "/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock";
   public $conn;

   public function getConnection()
   {
      $this->conn = null;

      try {
         $dsn = "mysql:unix_socket={$this->socket};dbname={$this->db_name};charset=utf8mb4";

         $this->conn = new PDO($dsn, $this->username, $this->password);

         $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

      } catch (PDOException $exception) {
         error_log("Database Connection Error: " . $exception->getMessage());

         if (!class_exists('ResponseUtil')) {
            require_once __DIR__ . '/../utils/ResponseUtil.php';
         }

         $errorMessage = "Database connection failed.";
         if (!file_exists($this->socket)) {
            $errorMessage .= " MySQL socket not found. Please start MySQL in XAMPP.";
         }

         ResponseUtil::error($errorMessage, 500);
      }

      return $this->conn;
   }
}