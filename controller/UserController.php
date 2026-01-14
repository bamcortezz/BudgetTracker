<?php

require_once __DIR__ . "/../config/Config.php"; // has session_start();
require_once __DIR__ . "/../model/User.php";
require_once __DIR__ . "/../utils/ResponseUtil.php";
require_once __DIR__ . "/../utils/CsrfUtil.php";

class UserController
{
   private $userModel;

   public function __construct()
   {
      $this->userModel = new User();
   }

   public function register($username, $email, $password, $csrfToken)
   {
      if (!CsrfUtil::verifyToken($csrfToken)) {
         ResponseUtil::error("Invalid CSRF token", 403);
      }

      if (empty($username) || empty($email) || empty($password)) {
         ResponseUtil::error("All fields are required");
      }

      if ($this->userModel->isEmailExists($email)) {
         ResponseUtil::error("Email already exists");
      }

      if ($this->userModel->register($username, $email, $password)) {
         ResponseUtil::success("User registered successfully", [], 201);
      }

      ResponseUtil::error("Registration failed");
   }

   public function login($email, $password, $csrfToken)
   {
      if (!CsrfUtil::verifyToken($csrfToken)) {
         ResponseUtil::error("Invalid CSRF token", 403);
      }

      if (empty($email) || empty($password)) {
         ResponseUtil::error("Email and password are required");
      }

      $user = $this->userModel->login($email, $password);

      if ($user) {
         $_SESSION['user_id'] = $user['id'];
         $_SESSION['username'] = $user['username'];

         ResponseUtil::success("Login successful", $user);
      }

      ResponseUtil::error("Invalid email or password", 401);
   }
}