<?php

require_once __DIR__ . "/../controller/TransactionController.php";
require_once __DIR__ . "/../utils/ResponseUtil.php";

$transactionController = new TransactionController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   if (isset($_POST['add_transaction'])) {
      $data = [
         'csrf_token' => $_POST['csrf_token'] ?? null,
         'type' => $_POST['type'] ?? null,
         'amount' => $_POST['amount'] ?? null,
         'category_id' => $_POST['category_id'] ?? null,
         'description' => $_POST['description'] ?? null,
         'date' => $_POST['date'] ?? null
      ];

      $transactionController->addTransaction($data);
   }

   if (isset($_POST['delete_transaction'])) {
      $data = [
         'csrf_token' => $_POST['csrf_token'] ?? null,
         'id' => $_POST['id'] ?? null
      ];

      $transactionController->deleteTransaction($data);
   }

} else {
   ResponseUtil::error("Method Not Allowed", 405);
}