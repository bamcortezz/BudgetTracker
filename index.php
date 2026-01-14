<?php require_once __DIR__ . "/partials/Head.php"; ?>

<body class="bg-rose-50 font-sans text-gray-800 min-h-screen flex flex-col">

   <nav
      class="bg-white border-b border-rose-100 px-6 py-4 flex justify-between items-center shadow-sm fixed w-full top-0 z-50">
      <div class="flex items-center gap-2">
         <div class="bg-rose-400 p-2 rounded-lg text-white">
            <i class="fa-solid fa-wallet"></i>
         </div>
         <span class="text-xl font-bold text-rose-600 tracking-tight">BudgetTracker</span>
      </div>
      <div class="space-x-4">
         <button class="text-rose-600 hover:text-rose-700 font-medium">Login</button>
         <button
            class="bg-rose-500 hover:bg-rose-600 text-white px-5 py-2 rounded-full transition-all shadow-md shadow-rose-200">
            Get Started
         </button>
      </div>
   </nav>

   <main class="flex-grow flex items-center justify-center pt-32 pb-20 px-6">
      <div class="container max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-16">

         <div class="lg:w-1/2 text-center lg:text-left">
            <h1 class="text-5xl lg:text-7xl font-extrabold text-gray-900 leading-tight mb-8">
               Manage your money <br> <span class="text-rose-500">beautifully.</span>
            </h1>
            <p class="text-xl text-gray-600 mb-10 max-w-lg mx-auto lg:mx-0 leading-relaxed">
               The simple, elegant way to track your expenses and grow your savings.
               Experience financial peace of mind with our light and intuitive tracker.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
               <button
                  class="bg-rose-500 hover:bg-rose-600 text-white px-10 py-5 rounded-2xl font-bold text-lg shadow-xl shadow-rose-200 transition-all hover:-translate-y-1">
                  Create Free Account
               </button>
            </div>
         </div>

         <div class="lg:w-1/2 w-full max-w-md lg:max-w-none">
            <div class="bg-white p-10 rounded-[3rem] shadow-2xl border border-rose-100 relative">
               <div class="absolute -top-6 -right-6 bg-rose-500 text-white p-5 rounded-full shadow-lg animate-bounce">
                  <i class="fa-solid fa-heart text-xl"></i>
               </div>

               <h2 class="text-2xl font-bold text-gray-800 mb-8">Quick Overview</h2>
               <div class="space-y-6">
                  <div class="bg-rose-50 p-5 rounded-2xl flex justify-between items-center border border-rose-100">
                     <div class="flex items-center gap-4">
                        <div class="bg-white p-3 rounded-xl shadow-sm text-rose-400">
                           <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                        <span class="font-semibold text-gray-700">Groceries</span>
                     </div>
                     <span class="text-rose-600 font-bold text-lg">-₱120.00</span>
                  </div>

                  <div
                     class="bg-emerald-50 p-5 rounded-2xl flex justify-between items-center border border-emerald-100">
                     <div class="flex items-center gap-4">
                        <div class="bg-white p-3 rounded-xl shadow-sm text-emerald-400">
                           <i class="fa-solid fa-money-bill-trend-up"></i>
                        </div>
                        <span class="font-semibold text-gray-700">Salary</span>
                     </div>
                     <span class="text-emerald-600 font-bold text-lg">+₱3,500.00</span>
                  </div>
               </div>

               <div class="mt-10 pt-8 border-t border-gray-100 text-center">
                  <p class="text-sm text-gray-400 italic font-medium tracking-wide">"Save more, bloom every day."</p>
               </div>
            </div>
         </div>

      </div>
   </main>

   <?php require_once __DIR__ . "/partials/Foot.php"; ?>