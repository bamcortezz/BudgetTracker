<?php
require_once __DIR__ . "/../config/Database.php";

class Transaction
{
   private $conn;
   private $table_name = "transactions";

   public function __construct()
   {
      $database = new Database();
      $this->conn = $database->getConnection();
   }

   public function createTransaction($userId, $categoryId, $amount, $type, $description = null, $date = null)
   {
      if (!$date) {
         $date = date('Y-m-d');
      }

      $query = "INSERT INTO " . $this->table_name . " (user_id, category_id, amount, type, description, date) 
                VALUES (:user_id, :category_id, :amount, :type, :description, :date)";
      $stmt = $this->conn->prepare($query);

      $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
      $stmt->bindParam(":category_id", $categoryId, PDO::PARAM_INT);
      $stmt->bindParam(":amount", $amount);
      $stmt->bindParam(":type", $type);
      $stmt->bindParam(":description", $description);
      $stmt->bindParam(":date", $date);

      if ($stmt->execute()) {
         return $this->conn->lastInsertId();
      }
      return false;
   }

   public function getTransactionsByUser($userId, $limit = 50, $offset = 0)
   {
      $query = "SELECT t.id, t.user_id, t.category_id, c.name as category_name, 
                       t.amount, t.type, t.description, t.date, t.created_at, t.updated_at
                FROM " . $this->table_name . " t
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE t.user_id = :user_id AND t.status = 'active'
                ORDER BY t.date DESC, t.created_at DESC
                LIMIT :limit OFFSET :offset";

      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
      $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
      $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   public function deleteTransaction($id, $userId)
   {
      $query = "UPDATE " . $this->table_name . " SET status = 'deleted' WHERE id = :id AND user_id = :user_id AND status = 'active'";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":id", $id, PDO::PARAM_INT);
      $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);

      return $stmt->execute();
   }

   public function getTotalIncome($userId, $startDate = null, $endDate = null)
   {
      $query = "SELECT COALESCE(SUM(amount), 0) as total 
                FROM " . $this->table_name . " 
                WHERE user_id = :user_id AND type = 'income' AND status = 'active'";

      if ($startDate && $endDate) {
         $query .= " AND date BETWEEN :start_date AND :end_date";
      }

      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);

      if ($startDate && $endDate) {
         $stmt->bindParam(":start_date", $startDate);
         $stmt->bindParam(":end_date", $endDate);
      }

      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      return (float) $result['total'];
   }

   public function getTotalExpenses($userId, $startDate = null, $endDate = null)
   {
      $query = "SELECT COALESCE(SUM(amount), 0) as total 
                FROM " . $this->table_name . " 
                WHERE user_id = :user_id AND type = 'expense' AND status = 'active'";

      if ($startDate && $endDate) {
         $query .= " AND date BETWEEN :start_date AND :end_date";
      }

      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);

      if ($startDate && $endDate) {
         $stmt->bindParam(":start_date", $startDate);
         $stmt->bindParam(":end_date", $endDate);
      }

      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      return (float) $result['total'];
   }

   public function getUserStats($userId)
   {
      $query = "SELECT 
                    SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
                    SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
                  FROM " . $this->table_name . " 
                  WHERE user_id = :user_id AND status = 'active'";

      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':user_id', $userId);
      $stmt->execute();

      $result = $stmt->fetch();

      $income = $result['total_income'] ?? 0;
      $expense = $result['total_expense'] ?? 0;
      $balance = $income - $expense;

      return [
         'balance' => $balance,
         'income' => $income,
         'expense' => $expense
      ];
   }

   public function getSpendingByCategory($userId, $startDate = null, $endDate = null)
   {
      $query = "SELECT c.id, c.name, 
                       SUM(CASE WHEN t.type = 'expense' THEN t.amount ELSE 0 END) as total_expenses,
                       SUM(CASE WHEN t.type = 'income' THEN t.amount ELSE 0 END) as total_income,
                       COUNT(t.id) as transaction_count
                FROM categories c
                LEFT JOIN " . $this->table_name . " t ON c.id = t.category_id AND t.user_id = :user_id AND t.status = 'active'";

      if ($startDate && $endDate) {
         $query .= " AND t.date BETWEEN :start_date AND :end_date";
      }

      $query .= " GROUP BY c.id, c.name ORDER BY total_expenses DESC";

      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);

      if ($startDate && $endDate) {
         $stmt->bindParam(":start_date", $startDate);
         $stmt->bindParam(":end_date", $endDate);
      }

      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   public function getPaginationData($userId, $page = 1, $perPage = 15)
   {
      $offset = ($page - 1) * $perPage;
      $transactions = $this->getTransactionsByUser($userId, $perPage, $offset);

      $totalQuery = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE user_id = :user_id AND status = 'active'";
      $stmt = $this->conn->prepare($totalQuery);
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