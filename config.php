<?php
    $servername = "localhost"; //คือชื่อของเครื่องเซิร์ฟเวอร์ฐานข้อมูล ปกติถ้าใช้ XAMPP ในเครื่องตัวเอง จะเป็น "localhost"
    $username = "root"; //ชื่อผู้ใช้ของ MySQL (ค่าเริ่มต้นของ XAMPP คือ "root")
    $password = ""; //รหัสผ่านของ MySQL (ค่าเริ่มต้นของ XAMPP คือ ว่าง)
    $dbname = "shop"; //ชื่อฐานข้อมูลที่จะใช้ (ในที่นี้คือ "shop")


        // คำสั่งนี้สร้าง “อ็อบเจกต์” (object) สำหรับเชื่อมต่อฐานข้อมูล
        // โดยใช้คลาสชื่อว่า mysqli (ย่อจาก MySQL Improved)

        // ✅ ถ้าเชื่อมต่อสำเร็จ → ตัวแปร $conn จะเก็บ “การเชื่อมต่อ” เอาไว้
        // ❌ ถ้าเชื่อมต่อล้มเหลว → จะเกิด error ที่เราตรวจสอบได้ในบรรทัดถัดไป
    $conn = new mysqli($servername, $username, $password, $dbname);
        // $conn->connect_error → ตรวจสอบว่ามีข้อผิดพลาดตอนเชื่อมต่อไหม
        // ถ้ามี (true) → สั่ง die() เพื่อหยุดการทำงานทั้งหมดทันทีและแสดงข้อความว่า
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
?>