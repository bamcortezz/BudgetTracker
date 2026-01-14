<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>BudgetTracker | Bloom</title>
   <script src="https://cdn.tailwindcss.com"></script>
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
   <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
   <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
   <script src="assets/js/toast.js"></script>
   <script src="assets/js/togglePassword.js"></script>
   <style>
      @keyframes float {

         0%,
         100% {
            transform: translateY(0px);
         }

         50% {
            transform: translateY(-20px);
         }
      }

      .float-animation {
         animation: float 3s ease-in-out infinite;
      }

      @keyframes gradient {

         0%,
         100% {
            background-position: 0% 50%;
         }

         50% {
            background-position: 100% 50%;
         }
      }

      .gradient-animate {
         background-size: 200% 200%;
         animation: gradient 8s ease infinite;
      }
   </style>
</head>