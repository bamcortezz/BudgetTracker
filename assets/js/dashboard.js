const modal = document.getElementById("transactionModal");
const openModalBtn = document.getElementById("openModalBtn");

function openModal() {
  modal.classList.remove("hidden");
  document.body.style.overflow = "hidden";
}

function closeModal() {
  modal.classList.add("hidden");
  document.body.style.overflow = "auto";
  document.getElementById("transactionForm").reset();
}

if (openModalBtn) openModalBtn.addEventListener("click", openModal);

document
  .getElementById("transactionForm")
  .addEventListener("submit", async function (e) {
    e.preventDefault();
    const saveBtn = document.getElementById("saveBtn");
    const saveBtnText = document.getElementById("saveBtnText");
    saveBtn.disabled = true;
    saveBtnText.innerText = "Saving...";

    try {
      const response = await fetch("routes/TransactionRoutes.php", {
        method: "POST",
        body: new FormData(this),
      });
      const result = await response.json();
      if (result.status === "success") {
        showToast(result.message, "success");
        setTimeout(() => location.reload(), 1000);
      } else {
        showToast(result.message, "error");
      }
    } catch (error) {
      showToast("Submission failed.", "error");
    } finally {
      saveBtn.disabled = false;
      saveBtnText.innerText = "Save Transaction";
    }
  });

async function deleteTransaction(id) {
  if (!confirm("Are you sure you want to remove this record? 🌿")) return;

  const formData = new FormData();
  formData.append("delete_transaction", "1");
  formData.append("id", id);
  formData.append(
    "csrf_token",
    document.querySelector('input[name="csrf_token"]').value
  );

  try {
    const response = await fetch("routes/TransactionRoutes.php", {
      method: "POST",
      body: formData,
    });
    const result = await response.json();
    if (result.status === "success") {
      showToast(result.message, "success");
      setTimeout(() => location.reload(), 800);
    } else {
      showToast(result.message, "error");
    }
  } catch (error) {
    showToast("Error deleting transaction", "error");
  }
}

if (document.getElementById("logoutForm")) {
  document
    .getElementById("logoutForm")
    .addEventListener("submit", async function (e) {
      e.preventDefault();
      if (!confirm("Are you sure you want to logout? 🌸")) return;

      const logoutBtn = this.querySelector("button");
      logoutBtn.disabled = true;
      logoutBtn.innerText = "Logging out...";

      try {
        const response = await fetch("routes/UserRoutes.php", {
          method: "POST",
          body: new FormData(this),
        });
        const result = await response.json();
        if (result.status === "success") window.location.href = "index.php";
      } catch (error) {
        showToast("Logout failed.", "error");
        logoutBtn.disabled = false;
        logoutBtn.innerText = "Logout";
      }
    });
}
