<?php
header("Content-Type: application/json; charset=UTF-8");
include "config.php";

$sql = "SELECT cus_id, cus_name, cus_username FROM customers ORDER BY cus_id ASC";
$result = $conn->query($sql);
$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $data]);
} else {
    echo json_encode(["status" => "error", "message" => "ไม่สามารถดึงข้อมูลได้"]);
}

$conn->close();
?>
