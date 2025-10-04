<?php
header("Content-Type: application/json; charset=UTF-8");
include "config.php";

$action = $_POST['action'] ?? '';

if ($action == "add") {
    $cus_name = $_POST['cus_name'] ?? '';
    $cus_username = $_POST['cus_username'] ?? '';
    $cus_password = $_POST['cus_password'] ?? '';
    $cus_confirm = $_POST['cus_confirm'] ?? '';

    // ตรวจสอบความครบของข้อมูล
    if (!$cus_name || !$cus_username || !$cus_password || !$cus_confirm) {
        echo json_encode(["status" => "error", "message" => "กรอกข้อมูลไม่ครบ"]);
        exit;
    }

    // ตรวจสอบรหัสผ่านตรงกันไหม
    if ($cus_password !== $cus_confirm) {
        echo json_encode(["status" => "error", "message" => "รหัสผ่านไม่ตรงกัน"]);
        exit;
    }

    // สร้างรหัสลูกค้าใหม่ (CUS + running number)
    $sql_last = "SELECT cus_no FROM customers ORDER BY cus_no DESC LIMIT 1";
    $res_last = $conn->query($sql_last);
    $new_id = "CUS01";
    if ($res_last && $res_last->num_rows > 0) {
        $row = $res_last->fetch_assoc();
        $next = str_pad($row['cus_no'] + 1, 2, "0", STR_PAD_LEFT);
        $new_id = "CUS" . $next;
    }

    // เพิ่มข้อมูล
    $sql = "INSERT INTO customers (cus_id, cus_name, cus_username, cus_password, cus_session)
            VALUES (?, ?, ?, ?, '')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $new_id, $cus_name, $cus_username, $cus_password);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "บันทึกข้อมูลลูกค้าสำเร็จ"]);
    } else {
        echo json_encode(["status" => "error", "message" => "ไม่สามารถบันทึกข้อมูลได้: " . $conn->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

echo json_encode(["status" => "error", "message" => "action ไม่ถูกต้อง"]);
?>
