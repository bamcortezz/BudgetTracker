function showToast(message, type = "success", duration = 3000) {
  const colors = {
    success: "linear-gradient(to right, #10b981, #059669)",
    error: "linear-gradient(to right, #f43f5e, #e11d48)",
    info: "linear-gradient(to right, #3b82f6, #2563eb)",
    warning: "linear-gradient(to right, #f59e0b, #d97706)",
  };

  Toastify({
    text: message,
    duration: duration,
    close: true,
    gravity: "top",
    position: "right",
    stopOnFocus: true,
    style: {
      background: colors[type] || colors.success,
      borderRadius: "12px",
      padding: "16px 24px",
      fontWeight: "600",
      fontSize: "14px",
      boxShadow: "0 10px 25px rgba(0, 0, 0, 0.1)",
    },
  }).showToast();
}

/**
 * Toggle password visibility
 * @param {string} inputId - The ID of the password input field
 * @param {string} iconId - The ID of the toggle icon element
 */
function togglePassword(inputId, iconId) {
  const input = document.getElementById(inputId);
  const icon = document.getElementById(iconId);

  if (input.type === "password") {
    input.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    input.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
}
