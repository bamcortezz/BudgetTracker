<?php
require_once __DIR__ . "/../model/Transaction.php";
require_once __DIR__ . "/../model/Category.php";
require_once __DIR__ . "/../utils/CsrfUtil.php";
require_once __DIR__ . "/../utils/ResponseUtil.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

class TransactionController
{
   private $transactionModel;
   private $categoryModel;

   public function __construct()
   {
      $this->transactionModel = new Transaction();
      $this->categoryModel = new Category();
   }

   public function addTransaction($data)
   {
      if (!CsrfUtil::verifyToken($data['csrf_token'] ?? '')) {
         ResponseUtil::error("Invalid CSRF token.", 403);
      }

      AuthMiddleware::requireAuth();
      $userId = AuthMiddleware::getUserId();

      $amount = filter_var($data['amount'], FILTER_VALIDATE_FLOAT);
      $type = $data['type'] ?? '';
      $categoryId = filter_var($data['category_id'], FILTER_VALIDATE_INT);
      $description = htmlspecialchars(trim($data['description'] ?? ''));
      $date = !empty($data['date']) ? $data['date'] : date('Y-m-d');

      if (!$amount || $amount <= 0) {
         ResponseUtil::error("Please enter a valid amount.");
      }

      if (!in_array($type, ['income', 'expense'])) {
         ResponseUtil::error("Invalid transaction type.");
      }

      if (!$categoryId || !$this->categoryModel->categoryExists($categoryId)) {
         ResponseUtil::error("Please select a valid category.");
      }

      $result = $this->transactionModel->createTransaction(
         $userId,
         $categoryId,
         $amount,
         $type,
         $description,
         $date
      );

      if ($result) {
         ResponseUtil::success("Transaction bloomed successfully!", ["id" => $result]);
      } else {
         ResponseUtil::error("Failed to save transaction.", 500);
      }
   }

   public function deleteTransaction($data)
   {
      if (!CsrfUtil::verifyToken($data['csrf_token'] ?? '')) {
         ResponseUtil::error("Security check failed.", 403);
      }

      AuthMiddleware::requireAuth();
      $userId = AuthMiddleware::getUserId();
      $transactionId = filter_var($data['id'], FILTER_VALIDATE_INT);

      if (!$transactionId) {
         ResponseUtil::error("Invalid transaction ID.");
      }

      $deleted = $this->transactionModel->deleteTransaction($transactionId, $userId);

      if ($deleted) {
         ResponseUtil::success("Transaction removed.");
      } else {
         ResponseUtil::error("Failed to delete transaction.", 500);
      }
   }
}