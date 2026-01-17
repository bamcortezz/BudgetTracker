<?php

class ResponseUtil
{
   public static function sendJson($data, $statusCode = 200)
   {
      header('Content-Type: application/json; charset=utf-8');
      http_response_code($statusCode);

      echo json_encode($data);
      exit;
   }

   public static function success($message = "Success", $data = [], $statusCode = 200)
   {
      self::sendJson([
         "status" => "success",
         "message" => $message,
         "data" => $data
      ], $statusCode);
   }

   public static function error($message = "An error occurred", $statusCode = 400, $data = null)
   {
      self::sendJson([
         "status" => "error",
         "message" => $message,
         "data" => $data
      ], $statusCode);
   }
}