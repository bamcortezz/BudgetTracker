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
        setTimeout(() => (window.location.href = "transactions.php"), 1500);
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
  const result = await Swal.fire({
    title: "Are you sure?",
    text: "You want to remove this record? 🌿",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#f43f5e",
    cancelButtonColor: "#6b7280",
    confirmButtonText: "Yes, delete it!",
    cancelButtonText: "Cancel",
  });

  if (!result.isConfirmed) return;

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
