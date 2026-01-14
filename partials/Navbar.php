<?php
$isAuthenticated = AuthMiddleware::isAuthenticated();
$currentPage = basename($_SERVER['PHP_SELF']);

$username = $isAuthenticated ? AuthMiddleware::getUsername() : null;
?>

<nav
   class="bg-white border-b border-rose-100 px-6 py-4 flex justify-between items-center shadow-sm fixed w-full top-0 z-50">
   <div class="flex items-center gap-2">
      <a href="<?php echo ($isAuthenticated && $currentPage === 'dashboard.php') ? 'dashboard.php' : 'index.php'; ?>"
         class="flex items-center gap-2">
         <div class="bg-rose-400 p-2 rounded-lg text-white">
            <i class="fa-solid fa-wallet text-sm"></i>
         </div>
         <span class="text-xl font-bold text-rose-600 tracking-tight">BudgetTracker</span>
      </a>
   </div>

   <?php if ($currentPage === 'dashboard.php'): ?>

      <div class="flex items-center gap-4">
         <span class="text-gray-600 hidden md:block">Hi, <span class="font-bold text-rose-500">
               <?php echo htmlspecialchars($username); ?>
            </span></span>
         <form id="logoutForm">
            <input type="hidden" name="logout" value="1">
            <button type="submit"
               class="bg-rose-100 text-rose-600 px-4 py-2 rounded-xl font-semibold hover:bg-rose-200 transition-all">
               Logout
            </button>
         </form>
      </div>
   <?php elseif ($currentPage === 'index.php'): ?>

      <div class="space-x-2 md:space-x-4">
         <?php if ($isAuthenticated): ?>
            <a href="dashboard.php"
               class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-2 rounded-full font-bold shadow-md shadow-rose-200 transition-all inline-block">
               Dashboard
            </a>
         <?php else: ?>
            <a href="login.php"
               class="text-rose-600 hover:text-rose-700 font-semibold px-4 py-2 rounded-lg transition-colors">
               Login
            </a>
            <a href="register.php"
               class="hidden sm:inline-block bg-rose-500 hove  r:bg-rose-600 text-white px-5 py-2 rounded-full font-bold shadow-md shadow-rose-200 transition-all">
               Get Started
            </a>
         <?php endif; ?>
      </div>
   <?php endif; ?>
</nav>