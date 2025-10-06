<?php
    //กำหนด ชนิดข้อมูล (Content-Type) ของการตอบกลับเป็น application/json
    //เพื่อให้ฝั่ง JavaScript รู้ว่า response ที่ส่งกลับมาเป็น JSON ไม่ใช่ HTML ปกติ
header("Content-Type: application/json; charset=UTF-8");

    //ดึงไฟล์ config.php เข้ามาเพื่อเชื่อมต่อฐานข้อมูล
include "config.php";

    // สร้างคำสั่ง SQL เพื่อ “ดึงข้อมูลลูกค้าทั้งหมด”
    // โดยจะเลือกเฉพาะ 3 ฟิลด์ คือ:
    // cus_id → รหัสลูกค้า (เช่น CUS01)
    // cus_name → ชื่อลูกค้า
    // cus_username → อีเมลลูกค้า
    // และจะ เรียงลำดับจากรหัสลูกค้าน้อย → มาก (ORDER BY cus_id ASC)
$sql = "SELECT cus_id, cus_name, cus_username FROM customers ORDER BY cus_id ASC";
    // ใช้ฟังก์ชัน $conn->query() เพื่อ “สั่งรันคำสั่ง SQL” ที่เราเขียนไว้ด้านบน
    // และเก็บผลลัพธ์ในตัวแปร $result
    // ถ้า query สำเร็จ → $result จะมีข้อมูลแบบ object (mysqli_result)
    // ถ้า query ผิด (เช่น ชื่อตารางพิมพ์ผิด) → $result จะเป็น false
$result = $conn->query($sql);
   //สร้าง array เปล่าไว้เก็บข้อมูลลูกค้าทั้งหมด(แต่ละแถวจะถูกเพิ่มเข้ามาภายหลังใน while-loop)
$data = [];
    //ถ้า $result ไม่เป็น false แปลว่า query สำเร็จ → ดึงข้อมูลได้
if ($result) {
    //ใช้ while-loop อ่านข้อมูลแต่ละแถวออกมา
    //$result->fetch_assoc() จะดึงแถวจาก MySQL มาเป็น “Array แบบชื่อคอลัมน์” 
    //เช่น ["cus_id"=>"CUS01", "cus_name"=>"Supawat", "cus_username"=>"supawat@muit.ac.th"]
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    //ส่งข้อมูลออกไปเป็น JSON
    //ฟังก์ชัน json_encode() จะแปลง array ให้เป็น JSON string
    echo json_encode(["status" => "success", "data" => $data]);

    //กรณี query ผิดพลาด
    //ถ้าการ query มีปัญหา เช่น: ตาราง customers ไม่มีอยู่จริง    
    //คำสั่ง SQL เขียนผิดฐานข้อมูล disconnect 
    //ระบบจะตอบกลับ JSON แบบนี้แทน:{"status":"error","message":"ไม่สามารถดึงข้อมูลได้"}
} else {
    echo json_encode(["status" => "error", "message" => "ไม่สามารถดึงข้อมูลได้"]);
}
//ปิดการเชื่อมต่อฐานข้อมูลหลังจากเสร็จงาน เพื่อประหยัดทรัพยากรเซิร์ฟเวอร์
$conn->close();
?>
