<?php
header("Content-Type: application/json; charset=UTF-8");
include "config.php";
    //รับค่าจาก Front-end
    //ดึงค่าชื่อ action จากข้อมูลที่ส่งมาจาก JavaScript (ผ่าน fetch())
    //ถ้า JS ส่ง action=add → หมายถึง “เพิ่มลูกค้าใหม่”
    //ถ้าไม่มีค่า → จะได้เป็น '' (ว่าง) เพื่อป้องกัน error
$action = $_POST['action'] ?? '';
    //เช็กก่อนว่าคำขอที่เข้ามาเป็น add หรือไม่ ถ้าไม่ใช่ → จะไม่ทำอะไร (และจะแสดง error ด้านล่างสุด)
if ($action == "add") {
    // ดึงค่าที่ส่งจาก <form> ของหน้า HTML
    // เช่นใน <input name="cus_name">, <input name="cus_username">, ฯลฯ
    // เครื่องหมาย ?? '' คือ Null Coalescing Operator
    // แปลว่า “ถ้าไม่มีค่า ให้ใช้ค่าว่างแทน” เพื่อกัน error
    $cus_name = $_POST['cus_name'] ?? '';
    $cus_username = $_POST['cus_username'] ?? '';
    $cus_password = $_POST['cus_password'] ?? '';
    $cus_confirm = $_POST['cus_confirm'] ?? '';
    // ตรวจสอบว่ากรอกข้อมูลครบหรือไม่ เช็กว่า input ใด ๆ เป็นค่าว่างหรือไม่
    // ถ้าไม่ครบ → ส่งข้อความกลับให้ JS ว่า “กรอกข้อมูลไม่ครบ”
    // exit; จะหยุดไม่ให้ PHP ดำเนินต่อ
    if (!$cus_name || !$cus_username || !$cus_password || !$cus_confirm) {
        echo json_encode(["status" => "error", "message" => "กรอกข้อมูลไม่ครบ"]);
        exit;
    }
    // ตรวจสอบรหัสผ่านตรงกันไหม
    if ($cus_password !== $cus_confirm) {
        echo json_encode(["status" => "error", "message" => "รหัสผ่านไม่ตรงกัน"]);
        exit;
    }
    //สร้างรหัสลูกค้าใหม่ (CUS + running number)
    //เลือกข้อมูลคอลัมน์ cus_no (เลขลำดับลูกค้าในตาราง) จากตาราง customers
    //โดยเรียงจากมากไปน้อย (DESC)
    //และเอามาเพียง 1 แถว (LIMIT 1)
    //ผลลัพธ์ที่ได้จะเป็น “ลูกค้าคนล่าสุด” cus05
    $sql_last = "SELECT cus_no FROM customers ORDER BY cus_no DESC LIMIT 1";
    //ส่งคำสั่ง SQL ที่เราเขียนข้างบนไปให้ฐานข้อมูล MySQL ผ่าน $conn
    // ผลลัพธ์จะถูกเก็บใน $res_last
    // ถ้ารัน SQL ได้ → $res_last จะเป็น object ที่มีข้อมูล
    // ถ้ารันไม่สำเร็จ → $res_last จะเป็น false
    $res_last = $conn->query($sql_last);
    // ตั้งค่ารหัสลูกค้าเริ่มต้นไว้ก่อน
    // กรณีที่ในตารางยังไม่มีลูกค้าเลย (ฐานข้อมูลว่าง)
    // ระบบจะเริ่มต้นที่ "CUS01"
    $new_id = "CUS01";
    // เช็กว่า query สำเร็จไหม และมีข้อมูลกลับมาหรือเปล่า
    // (เช่น มีลูกค้าเก่าอยู่ในระบบแล้ว)
    // $res_last = true → SQL ทำงานได้
    // $res_last->num_rows > 0 → มีแถวข้อมูลกลับมาอย่างน้อย 1 แถว
    // ถ้าทั้งสองเงื่อนไขเป็นจริง → เข้าไปทำต่อในบล็อกนี้
    if ($res_last && $res_last->num_rows > 0) {
        //ดึงข้อมูลแถวแรกจาก $res_last ออกมาเป็น Array


        // $row = $res_last->fetch_assoc();
        // คำนวณหมายเลขลูกค้าคนถัดไป
        // เอา cus_no เดิมมา +1 → 7 + 1 = 8
        // จากนั้นใช้ str_pad() เพื่อให้ตัวเลขมี 2 หลักเสมอ
        // เช่น
        // 1 → "01"
        // 8 → "08"
        // 15 → "15" (ไม่ต้องเติมศูนย์)
        $next = str_pad($row['cus_no'] + 1, 2, "0", STR_PAD_LEFT);
        $new_id = "CUS" . $next;
    }

    // เพิ่มข้อมูล เตรียมคำสั่ง SQL แบบปลอดภัย
    //บรรทัดนี้คือคำสั่ง SQL ที่เตรียมไว้เพื่อ เพิ่มข้อมูลลูกค้าใหม่ ลงในตาราง customersโดยจะใส่ค่าใน 5 คอลัมน์ ได้แก่

    //INSERT INTO customers (...)
    //คือคำสั่งบอก MySQL ว่า “เราจะเพิ่มข้อมูลใหม่เข้าไปในตารางชื่อ customers”
    $sql = "INSERT INTO customers (cus_id, cus_name, cus_username, cus_password, cus_session)
            VALUES (?, ?, ?, ?, '')";
    $stmt = $conn->prepare($sql);
    //คือการ “ผูกค่า” จากตัวแปร PHP เข้ากับเครื่องหมาย ? ใน SQL
    // "ssss" หมายถึง ค่าที่ส่งไปทั้ง 4 ตัว เป็น string (ตัวอักษรทั้งหมด)
    $stmt->bind_param("ssss", $new_id, $cus_name, $cus_username, $cus_password);

    //หมายถึง → สั่งให้ “รันคำสั่ง SQL” ที่เราเตรียมไว้ก่อนหน้านี้จริง ๆ
    // (ในกรณีนี้คือคำสั่ง INSERT ข้อมูลลูกค้าใหม่)
    // ถ้า execute() ทำงานสำเร็จ (เช่น เพิ่มข้อมูลได้จริง)
    // จะได้ค่า true → เข้าสู่ if
    // แต่ถ้าเกิดข้อผิดพลาด (เช่น ฐานข้อมูลมีปัญหา หรือฟิลด์สะกดผิด)
    // จะได้ค่า false → เข้าสู่ else
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "บันทึกข้อมูลลูกค้าสำเร็จ"]);
    } else {
        echo json_encode(["status" => "error", "message" => "ไม่สามารถบันทึกข้อมูลได้: " . $conn->error]);
    }

    // $stmt->close();	ปิดคำสั่ง SQL ที่เตรียมไว้ (Prepared Statement)
    // $conn->close();	ปิดการเชื่อมต่อกับฐานข้อมูล MySQL
    // exit;	หยุดการทำงานของไฟล์ PHP (ไม่ให้รันอะไรต่อ)
    $stmt->close();
    $conn->close();
    exit;
}

echo json_encode(["status" => "error", "message" => "action ไม่ถูกต้อง"]);
?>
