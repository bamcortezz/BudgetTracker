<?php

class Database
{
   private $db_name = "budget_tracker";
   private $username = "root";
   private $password = "";
   private $host = "localhost";
   private $socket = "/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock";
   public $conn;

   public function getConnection()
   {
      $this->conn = null;

      try {
         // Check if socket exists (local XAMPP environment)
         if (file_exists($this->socket)) {
            $dsn = "mysql:unix_socket={$this->socket};dbname={$this->db_name};charset=utf8mb4";
         } else {
            // Use standard host:port connection (for production/shared hosting)
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
         }

         $this->conn = new PDO($dsn, $this->username, $this->password);

         $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

      } catch (PDOException $exception) {
         error_log("Database Connection Error: " . $exception->getMessage());
      }

      return $this->conn;
   }
}