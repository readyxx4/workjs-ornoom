function searchProduct() {
  let keyword = $("#keyword").val().trim();

  $("#summary").text("กำลังค้นหา...");
  $("#productTable tbody").empty();

  // แสดงตารางเมื่อค้นหา
  $("#productSection").show();

  $.ajax({
    url: "https://web.bisx.app/_webservice/getProductList.php",
    type: "POST",
    contentType: "application/json",
    data: JSON.stringify({ keyProductName: keyword }),
    success: function(response) {
      console.log(response);

      if (response.datalist && response.datalist.length > 0) {
        response.datalist.forEach(function(item) {
          let imgSrc = item.pro_image && item.pro_image.trim() !== ""
            ? `https://web.bisx.app/image_product/${item.pro_image}`
            : "https://via.placeholder.com/60x60?text=No+Image";

          let row = `
            <tr>
              <td><img src="${imgSrc}" alt="${item.pro_name}" 
                       onerror="this.onerror=null;this.src='https://via.placeholder.com/60x60?text=No+Image';"></td>
              <td>${item.pro_id}</td>
              <td>${item.pro_name}</td>
              <td>${item.protype_name}</td>
              <td>${item.pro_price}</td>
              <td>${item.pro_num}</td>
            </tr>
          `;
          $("#productTable tbody").append(row);
        });

        $("#summary").text(`ผลลัพธ์: 1 (success) พบ ${response.datalist.length} รายการ`);
      } else {
        $("#summary").text(`ผลลัพธ์: 0 พบ 0 รายการ`);
        $("#productTable tbody").append(
          `<tr><td colspan="6" style="text-align:center;">ไม่พบสินค้า</td></tr>`
        );
      }
    },
    error: function(error) {
      console.error("Error:", error);
      $("#summary").text("เกิดข้อผิดพลาดในการดึงข้อมูล");
      $("#productTable tbody").append(
        `<tr><td colspan="6" style="text-align:center; color:red;">เกิดข้อผิดพลาดในการดึงข้อมูล</td></tr>`
      );
    }
  });
}

function resetSearch() {
  $("#keyword").val("");
  $("#summary").text("");
  $("#productTable tbody").empty();

  // ซ่อนตารางเมื่อกดล้าง
  $("#productSection").hide();
}
