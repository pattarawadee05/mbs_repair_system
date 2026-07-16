<?php
// config.php
$host = "localhost";
$db_user = "root"; // เปลี่ยนให้ตรงกับ Username ของ phpMyAdmin บนเซิร์ฟเวอร์ ReadyIDC
$db_pass = "";     // ใส่รหัสผ่าน phpMyAdmin ของคุณ
$db_name = "mbs_system_db";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>