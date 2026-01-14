<?php

require_once __DIR__ . "/../config/Config.php";
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

   public function register($username, $email, $password, $confirmPassword, $csrfToken)
   {
      if (!CsrfUtil::verifyToken($csrfToken)) {
         ResponseUtil::error("Invalid CSRF token", 403);
      }

      if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
         ResponseUtil::error("All fields are required");
      }

      if ($password !== $confirmPassword) {
         ResponseUtil::error("Passwords do not match");
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

      $user = $this->userModel->getUserByEmail($email);

      if (!$user) {
         ResponseUtil::error("Email not found", 404);
      }

      if (!password_verify($password, $user['password'])) {
         ResponseUtil::error("Incorrect password", 401);
      }

      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];

      unset($user['password']);
      ResponseUtil::success("Login successful", $user);
   }

   public function logout()
   {
      $_SESSION = array();

      if (ini_get("session.use_cookies")) {
         $params = session_get_cookie_params();
         setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
         );
      }

      session_destroy();
      ResponseUtil::success("Logged out successfully");
   }
}