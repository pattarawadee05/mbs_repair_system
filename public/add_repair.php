<?php
// public/add_repair.php
include '../config.php';

$is_success = false;
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'repair') {
    $reporter_name = isset($_POST['reporter_name']) ? $conn->real_escape_string($_POST['reporter_name']) : '';
    $reporter_role = isset($_POST['reporter_role']) ? $conn->real_escape_string($_POST['reporter_role']) : '';
    $phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : '';
    
    if (!empty($_POST['building_room'])) {
        $location_data = explode('|', $_POST['building_room']);
        $building = isset($location_data[0]) ? $conn->real_escape_string($location_data[0]) : '';
        $room_number = isset($location_data[1]) ? $conn->real_escape_string($location_data[1]) : '';
        $room_type = isset($location_data[2]) ? $conn->real_escape_string($location_data[2]) : '';
    } else {
        $building = "";
        $room_number = "";
        $room_type = "";
    }
    
    $device_type = isset($_POST['device_type']) ? $conn->real_escape_string($_POST['device_type']) : '';
    $description = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';
    $line_user_id = "LINE_MBS_" . uniqid(); 
    
    $department = "";
    $file_name = null;

    $sql = "INSERT INTO repair_requests (line_user_id, reporter_name, reporter_role, department, phone, building, room_type, room_number, device_type, description, image_before, status) 
            VALUES ('$line_user_id', '$reporter_name', '$reporter_role', '$department', '$phone', '$building', '$room_type', '$room_number', '$device_type', '$description', '$file_name', 'รอดำเนินการ')";
    
    if ($conn->query($sql) === TRUE) {
        $is_success = true;
    } else {
        $error_message = $conn->error;
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
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e0f2fe !important;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.6) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(244, 243, 255, 0.7) 0%, transparent 50%),
                linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 40%, #e0e7ff 100%) !important;
            min-height: 100vh;
            font-family: 'Sarabun', sans-serif;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
            margin: 0;
        }
        .mbs-new-box {
            width: 100%;
            max-width: 460px;
            background: rgba(255, 255, 255, 0.96) !important;
            border-radius: 30px !important;
            box-shadow: 0 20px 50px rgba(14, 165, 233, 0.08) !important;
            padding: 40px 30px !important;
            border: 1px solid rgba(255, 255, 255, 0.7);
        }
        .mbs-logo-section {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 20px;
        }
        .mbs-logo-section img {
            width: 90px;
            height: 90px;
            object-fit: contain;
            margin-bottom: 15px;
        }
        .mbs-gradient-title {
            font-size: 15px;
            font-weight: 700;
            line-height: 1.6;
            margin: 0 auto;
            text-align: center;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 50%, #075985 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: block;
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
        optgroup {
            font-weight: 700 !important;
            color: #0b2545 !important;
            font-style: normal;
            background: #f1f5f9;
            padding: 6px 8px !important;
        }
        optgroup option {
            font-weight: 400 !important;
            color: #334155 !important;
            background: #ffffff;
            padding: 6px 10px !important;
        }

        /* ✨ ดีไซน์เปลี่ยนโฉมใหม่หมดจดหลังส่งฟอร์มสำเร็จ (Full-Screen Success Mode) */
        .success-container {
            text-align: center;
            padding: 20px 10px;
        }
        .success-icon-wrapper {
            width: 100px;
            height: 100px;
            background: #f0fdf4;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px auto;
            border: 4px solid #bbf7d0;
            box-shadow: 0 10px 25px rgba(34, 197, 94, 0.15);
        }
        .success-icon-wrapper svg {
            width: 50px;
            height: 50px;
            color: #16a34a;
        }
        .success-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
        }
        .success-text {
            font-size: 15px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .btn-success-home {
            background: linear-gradient(90deg, #0284c7 0%, #0369a1 100%) !important;
            border: none;
            border-radius: 15px;
            padding: 14px 30px;
            font-size: 15px;
            font-weight: 600;
            color: white;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
            text-decoration: none;
            display: inline-block;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="mbs-new-box" id="mainBox">
    <?php if ($is_success): ?>
        <div class="success-container">
            <div class="success-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </div>
            <h2 class="success-title">ส่งข้อมูลสำเร็จ!</h2>
            <p class="success-text">
                ระบบได้ส่งข้อมูลแจ้งซ่อมเข้าฐานข้อมูลเรียบร้อยแล้ว<br>
                เจ้าหน้าที่จะเร่งดำเนินการตรวจสอบอุปกรณ์ให้โดยเร็วที่สุดค่ะ
            </p>
            <a href="add_repair.php" class="btn-success-home">กลับหน้าหลักเพื่อแจ้งซ่อมเพิ่ม</a>
        </div>
    <?php else: ?>
        <div class="mbs-logo-section" id="topSection">
            <img src="mbs_logo.png" alt="MBS Logo">
            <h1 class="mbs-gradient-title">ระบบแจ้งซ่อมและติดตามอุปกรณ์เพื่อเพิ่มประสิทธิภาพการบริการและการรายงานสถิติเชิงบริหาร คณะการบัญชีและการจัดการ มหาวิทยาลัยมหาสารคาม</h1>
        </div>

        <form id="repairForm" action="add_repair.php" method="POST">
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
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('phoneInput').addEventListener('input', function (e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// ส่วนงานดึงข้อมูลจาก get_rooms.php มาร้อยเข้า Dropdown แยกอาคาร
window.addEventListener('DOMContentLoaded', () => {
    const locationSelect = document.getElementById('locationSelect');
    if (!locationSelect) return;
    
    fetch('get_rooms.php')
        .then(response => response.json())
        .then(data => {
            locationSelect.innerHTML = '<option value="">-- กรุณาเลือก อาคาร และ ห้องเรียน --</option>';

            const groupACC = document.createElement('optgroup');
            groupACC.label = '🏢 อาคาร ACC.BIZ';
            
            const groupSBS = document.createElement('optgroup');
            groupSBS.label = '🏢 อาคาร SBS';

            data.forEach(room => {
                const option = document.createElement('option');
                option.value = room.building + '|' + room.room_number + '|' + room.room_type;
                option.textContent = room.room_number + ' (' + room.room_type + ')';

                // ดักจับเงื่อนไขข้อความให้ยืดหยุ่น ป้องกัน Dropdown โบ๋
                if (room.building.includes('ACC.BIZ') || room.building.includes('ACC') || room.building.includes('acc')) {
                    groupACC.appendChild(option);
                } else if (room.building.includes('SBS') || room.building.includes('sbs')) {
                    groupSBS.appendChild(option);
                } else {
                    groupACC.appendChild(option);
                }
            });

            locationSelect.appendChild(groupACC);
            locationSelect.appendChild(groupSBS);
        })
        .catch(err => console.error("โหลดข้อมูลห้องผิดพลาด:", err));
});

if (document.getElementById('locationSelect')) {
    document.getElementById('locationSelect').addEventListener('change', function() {
        if(this.value) {
            const parts = this.value.split('|');
            document.getElementById('room_type').value = parts[2];
        }
    });
}

// แสดงข้อผิดพลาดด้วย SweetAlert2 โทนสีแดงเฉพาะตอนข้อมูล SQL มีปัญหาเท่านั้น
<?php if (!empty($error_message)): ?>
    Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาด!',
        text: '<?php echo $error_message; ?>',
        confirmButtonColor: '#e11d48',
        confirmButtonText: 'ปิด'
    });
<?php endif; ?>
</script>
</body>
</html>