<?php

require_once __DIR__ . "/../controller/UserController.php";

$UserController = new UserController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   if (isset($_POST['register'])) {
      $username = isset($_POST['username']) ? trim($_POST['username']) : null;
      $email = isset($_POST['email']) ? trim($_POST['email']) : null;
      $password = isset($_POST['password']) ? $_POST['password'] : null;
      $csrfToken = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null;

      $UserController->register($username, $email, $password, $csrfToken);
   }

   if (isset($_POST['login'])) {
      $email = isset($_POST['email']) ? trim($_POST['email']) : null;
      $password = isset($_POST['password']) ? $_POST['password'] : null;
      $csrfToken = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null;

      $UserController->login($email, $password, $csrfToken);
   }

   if (isset($_POST['logout'])) {
      $UserController->logout();
   }

} else {
   ResponseUtil::error("Method Not Allowed", 405);
}