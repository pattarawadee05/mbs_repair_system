<?php
// public/add_repair.php
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" || (isset($_POST['action']) && $_POST['action'] == 'repair')) {
    $reporter_name = $conn->real_escape_string($_POST['reporter_name']);
    $reporter_role = $conn->real_escape_string($_POST['reporter_role']);
    $phone = $conn->real_escape_string($_POST['phone']);
    
    // ผ่าแยกข้อมูล อาคาร | ห้อง | ประเภทห้อง จากตัวเลือกเดี่ยว
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
            background: #e0f2fe !important;
            background-image: radial-gradient(at 0% 0%, hsla(197,93%,88%,1) 0, transparent 50%), 
                              radial-gradient(at 100% 100%, hsla(204,90%,94%,1) 0, transparent 50%) !important;
            min-height: 100vh;
            font-family: 'Sarabun', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
            margin: 0;
        }
        .mbs-new-box {
            width: 100%;
            max-width: 450px;
            background: #ffffff !important;
            border-radius: 30px !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05) !important;
            padding: 35px 25px !important;
        }
        .mbs-logo-section {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 15px;
        }
        .mbs-logo-section img {
            width: 85px;
            height: 85px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        .mbs-logo-section h2 {
            font-size: 20px;
            font-weight: 700;
            color: #0c2340;
            margin: 0;
        }
        .mbs-logo-section p {
            font-size: 12px;
            color: #2563eb;
            margin: 4px 0 0 0;
        }
        .form-label {
            font-weight: 600;
            font-size: 13px;
            color: #0c2340;
            margin-bottom: 6px;
        }
        .form-control, .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 15px;
            padding: 12px 16px;
            font-size: 14px;
            background-color: #ffffff;
            color: #334155;
        }
        .form-control:focus, .form-select:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.12);
        }
        .btn-submit-gradient {
            background: linear-gradient(90deg, #1d4ed8 0%, #0284c7 100%) !important;
            border: none;
            border-radius: 15px;
            padding: 14px;
            font-size: 15px;
            font-weight: 600;
            color: white;
            box-shadow: 0 4px 12px rgba(29, 78, 216, 0.2);
            width: 100%;
            margin-top: 15px;
        }
        .btn-submit-gradient:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }
        /* ตกแต่งแถวหัวข้อกลุ่มอาคารให้ดูสวย เด่น และอ่านง่าย */
        optgroup {
            font-weight: 700 !important;
            color: #1e3a8a !important;
            font-style: normal;
            background: #f8fafc;
            padding: 5px;
        }
        optgroup option {
            font-weight: 400 !important;
            color: #334155 !important;
            background: #ffffff;
            padding: 6px 12px;
        }
    </style>
</head>
<body>

<div class="mbs-new-box">
    <div class="mbs-logo-section">
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
            <textarea name="description" class="form-control" rows="4" placeholder="ระบุอาการชำรุดอย่างละเอียด เช่น หน้าจอดับ หรือแอร์ไม่เย็น" required></textarea>
        </div>

        <button type="submit" class="btn btn-submit-gradient">ส่งข้อมูลแจ้งซ่อม</button>
    </form>
</div>

<script>
document.getElementById('phoneInput').addEventListener('input', function (e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});

window.addEventListener('DOMContentLoaded', () => {
    const locationSelect = document.getElementById('locationSelect');
    
    fetch('get_rooms.php')
        .then(response => response.json())
        .then(data => {
            // ล้างตัวเลือกเก่าออกก่อนเพื่อป้องกันการโหลดซ้ำซ้อน คืนค่าหน้าตาสวยงาม
            locationSelect.innerHTML = '<option value="">-- กรุณาเลือก อาคาร และ ห้องเรียน --</option>';

            const groupACC = document.createElement('optgroup');
            groupACC.label = '🏢 อาคาร ACC.BIZ';
            
            const groupSBS = document.createElement('optgroup');
            groupSBS.label = '🏢 อาคาร SBS';

            data.forEach(room => {
                const option = document.createElement('option');
                option.value = `${room.building}|${room.room_number}|${room.room_type}`;
                
                // แสดงรูปแบบผลลัพธ์ตามบรีฟ: ชื่อห้อง (ประเภทห้อง) เช่น ACC.BIZ301 (Com Lab)
                option.textContent = `${room.room_number} (${room.room_type})`;

                if (room.building === 'ตึก ACC.BIZ') {
                    groupACC.appendChild(option);
                } else if (room.building === 'ตึก SBS') {
                    groupSBS.appendChild(option);
                }
            });

            locationSelect.appendChild(groupACC);
            locationSelect.appendChild(groupSBS);
        })
        .catch(err => console.error("โหลดข้อมูลห้องผิดพลาด:", err));
});

document.getElementById('building_room');
document.getElementById('locationSelect').addEventListener('change', function() {
    if(this.value) {
        const parts = this.value.split('|');
        document.getElementById('room_type').value = parts[2];
    }
});
</script>
</body>
</html>