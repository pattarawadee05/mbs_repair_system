<?php
// public/add_repair.php
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" || (isset($_POST['action']) && $_POST['action'] == 'repair')) {
    $reporter_name = $conn->real_escape_string($_POST['reporter_name']);
    $reporter_role = $conn->real_escape_string($_POST['reporter_role']);
    $phone = $conn->real_escape_string($_POST['phone']);
    
    // ผ่าแยกข้อมูล อาคาร | ห้อง | ประเภทห้อง จากตัวเลือก Dropdown เดียว
    $location_data = explode('|', $_POST['building_room']);
    $building = $conn->real_escape_string($location_data[0]);
    $room_number = $conn->real_escape_string($location_data[1]);
    $room_type = $conn->real_escape_string($location_data[2]);
    
    $device_type = $conn->real_escape_string($_POST['device_type']);
    $description = $conn->real_escape_string($_POST['description']);
    $line_user_id = "LINE_MBS_" . uniqid(); 
    
    $department = "";
    $file_name = null;

    $sql = "INSERT INTO repair_requests (line_user_id, reporter_name, reporter_role, department, phone, building, room_type, room_number, device_type, description, image_before, status) 
            VALUES ('$line_user_id', '$reporter_name', '$reporter_role', '$department', '$phone', '$building', '$room_type', '$room_number', '$device_type', '$description', '$file_name', 'รอดำเนินการ')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('ระบบส่งข้อมูลแจ้งซ่อมเรียบร้อยแล้ว'); window.location.href='add_repair.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด: " . $conn->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งซ่อมออนไลน์ - MBS REPAIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f4f7fc !important;
            min-height: 100vh;
            font-family: 'Sarabun', sans-serif;
            color: #4a5568;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .mbs-card-container {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 30px 25px;
        }
        .mbs-logo-header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 20px;
        }
        .mbs-logo-header img {
            width: 90px;
            height: 90px;
            object-fit: contain;
            margin-bottom: 12px;
        }
        .mbs-logo-header h2 {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }
        .mbs-logo-header p {
            font-size: 12px;
            color: #2563eb;
            margin: 4px 0 0 0;
        }
        .form-label {
            font-weight: 500;
            font-size: 13px;
            color: #64748b;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 15px;
            padding: 12px 16px;
            font-size: 14px;
            background-color: #ffffff;
        }
        .btn-submit-mbs {
            background: linear-gradient(90deg, #2563eb 0%, #06b6d4 100%);
            border: none;
            border-radius: 20px;
            padding: 14px;
            font-size: 15px;
            font-weight: 600;
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
            margin-top: 15px;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="mbs-card-container">
    <div class="mbs-logo-header">
        <img src="mbs-logo.png" alt="MBS Logo">
        <h2>MBS REPAIR</h2>
        <p>ระบบแจ้งซ่อมครุภัณฑ์ คณะการบัญชีและการจัดการ มมส</p>
    </div>

    <form action="" method="POST">
        <input type="hidden" name="action" value="repair">
        <input type="hidden" id="room_type" name="room_type">
        
        <div class="mb-3">
            <label class="form-label">ชื่อ-นามสกุล ผู้แจ้งซ่อม</label>
            <input type="text" name="reporter_name" class="form-control" placeholder="กรอกชื่อและนามสกุลของคุณ" required>
        </div>

        <div class="mb-3">
            <label class="form-label">ประเภทผู้แจ้งซ่อม</label>
            <select name="reporter_role" class="form-select" required>
                <option value="">-- กรุณาเลือกประเภทผู้ใช้งาน --</option>
                <option value="บุคลากร">บุคลากร</option>
                <option value="อาจารย์">อาจารย์</option>
                <option value="นิสิต">นิสิต</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">หมายเลขโทรศัพท์</label>
            <input type="text" name="phone" id="phoneInput" class="form-control" placeholder="ระบุเบอร์โทรศัพท์ 10 หลัก" required maxlength="10">
        </div>

        <div class="mb-3">
            <label class="form-label">อาคาร/ห้อง</label>
            <select name="building_room" id="locationSelect" class="form-select" required>
                <option value="">-- กรุณาเลือก อาคาร และ ห้องเรียน --</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">เลือกประเภทอุปกรณ์</label>
            <select name="device_type" class="form-select" required>
                <option value="">-- กรุณาเลือกประเภทอุปกรณ์ --</option>
                <option value="คอมพิวเตอร์">คอมพิวเตอร์</option>
                <option value="เครื่องพิมพ์">เครื่องพิมพ์</option>
                <option value="เครื่องปรับอากาศ">เครื่องปรับอากาศ</option>
                <option value="จอ / ทีวี / โปรเจคเตอร์">จอ / ทีวี / โปรเจคเตอร์</option>
                <option value="ไมค์ / เครื่องเสียง">ไมค์ / เครื่องเสียง</option>
                <option value="อื่นๆ">อื่นๆ</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">รายละเอียดปัญหา / อาการชำรุด</label>
            <textarea name="description" class="form-control" rows="4" placeholder="ระบุอาการชำรุด เช่น หน้าจอดับ หรือแอร์ไม่เย็น" required></textarea>
        </div>

        <button type="submit" class="btn btn-submit-mbs">ส่งข้อมูลแจ้งซ่อม</button>
    </form>
</div>

<script>
// ล็อกให้พิมพ์ได้เฉพาะตัวเลขในช่องเบอร์โทร
document.getElementById('phoneInput').addEventListener('input', function (e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// โดลดรายชื่อห้องทั้งหมดขึ้นมาแสดงใน Dropdown เดียว
window.addEventListener('DOMContentLoaded', () => {
    const locationSelect = document.getElementById('locationSelect');
    fetch('get_rooms.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(room => {
                const option = document.createElement('option');
                option.value = `${room.building}|${room.room_number}|${room.room_type}`;
                option.textContent = `${room.room_number} (${room.building})`;
                locationSelect.appendChild(option);
            });
        });
});

document.getElementById('locationSelect').addEventListener('change', function() {
    if(this.value) {
        const parts = this.value.split('|');
        document.getElementById('room_type').value = parts[2];
    }
});
</script>
</body>
</html>