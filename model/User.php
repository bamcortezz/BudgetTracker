<?php

require_once __DIR__ . "/../config/Database.php";

class User
{
   private $conn;

   public function __construct()
   {
      $database = new Database();
      $this->conn = $database->getConnection();
   }

   public function register($username, $email, $password)
   {
      $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
      $stmt = $this->conn->prepare($query);

      $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

      $stmt->bindParam(":username", $username);
      $stmt->bindParam(":email", $email);
      $stmt->bindParam(":password", $hashedPassword);

      if ($stmt->execute()) {
         return true;
      }
      return false;
   }

   public function login($email, $password)
   {
      $query = "SELECT id, username, email, password FROM users WHERE email = :email LIMIT 1";

      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":email", $email);
      $stmt->execute();

      $user = $stmt->fetch();

      if ($user && password_verify($password, $user['password'])) {
         unset($user['password']);
         return $user;
      }
      return false;
   }

   public function isEmailExists($email)
   {
      $query = "SELECT id FROM users WHERE email = :email LIMIT 1";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":email", $email);
      $stmt->execute();

      return $stmt->rowCount() > 0;
   }
}