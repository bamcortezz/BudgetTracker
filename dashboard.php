<?php
require_once __DIR__ . "/config/Config.php";
require_once __DIR__ . "/middleware/AuthMiddleware.php";
require_once __DIR__ . "/model/Transaction.php";
require_once __DIR__ . "/model/Category.php";
require_once __DIR__ . "/utils/CsrfUtil.php";
require_once __DIR__ . "/partials/Head.php";

AuthMiddleware::requireAuth();

$userId = AuthMiddleware::getUserId();
$username = AuthMiddleware::getUsername();

$transactionModel = new Transaction();
$categoryModel = new Category();

$stats = $transactionModel->getUserStats($userId);
$recentTransactions = $transactionModel->getTransactionsByUser($userId, 4);
$categories = $categoryModel->getAllCategories();
$categorySpending = $transactionModel->getSpendingByCategory($userId);
$csrfToken = CsrfUtil::generateToken();
?>

<body class="bg-rose-50 font-sans text-gray-800 min-h-screen flex flex-col">

   <?php require_once __DIR__ . "/partials/Navbar.php"; ?>

   <main class="flex-grow pt-28 pb-12 px-6">
      <div class="container max-w-6xl mx-auto">
         <header class="mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900">Financial Dashboard</h1>
            <p class="text-gray-500">Track your blooms and expenses today, <?php echo htmlspecialchars($username); ?>.
            </p>
         </header>

         <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-rose-100">
               <p class="text-rose-400 font-semibold mb-1">Total Balance</p>
               <h3 class="text-3xl font-bold">₱<?php echo number_format($stats['balance'], 2); ?></h3>
            </div>

            <div class="bg-emerald-50 p-8 rounded-[2rem] shadow-sm border border-emerald-100">
               <p class="text-emerald-600 font-semibold mb-1">Total Income</p>
               <h3 class="text-3xl font-bold text-emerald-700">₱<?php echo number_format($stats['income'], 2); ?></h3>
            </div>

            <div class="bg-rose-500 p-8 rounded-[2rem] shadow-lg shadow-rose-200 border border-rose-400">
               <p class="text-rose-100 font-semibold mb-1">Total Expenses</p>
               <h3 class="text-3xl font-bold text-white">₱<?php echo number_format($stats['expense'], 2); ?></h3>
            </div>
         </div>

         <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-rose-100 p-8">
               <h2 class="text-xl font-bold text-gray-800 mb-6">Expense by Category</h2>
               <div class="relative h-[300px]">
                  <?php if (empty($recentTransactions)): ?>
                     <div class="flex flex-col items-center justify-center h-full">
                        <div class="text-rose-200 mb-4">
                           <i class="fa-solid fa-chart-pie text-5xl"></i>
                        </div>
                        <p class="text-gray-400 italic">No transactions found yet. Start blooming!</p>
                     </div>
                  <?php else: ?>
                     <canvas id="expenseChart"></canvas>
                  <?php endif; ?>
               </div>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-rose-100 p-8">
               <h2 class="text-xl font-bold text-gray-800 mb-6">Income vs Expenses</h2>
               <div class="relative h-[300px]">
                  <?php if (empty($recentTransactions)): ?>
                     <div class="flex flex-col items-center justify-center h-full">
                        <div class="text-rose-200 mb-4">
                           <i class="fa-solid fa-chart-bar text-5xl"></i>
                        </div>
                        <p class="text-gray-400 italic">No transactions found yet. Start blooming!</p>
                     </div>
                  <?php else: ?>
                     <canvas id="incomeExpenseChart"></canvas>
                  <?php endif; ?>
               </div>
            </div>
         </div>

         <div class="bg-white rounded-[2.5rem] shadow-sm border border-rose-100 p-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
               <h2 class="text-xl font-bold text-gray-800">Recent Transactions</h2>
               <a href="transactions.php"
                  class="inline-flex items-center text-rose-500 hover:text-rose-600 font-semibold transition-colors group">
                  View All Transactions
                  <i class="fa-solid fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
               </a>
            </div>

            <?php if (empty($recentTransactions)): ?>
               <div class="text-center py-12">
                  <div class="text-rose-200 mb-4">
                     <i class="fa-solid fa-leaf text-5xl"></i>
                  </div>
                  <p class="text-gray-400 italic">No transactions found yet. Start blooming!</p>
               </div>
            <?php else: ?>
               <div class="overflow-x-auto">
                  <table class="w-full text-left">
                     <thead>
                        <tr class="text-rose-400 text-sm uppercase tracking-wider border-b border-rose-50">
                           <th class="pb-4 font-semibold">Date</th>
                           <th class="pb-4 font-semibold">Category</th>
                           <th class="pb-4 font-semibold">Description</th>
                           <th class="pb-4 font-semibold text-right">Amount</th>
                        </tr>
                     </thead>
                     <tbody class="divide-y divide-rose-50">
                        <?php foreach ($recentTransactions as $tx): ?>
                           <tr class="hover:bg-rose-50/30 transition-colors">
                              <td class="py-4 text-gray-600 text-sm">
                                 <?php echo date('M d, Y', strtotime($tx['date'])); ?>
                              </td>
                              <td class="py-4">
                                 <?php
                                 $categoryName = $tx['category_name'];
                                 $isIncome = in_array($categoryName, ['Salary', 'Freelance', 'Investment', 'Allowance']);
                                 $badgeClasses = $isIncome
                                    ? 'bg-emerald-100 text-emerald-600'
                                    : 'bg-rose-100 text-rose-600';
                                 ?>
                                 <span
                                    class="<?php echo $badgeClasses; ?> px-3 py-1 rounded-full text-xs font-bold uppercase">
                                    <?php echo htmlspecialchars($categoryName); ?>
                                 </span>
                              </td>
                              <td class="py-4 text-gray-700 text-sm">
                                 <?php echo htmlspecialchars($tx['description'] ?: 'No description'); ?>
                              </td>
                              <td
                                 class="py-4 text-right font-bold <?php echo $tx['type'] === 'income' ? 'text-emerald-600' : 'text-rose-500'; ?>">
                                 <?php echo ($tx['type'] === 'income' ? '+' : '-') . '₱' . number_format($tx['amount'], 2); ?>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                     </tbody>
                  </table>
               </div>

            <?php endif; ?>
         </div>
      </div>
   </main>

   <script>
      const categorySpending = <?php echo json_encode($categorySpending); ?>;

      const expenseCategories = categorySpending
         .filter(cat => parseFloat(cat.total_expenses) > 0)
         .sort((a, b) => parseFloat(b.total_expenses) - parseFloat(a.total_expenses))
         .slice(0, 8);

      const expenseLabels = expenseCategories.map(cat => cat.name);
      const expenseData = expenseCategories.map(cat => parseFloat(cat.total_expenses));

      const expenseColors = [
         '#e11d48', '#f43f5e', '#fb7185', '#fda4af',
         '#fecdd3', '#fce7f3', '#fdf2f8', '#fff1f2'
      ];

      if (document.getElementById('expenseChart')) {
         new Chart(document.getElementById('expenseChart'), {
            type: 'pie',
            data: {
               labels: expenseLabels,
               datasets: [{
                  data: expenseData,
                  backgroundColor: expenseColors,
                  borderWidth: 2,
                  borderColor: '#fff'
               }]
            },
            options: {
               responsive: true,
               maintainAspectRatio: false,
               plugins: {
                  legend: {
                     display: false
                  },
                  tooltip: {
                     backgroundColor: '#fff',
                     titleColor: '#1f2937',
                     bodyColor: '#6b7280',
                     borderColor: '#f43f5e',
                     borderWidth: 1,
                     padding: 12,
                     displayColors: true,
                     callbacks: {
                        label: function (context) {
                           return context.label + ': ₱' + context.parsed.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                     }
                  }
               }
            }
         });
      }

      if (document.getElementById('incomeExpenseChart')) {
         new Chart(document.getElementById('incomeExpenseChart'), {
            type: 'bar',
            data: {
               labels: ['Income', 'Expenses'],
               datasets: [{
                  label: 'Amount (₱)',
                  data: [<?php echo $stats['income']; ?>, <?php echo $stats['expense']; ?>],
                  backgroundColor: ['#10b981', '#f43f5e'],
                  borderRadius: 12,
                  borderWidth: 0
               }]
            },
            options: {
               responsive: true,
               maintainAspectRatio: false,
               plugins: {
                  legend: { display: false },
                  tooltip: {
                     backgroundColor: '#fff',
                     titleColor: '#1f2937',
                     bodyColor: '#6b7280',
                     borderColor: '#e5e7eb',
                     borderWidth: 1,
                     padding: 12,
                     callbacks: {
                        label: function (context) {
                           return '₱' + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                     }
                  }
               },
               scales: {
                  y: {
                     beginAtZero: true,
                     ticks: {
                        callback: function (value) {
                           return '₱' + value.toLocaleString('en-US', { minimumFractionDigits: 0 });
                        },
                        font: { size: 11, weight: '600' },
                        color: '#6b7280'
                     },
                     grid: {
                        color: '#f3f4f6',
                        drawBorder: false
                     }
                  },
                  x: {
                     ticks: {
                        font: { size: 13, weight: '700' },
                        color: '#374151'
                     },
                     grid: { display: false }
                  }
               }
            }
         });
      }
   </script>

   <script>
      if (document.getElementById("logoutForm")) {
         document.getElementById("logoutForm").addEventListener("submit", async function (e) {
            e.preventDefault();

            const result = await Swal.fire({
               title: 'Logout?',
               text: "Are you sure you want to logout? 🌸",
               icon: 'question',
               showCancelButton: true,
               confirmButtonColor: '#f43f5e',
               cancelButtonColor: '#6b7280',
               confirmButtonText: 'Yes, logout',
               cancelButtonText: 'Cancel'
            });

            if (!result.isConfirmed) return;

            const logoutBtn = this.querySelector("button");
            logoutBtn.disabled = true;
            logoutBtn.innerText = "Logging out...";

            try {
               const response = await fetch("routes/UserRoutes.php", {
                  method: "POST",
                  body: new FormData(this),
               });
               const result = await response.json();
               if (result.status === "success") {
                  window.location.href = "/";
               } else {
                  showToast(result.message || "Logout failed.", "error");
                  logoutBtn.disabled = false;
                  logoutBtn.innerText = "Logout";
               }
            } catch (error) {
               showToast("Logout failed.", "error");
               logoutBtn.disabled = false;
               logoutBtn.innerText = "Logout";
            }
         });
      }
   </script>

   <script src="assets/js/dashboard.js"></script>

   <?php require_once __DIR__ . "/partials/Foot.php"; ?>