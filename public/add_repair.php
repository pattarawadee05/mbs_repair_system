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
    
    // ตั้งค่าฟิลด์ที่ไม่ได้ใช้ให้เป็นค่าว่างเพื่อไม่ให้ SQL พัง
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
    <title>MBS REPAIR - ระบบแจ้งซ่อมครุภัณฑ์ คณะการบัญชีและการจัดการ มมส</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #e0f2fe;
            background-image: radial-gradient(at 0% 0%, hsla(197,93%,88%,1) 0, transparent 50%), 
                              radial-gradient(at 100% 100%, hsla(204,90%,94%,1) 0, transparent 50%);
            min-height: 100vh;
            font-family: 'Sarabun', sans-serif;
            color: #1e3a8a;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }
        .form-container {
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border-radius: 35px;
            box-shadow: 0 20px 40px rgba(14, 165, 233, 0.1);
            padding: 40px 30px;
        }
        .logo-box {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-box img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-bottom: 15px;
        }
        .logo-box h2 {
            font-size: 22px;
            font-weight: 700;
            color: #032b69;
            margin-bottom: 4px;
        }
        .logo-box p {
            font-size: 13px;
            color: #2563eb;
            font-weight: 500;
        }
        .form-label {
            font-weight: 600;
            font-size: 14px;
            color: #032b69;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            border: 1px solid #d1edff;
            border-radius: 16px;
            padding: 13px 16px;
            font-size: 14px;
            background-color: #f8fafc;
            color: #334155;
        }
        .form-control:focus, .form-select:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.15);
            background-color: #fff;
        }
        .btn-submit {
            background: linear-gradient(90deg, #0256cc 0%, #00a2e8 100%);
            border: none;
            border-radius: 16px;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            box-shadow: 0 4px 12px rgba(2, 86, 204, 0.2);
            transition: all 0.2s ease;
            margin-top: 15px;
        }
        .btn-submit:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

<div class="form-container">
    <div class="logo-box">
        <!-- เรียกใช้รูปภาพโลโก้ mbs-logo.png ที่อยู่ในโฟลเดอร์เดียวกับไฟล์นี้โดยตรง -->
        <img src="mbs-logo.png" alt="MBS Logo">
        <h2>MBS REPAIR</h2>
        <p>ระบบแจ้งซ่อมครุภัณฑ์ คณะการบัญชีและการจัดการ มมส</p>
    </div>

    <form action="" method="POST">
        <input type="hidden" name="action" value="repair">
        
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
            <!-- จำกัดความยาวสูงสุด 10 หลัก และบล็อกไม่ให้พิมพ์เกินทาง JavaScript -->
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
            <textarea name="description" class="form-control" rows="4" placeholder="ระบุอาการชำรุดอย่างละเอียด เช่น หน้าจอดับ หรือแอร์ไม่เย็น" required></textarea>
        </div>

        <button type="submit" class="btn btn-submit w-100">ส่งข้อมูลแจ้งซ่อม</button>
    </form>
</div>

<script>
// ควบคุมและบล็อกช่องเบอร์โทรศัพท์ พิมพ์ได้เฉพาะตัวเลข และไม่เกิน 10 หลัก
document.getElementById('phoneInput').addEventListener('input', function (e) {
    this.value = this.value.replace(/[^0-9]/g, ''); // ลบอักขระที่ไม่ใช่ตัวเลขออกทันที
});

// โดลดข้อมูลห้องเรียนทั้งหมดจากตึก ACC.BIZ และ SBS มารวมอยู่ใน Dropdown เดียว
window.addEventListener('DOMContentLoaded', () => {
    const locationSelect = document.getElementById('locationSelect');
    fetch('get_rooms.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(room => {
                const option = document.createElement('option');
                option.value = `${room.building}|${room.room_number}|${room.room_type}`;
                // แสดงผลข้อความในเมนู เช่น ACC.BIZ103 (ตึก ACC.BIZ)
                option.textContent = `${room.room_number} (${room.building})`;
                locationSelect.appendChild(option);
            });
        })
        .catch(err => console.error("โหลดข้อมูลห้องผิดพลาด:", err));
});
</script>
</body>
</html>