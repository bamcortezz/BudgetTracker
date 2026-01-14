<?php
require_once __DIR__ . "/../config/Database.php";

class Transaction
{
   private $conn;

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

      $query = "INSERT INTO transactions (user_id, category_id, amount, type, description, date) 
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
                FROM transactions t
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE t.user_id = :user_id
                ORDER BY t.date DESC, t.created_at DESC
                LIMIT :limit OFFSET :offset";

      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
      $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
      $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   public function getTransactionById($id, $userId)
   {
      $query = "SELECT t.id, t.user_id, t.category_id, c.name as category_name,
                       t.amount, t.type, t.description, t.date, t.created_at, t.updated_at
                FROM transactions t
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE t.id = :id AND t.user_id = :user_id
                LIMIT 1";

      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":id", $id, PDO::PARAM_INT);
      $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetch(PDO::FETCH_ASSOC);
   }

   public function updateTransaction($id, $userId, $categoryId, $amount, $type, $description = null, $date = null)
   {
      $query = "UPDATE transactions 
                SET category_id = :category_id, amount = :amount, type = :type, 
                    description = :description, date = :date
                WHERE id = :id AND user_id = :user_id";

      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":id", $id, PDO::PARAM_INT);
      $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
      $stmt->bindParam(":category_id", $categoryId, PDO::PARAM_INT);
      $stmt->bindParam(":amount", $amount);
      $stmt->bindParam(":type", $type);
      $stmt->bindParam(":description", $description);
      $stmt->bindParam(":date", $date);

      return $stmt->execute();
   }

   public function deleteTransaction($id, $userId)
   {
      $query = "DELETE FROM transactions WHERE id = :id AND user_id = :user_id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":id", $id, PDO::PARAM_INT);
      $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);

      return $stmt->execute();
   }

   public function getTotalIncome($userId, $startDate = null, $endDate = null)
   {
      $query = "SELECT COALESCE(SUM(amount), 0) as total 
                FROM transactions 
                WHERE user_id = :user_id AND type = 'income'";

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
                FROM transactions 
                WHERE user_id = :user_id AND type = 'expense'";

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

   public function getBalance($userId, $startDate = null, $endDate = null)
   {
      $income = $this->getTotalIncome($userId, $startDate, $endDate);
      $expenses = $this->getTotalExpenses($userId, $startDate, $endDate);

      return $income - $expenses;
   }

   public function getUserStats($userId)
   {
      $query = "SELECT 
                    SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
                    SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
                  FROM transactions 
                  WHERE user_id = :user_id";

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

   public function getTransactionsByCategory($userId, $categoryId, $limit = 50)
   {
      $query = "SELECT t.id, t.user_id, t.category_id, c.name as category_name,
                       t.amount, t.type, t.description, t.date, t.created_at, t.updated_at
                FROM transactions t
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE t.user_id = :user_id AND t.category_id = :category_id
                ORDER BY t.date DESC, t.created_at DESC
                LIMIT :limit";

      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
      $stmt->bindParam(":category_id", $categoryId, PDO::PARAM_INT);
      $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   public function getTransactionsByType($userId, $type, $limit = 50)
   {
      $query = "SELECT t.id, t.user_id, t.category_id, c.name as category_name,
                       t.amount, t.type, t.description, t.date, t.created_at, t.updated_at
                FROM transactions t
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE t.user_id = :user_id AND t.type = :type
                ORDER BY t.date DESC, t.created_at DESC
                LIMIT :limit";

      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
      $stmt->bindParam(":type", $type);
      $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   public function getMonthlySummary($userId, $year = null, $month = null)
   {
      if (!$year)
         $year = date('Y');
      if (!$month)
         $month = date('m');

      $startDate = date('Y-m-01', strtotime("$year-$month-01"));
      $endDate = date('Y-m-t', strtotime("$year-$month-01"));

      $income = $this->getTotalIncome($userId, $startDate, $endDate);
      $expenses = $this->getTotalExpenses($userId, $startDate, $endDate);

      return [
         'income' => $income,
         'expenses' => $expenses,
         'balance' => $income - $expenses,
         'year' => $year,
         'month' => $month
      ];
   }

   public function getSpendingByCategory($userId, $startDate = null, $endDate = null)
   {
      $query = "SELECT c.id, c.name, 
                       SUM(CASE WHEN t.type = 'expense' THEN t.amount ELSE 0 END) as total_expenses,
                       SUM(CASE WHEN t.type = 'income' THEN t.amount ELSE 0 END) as total_income,
                       COUNT(t.id) as transaction_count
                FROM categories c
                LEFT JOIN transactions t ON c.id = t.category_id AND t.user_id = :user_id";

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
}