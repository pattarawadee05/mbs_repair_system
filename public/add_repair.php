<?php
// public/add_repair.php
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" || (isset($_POST['action']) && $_POST['action'] == 'repair')) {
    $reporter_name = $conn->real_escape_string($_POST['reporter_name']);
    $reporter_role = $conn->real_escape_string($_POST['reporter_role']);
    $department = $conn->real_escape_string($_POST['department']);
    $phone = $conn->real_escape_string($_POST['phone']);
    
    // แยกข้อมูลห้องและอาคารที่ส่งมาจากค่าตัวเลือกเดี่ยว
    $location_data = explode('|', $_POST['location']);
    $building = $conn->real_escape_string($location_data[0]);
    $room_number = $conn->real_escape_string($location_data[1]);
    $room_type = $conn->real_escape_string($location_data[2]);
    
    $device_type = $conn->real_escape_string($_POST['device_type']);
    $description = $conn->real_escape_string($_POST['description']);
    $line_user_id = "LINE_MBS_" . uniqid(); 
    
    $file_name = null;
    if (isset($_FILES['evidence_file']) && $_FILES['evidence_file']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $ext = pathinfo($_FILES['evidence_file']['name'], PATHINFO_EXTENSION);
        $file_name = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['evidence_file']['tmp_name'], $target_dir . $file_name);
    }

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
            background: #f4f7fc;
            min-height: 100vh;
            font-family: 'Sarabun', sans-serif;
            color: #4a5568;
            display: flex;
            justify-content: center;
            padding: 20px 0;
        }
        .form-container {
            width: 100%;
            max-width: 450px;
            background: #ffffff;
            border-radius: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
            padding: 30px 25px;
        }
        .header-title {
            text-align: center;
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 25px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 15px;
        }
        .form-label {
            font-weight: 500;
            font-size: 13px;
            color: #64748b;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        .form-control, .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 15px;
            padding: 12px 16px;
            font-size: 14px;
            background-color: #ffffff;
            color: #334155;
        }
        .form-control::placeholder {
            color: #94a3b8;
        }
        .upload-zone {
            border: 1px dashed #cbd5e1;
            border-radius: 20px;
            padding: 20px;
            background: #f8fafc;
        }
        .btn-upload {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 8px 20px;
            font-size: 13px;
            color: #2563eb;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .preview-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-top: 15px;
        }
        .preview-box {
            aspect-ratio: 1;
            border-radius: 8px;
            overflow: hidden;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .preview-box img, .preview-box video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .btn-submit {
            background: linear-gradient(90deg, #3b82f6 0%, #06b6d4 100%);
            border: none;
            border-radius: 20px;
            padding: 14px;
            font-size: 15px;
            font-weight: 600;
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <div class="header-title">แจ้งซ่อมออนไลน์ - MBS REPAIR</div>

    <form action="" method="POST" enctype="multipart/form-data">
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
            <label class="form-label">หน่วยงาน / สาขาวิชา</label>
            <input type="text" name="department" class="form-control" placeholder="เช่น สาขาคอมพิวเตอร์ธุรกิจ / สำนักงานเลขานุการ" required>
        </div>

        <div class="mb-3">
            <label class="form-label">หมายเลขโทรศัพท์</label>
            <input type="tel" name="phone" class="form-control" placeholder="ระบุเบอร์โทรศัพท์ 10 หลัก" required pattern="[0-9]{10}">
        </div>

        <div class="mb-3">
            <label class="form-label">ห้อง / ภาควิชา / 🏢 สถานที่เกิดปัญหา</label>
            <select name="location" id="locationSelect" class="form-select" required>
                <option value="">-- กรุณาเลือกห้องเรียน / สถานที่ --</option>
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
            <textarea name="description" class="form-control" rows="3" placeholder="ระบุอาการชำรุด เช่น หน้าจอดับ หรือเครื่องปริ้นกระดาษติด" required></textarea>
        </div>

        <div class="mb-4">
            <div class="upload-zone text-center">
                <label class="form-label justify-content-center">หลักฐานรูปภาพหรือวิดีโอประกอบ</label>
                <label for="fileInput" class="btn-upload mt-2" style="cursor: pointer;">
                    📁 คลิกเพื่อเลือกไฟล์แนบ
                </label>
                <input type="file" id="fileInput" name="evidence_file" class="d-none" accept="image/*,video/*" onchange="previewMedia(event)">
                
                <div class="preview-container">
                    <div class="preview-box" id="p1"></div>
                    <div class="preview-box" id="p2"></div>
                    <div class="preview-box" id="p3"></div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-submit w-100">ส่งข้อมูลแจ้งซ่อม</button>
    </form>
</div>

<script>
// โหลดรายชื่อห้องทั้งหมดขึ้นมาแสดงใน Dropdown ช่องเดียวทันทีที่เปิดหน้าเว็บ
window.addEventListener('DOMContentLoaded', () => {
    const locationSelect = document.getElementById('locationSelect');
    fetch('get_rooms.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(room => {
                const option = document.createElement('option');
                // รวมข้อมูลส่งแบบมี Format ไปผ่าค่ายิงเข้าฐานข้อมูลหลังบ้าน
                option.value = `${room.building}|${room.room_number}|${room.room_type}`;
                option.textContent = `${room.room_number} - ${room.building} (${room.room_type})`;
                locationSelect.appendChild(option);
            });
        });
});

// ดักจับการเลือกสถานที่เพื่อแมปประเภทห้องเข้าตัวแปร Hidden
document.getElementById('locationSelect').addEventListener('change', function() {
    if(this.value) {
        const parts = this.value.split('|');
        document.getElementById('room_type').value = parts[2];
    }
});

// ฟังก์ชันสร้าง Preview รูปภาพ/วิดีโอแบบสด ๆ ทันทีที่ผู้ใช้เลือกไฟล์แนบ
function previewMedia(event) {
    const file = event.target.files[0];
    const p1 = document.getElementById('p1');
    p1.innerHTML = '';
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            if (file.type.startsWith('image/')) {
                p1.innerHTML = `<img src="${e.target.result}">`;
            } else if (file.type.startsWith('video/')) {
                p1.innerHTML = `<video src="${e.target.result}" controls></video>`;
            }
        }
        reader.readAsDataURL(file);
    }
}
</script>
</body>
</html>