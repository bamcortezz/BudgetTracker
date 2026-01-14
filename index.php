<?php
require_once __DIR__ . "/config/Config.php";
require_once __DIR__ . "/middleware/AuthMiddleware.php";
require_once __DIR__ . "/partials/Head.php";
?>

<body class="bg-rose-50 font-sans text-gray-800 min-h-screen flex flex-col">

   <?php require_once __DIR__ . "/partials/Navbar.php"; ?>

   <main class="flex-grow flex items-center justify-center pt-28 pb-16 px-6">
      <div class="container max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-12 lg:gap-16">

         <div class="w-full lg:w-1/2 text-center lg:text-left">
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 leading-tight mb-6">
               Manage your money <br class="hidden md:block">
               <span class="text-rose-500">beautifully.</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-600 mb-8 max-w-md mx-auto lg:mx-0 leading-relaxed">
               The simple, elegant way to track your expenses and grow your savings.
               Experience financial peace of mind.
            </p>
            <div class="flex justify-center lg:justify-start mb-4">
               <?php if (AuthMiddleware::isAuthenticated()): ?>
                  <a href="dashboard.php"
                     class="w-full sm:w-auto bg-rose-500 hover:bg-rose-600 text-white px-10 py-4 rounded-2xl font-bold text-lg shadow-xl shadow-rose-200 transition-all active:scale-95 lg:hover:-translate-y-1 text-center">
                     Go to Dashboard
                  </a>
               <?php else: ?>
                  <a href="register.php"
                     class="w-full sm:w-auto bg-rose-500 hover:bg-rose-600 text-white px-10 py-4 rounded-2xl font-bold text-lg shadow-xl shadow-rose-200 transition-all active:scale-95 lg:hover:-translate-y-1 text-center">
                     Create Free Account
                  </a>
               <?php endif; ?>
            </div>
         </div>

         <div class="w-full lg:w-1/2 flex justify-center">
            <div
               class="bg-white p-8 md:p-10 rounded-[2.5rem] md:rounded-[3rem] shadow-2xl border border-rose-100 relative w-full max-w-sm lg:max-w-md">
               <div
                  class="absolute -top-4 -right-2 md:-top-6 md:-right-6 bg-rose-500 text-white p-4 md:p-5 rounded-full shadow-lg animate-bounce">
                  <i class="fa-solid fa-heart text-lg md:text-xl"></i>
               </div>

               <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-6 md:mb-8 text-center lg:text-left">Quick
                  Overview</h2>

               <div class="space-y-4 md:space-y-6">
                  <div
                     class="bg-rose-50 p-4 md:p-5 rounded-2xl flex justify-between items-center border border-rose-100">
                     <div class="flex items-center gap-3 md:gap-4">
                        <div class="bg-white p-2 md:p-3 rounded-xl shadow-sm text-rose-400">
                           <i class="fa-solid fa-cart-shopping text-sm md:text-base"></i>
                        </div>
                        <span class="font-semibold text-gray-700 text-sm md:text-base">Groceries</span>
                     </div>
                     <span class="text-rose-600 font-bold text-base md:text-lg">-₱120.00</span>
                  </div>

                  <div
                     class="bg-emerald-50 p-4 md:p-5 rounded-2xl flex justify-between items-center border border-emerald-100">
                     <div class="flex items-center gap-3 md:gap-4">
                        <div class="bg-white p-2 md:p-3 rounded-xl shadow-sm text-emerald-400">
                           <i class="fa-solid fa-money-bill-trend-up text-sm md:text-base"></i>
                        </div>
                        <span class="font-semibold text-gray-700 text-sm md:text-base">Salary</span>
                     </div>
                     <span class="text-emerald-600 font-bold text-base md:text-lg">+₱3,500.00</span>
                  </div>
               </div>

               <div class="mt-8 md:mt-10 pt-6 md:pt-8 border-t border-gray-100 text-center">
                  <p class="text-xs md:text-sm text-gray-400 italic font-medium tracking-wide">"Save more, bloom every
                     day."</p>
               </div>
            </div>
         </div>

      </div>
   </main>

   <?php require_once __DIR__ . "/partials/Foot.php"; ?>