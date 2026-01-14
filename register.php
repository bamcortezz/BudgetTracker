<?php
require_once __DIR__ . "/config/Config.php";
require_once __DIR__ . "/middleware/AuthMiddleware.php";
require_once __DIR__ . "/utils/CsrfUtil.php";
require_once __DIR__ . "/partials/Head.php";
?>

<body class="bg-rose-50 font-sans text-gray-800 min-h-screen flex flex-col">

   <?php require_once __DIR__ . "/partials/Navbar.php"; ?>

   <main class="flex-grow flex items-center justify-center pt-24 pb-12 px-6">
      <div class="w-full max-w-md">
         <div class="bg-white p-8 md:p-10 rounded-[2.5rem] shadow-2xl border border-rose-100 relative">

            <div class="text-center mb-8">
               <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Grow With Us</h1>
               <p class="text-gray-500 italic">"Start your journey to financial peace."</p>
            </div>

            <form id="registerForm" class="space-y-5">
               <input type="hidden" name="register" value="1">
               <input type="hidden" name="csrf_token" value="<?php echo CsrfUtil::generateToken(); ?>">

               <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Username</label>
                  <input type="text" name="username" required
                     class="w-full px-5 py-4 rounded-2xl border border-rose-100 bg-rose-50/30 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:bg-white transition-all"
                     placeholder="username">
               </div>

               <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Email Address</label>
                  <input type="email" name="email" required
                     class="w-full px-5 py-4 rounded-2xl border border-rose-100 bg-rose-50/30 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:bg-white transition-all"
                     placeholder="email">
               </div>

               <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Password</label>
                  <div class="relative">
                     <input type="password" id="password" name="password" required
                        class="w-full px-5 py-4 pr-12 rounded-2xl border border-rose-100 bg-rose-50/30 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:bg-white transition-all"
                        placeholder="password">
                     <button type="button" onclick="togglePassword('password', 'togglePassword1')"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-rose-500 transition-colors">
                        <i class="fa-solid fa-eye" id="togglePassword1"></i>
                     </button>
                  </div>
               </div>

               <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Confirm Password</label>
                  <div class="relative">
                     <input type="password" id="confirm_password" name="confirm_password" required
                        class="w-full px-5 py-4 pr-12 rounded-2xl border border-rose-100 bg-rose-50/30 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:bg-white transition-all"
                        placeholder="confirm password">
                     <button type="button" onclick="togglePassword('confirm_password', 'togglePassword2')"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-rose-500 transition-colors">
                        <i class="fa-solid fa-eye" id="togglePassword2"></i>
                     </button>
                  </div>
               </div>

               <button type="submit" id="submitBtn"
                  class="w-full bg-rose-500 hover:bg-rose-600 text-white py-4 rounded-2xl font-bold text-lg shadow-lg shadow-rose-200 transition-all active:scale-95 mt-2 flex justify-center items-center">
                  <span id="btnText">Create Account</span>
               </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
               <p class="text-gray-600 text-sm">
                  Already have an account?
                  <a href="login.php" class="text-rose-500 font-bold hover:underline">Sign In</a>
               </p>
            </div>

         </div>

         <div class="text-center mt-8">
            <a href="index.php" class="text-rose-400 hover:text-rose-600 text-sm font-medium transition-colors">
               <i class="fa-solid fa-arrow-left mr-2"></i> Back to Home
            </a>
         </div>
      </div>
   </main>

   <script>
      document.getElementById('registerForm').addEventListener('submit', async function (e) {
         e.preventDefault();

         const form = e.target;
         const password = document.getElementById('password').value;
         const confirmPassword = document.getElementById('confirm_password').value;

         if (password !== confirmPassword) {
            showToast('Passwords do not match', 'error');
            return;
         }

         const formData = new FormData(form);
         const submitBtn = document.getElementById('submitBtn');
         const btnText = document.getElementById('btnText');

         submitBtn.disabled = true;
         submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
         btnText.innerText = 'Processing...';

         try {
            const response = await fetch('routes/UserRoutes.php', {
               method: 'POST',
               body: formData
            });

            const result = await response.json();

            if (result.status === 'success') {
               showToast(result.message, 'success');
               setTimeout(() => {
                  window.location.href = 'login.php';
               }, 1500);
            } else {
               showToast(result.message, 'error');
            }
         } catch (error) {
            console.error('Error:', error);
            showToast('Could not connect to the server. Please check your connection.', 'error');
         } finally {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
            btnText.innerText = 'Create Account';
         }
      });
   </script>

   <?php require_once __DIR__ . "/partials/Foot.php"; ?>