<?php
require_once __DIR__ . "/config/Config.php";
require_once __DIR__ . "/middleware/AuthMiddleware.php";
require_once __DIR__ . "/model/Transaction.php";
require_once __DIR__ . "/partials/Head.php";

AuthMiddleware::requireAuth();

$userId = AuthMiddleware::getUserId();
$username = AuthMiddleware::getUsername();

$transactionModel = new Transaction();
$stats = $transactionModel->getUserStats($userId);

$recentTransactions = $transactionModel->getTransactionsByUser($userId, 10);
?>

<body class="bg-rose-50 font-sans text-gray-800 min-h-screen flex flex-col">

   <?php require_once __DIR__ . "/partials/Navbar.php"; ?>

   <main class="flex-grow pt-28 pb-12 px-6">
      <div class="container max-w-6xl mx-auto">
         <header class="mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900">Financial Dashboard</h1>
            <p class="text-gray-500">Track your blooms and expenses today.</p>
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

         <div class="bg-white rounded-[2.5rem] shadow-sm border border-rose-100 p-8">
            <div class="flex justify-between items-center mb-8">
               <h2 class="text-xl font-bold text-gray-800">Recent Transactions</h2>
               <button id="openModalBtn"
                  class="bg-rose-500 text-white px-6 py-2 rounded-full font-bold shadow-md hover:bg-rose-600 transition-all">
                  + Add New
               </button>
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
                                 <span class="bg-rose-100 text-rose-600 px-3 py-1 rounded-full text-xs font-bold uppercase">
                                    <?php echo htmlspecialchars($tx['category_name']); ?>
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
      if (document.getElementById('logoutForm')) {
         document.getElementById('logoutForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const confirmed = confirm("Are you sure you want to logout? 🌸");

            if (!confirmed) {
               return;
            }

            const formData = new FormData(this);
            const logoutBtn = this.querySelector('button');

            logoutBtn.disabled = true;
            logoutBtn.innerText = 'Logging out...';

            try {
               const response = await fetch('routes/UserRoutes.php', {
                  method: 'POST',
                  body: formData
               });

               const result = await response.json();

               if (result.status === 'success') {
                  window.location.href = 'index.php';
               }
            } catch (error) {
               console.error('Logout failed:', error);
               alert('An error occurred during logout.');
               logoutBtn.disabled = false;
               logoutBtn.innerText = 'Logout';
            }
         });
      }
   </script>

   <?php require_once __DIR__ . "/partials/Foot.php"; ?>