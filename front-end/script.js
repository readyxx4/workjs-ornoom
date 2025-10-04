document.getElementById("cusForm").addEventListener("submit", async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);
  formData.append("action", "add");

  const res = await fetch("../setCustomer.php", {
    method: "POST",
    body: formData
  });
  const data = await res.json();
  const msg = document.getElementById("msg");

  msg.textContent = data.message;
  msg.style.color = data.status === "success" ? "green" : "red";

  if (data.status === "success") {
    e.target.reset(); // ล้างฟอร์มเมื่อบันทึกสำเร็จ
  }
});
