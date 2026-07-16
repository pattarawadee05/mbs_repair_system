<?php
// public/add_repair.php
include '../config.php';

// แก้ไขตรวจสอบค่า METHOD ของ SERVER ให้ถูกต้องป้องกันการส่งค่าล้มเหลว
if ($_SERVER["REQUEST_METHOD"] == "POST" || (isset($_POST['action']) && $_POST['action'] == 'repair')) {
    $reporter_name = $conn->real_escape_string($_POST['reporter_name']);
    $reporter_role = $conn->real_escape_string($_POST['reporter_role']);
    $department = $conn->real_escape_string($_POST['department']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $building = $conn->real_escape_string($_POST['building']);
    $room_number = $conn->real_escape_string($_POST['room_number']);
    $room_type = $conn->real_escape_string($_POST['room_type']);
    $device_type = $conn->real_escape_string($_POST['device_type']);
    $description = $conn->real_escape_string($_POST['description']);
    
    // ตั้งค่าไอดีจำลองไลน์ (ในอนาคตเชื่อมกับระบบลงทะเบียนของ Line OA ได้)
    $line_user_id = "LINE_MBS_" . uniqid(); 
    
    // การจัดการไฟล์อัปโหลด (รูปภาพ/วิดีโอ)
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
        echo "<script>alert('ระบบส่งข้อมูลแจ้งซ่อมเรียบร้อยแล้ว เจ้าหน้าที่จะรีบดำเนินการค่ะ'); window.location.href='add_repair.php';</script>";
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
            background: linear-gradient(135deg, #e0f2fe 0%, #ecfeff 100%);
            min-height: 100vh;
            font-family: 'Sarabun', sans-serif;
            color: #1e3a8a;
        }
        .form-container {
            max-width: 650px;
            margin: 40px auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(14, 165, 233, 0.1);
            padding: 30px;
        }
        .logo-box {
            text-align: center;
            margin-bottom: 25px;
        }
        .logo-box img {
            width: 70px;
            margin-bottom: 10px;
        }
        .logo-box h2 {
            font-size: 22px;
            font-weight: 700;
            color: #032b69;
            margin-bottom: 2px;
        }
        .logo-box p {
            font-size: 13px;
            color: #2563eb;
        }
        .form-label {
            font-weight: 500;
            font-size: 14px;
            color: #032b69;
            margin-bottom: 6px;
        }
        .form-control, .form-select {
            border: 1px solid #d1edff;
            border-radius: 12px;
            padding: 12px;
            font-size: 14px;
            background-color: #f8fafc;
        }
        .form-control:focus, .form-select:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.15);
            background-color: #fff;
        }
        .btn-submit {
            background: linear-gradient(90deg, #0284c7 0%, #0ea5e9 100%);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

<div class="container px-3">
    <div class="form-container">
        <div class="logo-box">
            <img src="https://upload.wikimedia.org/wikipedia/th/f/fc/Mahasarakham_University_Logo.png" alt="MSU Logo">
            <h2>MBS REPAIR</h2>
            <p>ระบบแจ้งซ่อมครุภัณฑ์ คณะการบัญชีและการจัดการ มมส</p>
        </div>

        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="repair">
            
            <div class="mb-3">
                <label class="form-label">ชื่อ-นามสกุล ผู้แจ้งซ่อม</label>
                <input type="text" name="reporter_name" class="form-control" placeholder="กรอกชื่อและนามสกุลของคุณ" required>
            </div>

            <div class="mb-3">
                <label class="form-label">ประเภทผู้แจ้งซ่อม</label>
                <select name="reporter_role" class="form-select" required>
                    <option value="">-- กรุณาเลือกประเภทผู้ใช้งาน --</option>
                    <option value="นิสิต">นิสิต</option>
                    <option value="อาจารย์">อาจารย์</option>
                    <option value="บุคลากร">บุคลากร</option>
                    <option value="เจ้าหน้าที่">เจ้าหน้าที่</option>
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

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">อาคาร</label>
                    <select name="building" id="buildingSelect" class="form-select" required>
                        <option value="">-- เลือกอาคาร --</option>
                        <option value="ตึก ACC.BIZ">ตึก ACC.BIZ</option>
                        <option value="ตึก SBS">ตึก SBS</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ห้อง</label>
                    <select name="room_number" id="roomSelect" class="form-select" required disabled>
                        <option value="">-- กรุณาเลือกอาคารก่อน --</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">ประเภทห้อง</label>
                <input type="text" id="room_type" name="room_type" class="form-control" readonly placeholder="ระบบจะระบุประเภทห้องให้อัตโนมัติ">
            </div>

            <div class="mb-3">
                <label class="form-label">เลือกประเภทอุปกรณ์ / ครุภัณฑ์ที่ชำรุด</label>
                <select name="device_type" class="form-select" required>
                    <option value="">-- กรุณาเลือกอุปกรณ์ชำรุด --</option>
                    <option value="คอมพิวเตอร์">คอมพิวเตอร์</option>
                    <option value="เครื่องพิมพ์">เครื่องพิมพ์</option>
                    <option value="เครื่องปรับอากาศ">เครื่องปรับอากาศ</option>
                    <option value="ระบบไฟฟ้า">จอ / ทีวี / โปรเจคเตอร์</option>
                    <option value="เครือข่าย">ไมค์ / เครื่องเสียง</option>
                    <option value="อื่นๆ">อื่นๆ</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">รายละเอียดปัญหา / อาการชำรุด</label>
                <textarea name="description" class="form-control" rows="4" placeholder="ระบุอาการชำรุดอย่างละเอียด เช่น หน้าจอดับ หรือแอร์ไม่เย็น" required></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">หลักฐานรูปภาพหรือวิดีโอประกอบ</label>
                <input type="file" name="evidence_file" class="form-control" accept="image/*,video/*">
            </div>

            <button type="submit" class="btn btn-submit w-100">ส่งข้อมูลแจ้งซ่อม</button>
        </form>
    </div>
</div>

<script>
document.getElementById('buildingSelect').addEventListener('change', function() {
    const building = this.value;
    const roomSelect = document.getElementById('roomSelect');
    const roomTypeInput = document.getElementById('room_type');
    
    roomSelect.innerHTML = '<option value="">-- กำลังโหลดรายชื่อห้อง --</option>';
    roomSelect.disabled = true;
    roomTypeInput.value = '';

    if (!building) {
        roomSelect.innerHTML = '<option value="">-- กรุณาเลือกอาคารก่อน --</option>';
        return;
    }

    fetch(`get_rooms.php?building=${encodeURIComponent(building)}`)
        .then(response => response.json())
        .then(data => {
            roomSelect.innerHTML = '<option value="">-- กรุณาเลือกห้อง --</option>';
            window.currentRoomsData = data; 
            
            data.forEach(room => {
                const option = document.createElement('option');
                option.value = room.room_number;
                option.textContent = room.room_number;
                roomSelect.appendChild(option);
            });
            roomSelect.disabled = false;
        });
});

document.getElementById('roomSelect').addEventListener('change', function() {
    const selectedRoom = this.value;
    const roomTypeInput = document.getElementById('room_type');
    
    if (window.currentRoomsData) {
        const found = window.currentRoomsData.find(r => r.room_number === selectedRoom);
        if (found) {
            roomTypeInput.value = found.room_type;
        }
    }
});
</script>
</body>
</html>