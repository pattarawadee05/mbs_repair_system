<?php
// public/get_rooms.php
include '../config.php';

// ดึงข้อมูลห้องทั้งหมดออกมาเพื่อเตรียมให้ฝั่ง JavaScript นำไปจัดหมวดหมู่กลุ่มอาคาร
$sql = "SELECT building, room_number, room_type FROM rooms ORDER BY building ASC, room_number ASC";
$result = $conn->query($sql);

$rooms = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rooms);
?>