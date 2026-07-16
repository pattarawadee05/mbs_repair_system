<?php
// config.php
$host = "localhost";
$db_user = "root"; // ตรวจสอบรหัสผ่านและ user ของเซิร์ฟเวอร์ ReadyIDC
$db_pass = "Project_02";     // ใส่รหัสผ่านของ phpMyAdmin
$db_name = "mbs_system_db";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>