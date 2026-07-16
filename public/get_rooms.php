<?php
// public/get_rooms.php
include '../config.php';

$building = isset($_GET['building']) ? $conn->real_escape_string($_GET['building']) : '';
$rooms = [];

if ($building != '') {
    $sql = "SELECT room_number, room_type FROM rooms WHERE building = '$building' ORDER BY room_number ASC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rooms);
?>