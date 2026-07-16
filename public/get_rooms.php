<?php
// public/get_rooms.php
include '../config.php';

// ดึงรายชื่อห้องทั้งหมดจากตึก ACC.BIZ และ ตึก SBS ออกมาแสดงพร้อมกัน
$sql = "SELECT building, room_number, room_type FROM rooms ORDER BY building ASC, room_number ASC";
$result = $conn->query($sql);

$rooms = [];
while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rooms);
?>