<?php

require_once __DIR__ . "/../config/Config.php";
require_once __DIR__ . "/../utils/ResponseUtil.php";

class AuthMiddleware
{
   public static function requireAuth()
   {
      if (!self::isAuthenticated()) {
         ResponseUtil::error("Authentication required. Please login to access this resource.", 401);
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

   public static function requireGuest()
   {
      if (self::isAuthenticated()) {
         ResponseUtil::error("You are already logged in.", 403);
      }

      return true;
   }

   public static function getCurrentUser()
   {
      if (!self::isAuthenticated()) {
         return null;
      }

      return [
         'id' => $_SESSION['user_id'],
         'username' => $_SESSION['username']
      ];
   }
}
