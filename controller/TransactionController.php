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

   public function getPaginationData($userId, $page = 1, $perPage = 15)
   {
      require_once __DIR__ . "/../config/Database.php";

      $offset = ($page - 1) * $perPage;
      $transactions = $this->transactionModel->getTransactionsByUser($userId, $perPage, $offset);

      $totalQuery = "SELECT COUNT(*) as total FROM transactions WHERE user_id = :user_id AND status = 'active'";
      $database = new Database();
      $conn = $database->getConnection();
      $stmt = $conn->prepare($totalQuery);
      $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
      $stmt->execute();
      $totalRecords = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
      $totalPages = ceil($totalRecords / $perPage);

      // Generate pagination links (always show, even for 1 page)
      $paginationLinks = [];
      $startPage = max(1, $page - 2);
      $endPage = min($totalPages, $page + 2);

      // First page and ellipsis
      if ($startPage > 1) {
         $paginationLinks[] = [
            'type' => 'link',
            'page' => 1,
            'label' => '1',
            'active' => false
         ];
         if ($startPage > 2) {
            $paginationLinks[] = ['type' => 'ellipsis'];
         }
      }

      // Page range
      for ($i = $startPage; $i <= $endPage; $i++) {
         $paginationLinks[] = [
            'type' => 'link',
            'page' => $i,
            'label' => (string) $i,
            'active' => $i === $page
         ];
      }

      // Last page and ellipsis
      if ($endPage < $totalPages) {
         if ($endPage < $totalPages - 1) {
            $paginationLinks[] = ['type' => 'ellipsis'];
         }
         $paginationLinks[] = [
            'type' => 'link',
            'page' => $totalPages,
            'label' => (string) $totalPages,
            'active' => false
         ];
      }

      return [
         'transactions' => $transactions,
         'totalRecords' => $totalRecords,
         'totalPages' => $totalPages,
         'currentPage' => $page,
         'paginationLinks' => $paginationLinks
      ];
   }
}