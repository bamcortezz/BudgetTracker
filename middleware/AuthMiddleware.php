<?php

require_once __DIR__ . "/../config/Config.php";

class AuthMiddleware
{
   public static function requireAuth()
   {
      if (!self::isAuthenticated()) {
         header("Location: index.php");
         exit();
      }

      return true;
   }

   public static function isAuthenticated()
   {
      return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
   }

   public static function getUserId()
   {
      return self::isAuthenticated() ? $_SESSION['user_id'] : null;
   }

   public static function getUsername()
   {
      return self::isAuthenticated() ? $_SESSION['username'] : null;
   }
}