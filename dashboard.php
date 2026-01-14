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
$recentTransactions = $transactionModel->getTransactionsByUser($userId, 10);
$categories = $categoryModel->getAllCategories();
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
                           <th class="pb-4 font-semibold text-center">Action</th>
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
                                 $isIncome = in_array($categoryName, ['Salary', 'Freelance', 'Investment']);
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
                              <td class="py-4 text-center">
                                 <button onclick="deleteTransaction(<?php echo $tx['id']; ?>)"
                                    class="text-gray-300 hover:text-rose-500 transition-colors">
                                    <i class="fa-solid fa-trash-can"></i>
                                 </button>
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

   <div id="transactionModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center px-4">
      <div class="absolute inset-0 bg-rose-900/20 backdrop-blur-sm" onclick="closeModal()"></div>
      <div
         class="bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl border border-rose-100 relative z-10 overflow-hidden animate-in fade-in zoom-in duration-300">
         <div class="bg-rose-500 p-6 text-white flex justify-between items-center">
            <h3 class="text-xl font-bold">New Transaction</h3>
            <button onclick="closeModal()" class="hover:rotate-90 transition-transform">
               <i class="fa-solid fa-xmark text-2xl"></i>
            </button>
         </div>

         <form id="transactionForm" class="p-8">
            <input type="hidden" name="add_transaction" value="1">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
               <div class="space-y-5">
                  <div>
                     <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Type</label>
                     <div class="grid grid-cols-2 gap-4">
                        <label class="relative cursor-pointer">
                           <input type="radio" name="type" value="expense" checked class="peer sr-only">
                           <div
                              class="text-center py-3 rounded-xl border border-rose-100 bg-rose-50 text-gray-600 peer-checked:bg-rose-500 peer-checked:text-white peer-checked:border-rose-500 transition-all font-bold">
                              Expense</div>
                        </label>
                        <label class="relative cursor-pointer">
                           <input type="radio" name="type" value="income" class="peer sr-only">
                           <div
                              class="text-center py-3 rounded-xl border border-rose-100 bg-rose-50 text-gray-600 peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 transition-all font-bold">
                              Income</div>
                        </label>
                     </div>
                  </div>
                  <div>
                     <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Amount</label>
                     <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₱</span>
                        <input type="number" step="0.01" name="amount" required
                           class="w-full pl-10 pr-5 py-4 rounded-2xl border border-rose-100 bg-rose-50/30 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:bg-white transition-all font-bold text-lg"
                           placeholder="0.00">
                     </div>
                  </div>
                  <div>
                     <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Date</label>
                     <input type="date" name="date" required value="<?php echo date('Y-m-d'); ?>"
                        class="w-full px-5 py-4 rounded-2xl border border-rose-100 bg-rose-50/30 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:bg-white transition-all">
                  </div>
               </div>
               <div class="space-y-5">
                  <div>
                     <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Category</label>
                     <select name="category_id" required
                        class="w-full px-5 py-4 rounded-2xl border border-rose-100 bg-rose-50/30 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:bg-white transition-all appearance-none">
                        <option value="" disabled selected>Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                           <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <div>
                     <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Description</label>
                     <textarea name="description" rows="4"
                        class="w-full px-5 py-4 rounded-2xl border border-rose-100 bg-rose-50/30 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:bg-white transition-all resize-none"
                        placeholder="What was this for?"></textarea>
                  </div>
               </div>
            </div>
            <div class="mt-8">
               <button type="submit" id="saveBtn"
                  class="w-full bg-rose-500 hover:bg-rose-600 text-white py-4 rounded-2xl font-bold text-lg shadow-lg shadow-rose-200 transition-all active:scale-95 flex justify-center items-center">
                  <span id="saveBtnText">Save Transaction</span>
               </button>
            </div>
         </form>
      </div>
   </div>

   <script src="assets/js/dashboard.js"></script>

   <?php require_once __DIR__ . "/partials/Foot.php"; ?>