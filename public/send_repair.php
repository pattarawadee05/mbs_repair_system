<?php
// public/send_repair.php
include '../config.php';

$error_message = "";

// ส่วนที่ 1: ตรวจสอบและบันทึกข้อมูลเข้าฐานข้อมูล phpMyAdmin เมื่อมีการกดส่งฟอร์ม (POST)
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

    // ทำการ INSERT ลงตาราง repair_requests
    $sql = "INSERT INTO repair_requests (line_user_id, reporter_name, reporter_role, department, phone, building, room_type, room_number, device_type, description, image_before, status) 
            VALUES ('$line_user_id', '$reporter_name', '$reporter_role', '$department', '$phone', '$building', '$room_type', '$room_number', '$device_type', '$description', '$file_name', 'รอดำเนินการ')";
    
    if ($conn->query($sql) === TRUE) {
        // [🔥 จุดแก้ไขสำคัญ] บันทึกข้อมูลสำเร็จ สั่ง Redirect หนีทันทีเพื่อล้างแคชเบราว์เซอร์ ป้องกันปัญหารีเฟรชแล้วเด้งหน้าต่างซ้ำซาก
        header("Location: send_repair.php?success=1");
        exit();
    } else {
        $error_message = urlencode($conn->error);
        header("Location: send_repair.php?error=" . $error_message);
        exit();
    }
}

// เช็คค่าพารามิเตอร์ success จาก URL หลังจากถูกย้ายหน้ามาแล้ว (GET)
$is_success = (isset($_GET['success']) && $_GET['success'] == 1) ? true : false;
$db_error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : "";
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งซ่อมออนไลน์ - MBS REPAIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            /* 💎 พื้นหลังสีฟ้ามุกวิ้งๆ ละมุนตา สไตล์ Pearl Shimmer */
            background-color: #e0f2fe !important;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.6) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(244, 243, 255, 0.7) 0%, transparent 50%),
                linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 40%, #e0e7ff 100%) !important;
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
            color: #0284c7;
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
            cursor: pointer;
        }
        optgroup {
            font-weight: 700 !important;
            color: #0b2545 !important;
            background: #f1f5f9;
        }
        optgroup option {
            font-weight: 400 !important;
            color: #334155 !important;
            background: #ffffff;
        }
        
        /* 🌟 โฉมใหม่หลังส่งข้อมูลสำเร็จเต็มจอ สวยเฉียบหรูหรา */
        .success-display {
            text-align: center;
            padding: 30px 10px;
        }
        .success-circle {
            width: 90px;
            height: 90px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 50%;
            font-size: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px auto;
            box-shadow: 0 10px 20px rgba(46, 125, 50, 0.15);
            border: 3px solid #a5d6a7;
        }
        .success-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
        }
        .success-desc {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .success-meta-info {
            background: #f8fafc;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 30px;
            font-size: 13px;
            text-align: left;
            border: 1px solid #e2e8f0;
        }
        .meta-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .meta-item:last-child {
            margin-bottom: 0;
        }
        .meta-label { color: #64748b; font-weight: 500; }
        .meta-value { color: #0f172a; font-weight: 600; }
        
        .btn-back-main {
            background: linear-gradient(90deg, #0284c7 0%, #0369a1 100%);
            color: white;
            text-decoration: none;
            padding: 14px;
            border-radius: 15px;
            font-size: 15px;
            font-weight: 600;
            display: block;
            text-align: center;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
        }
    </style>
</head>
<body>

<div class="mbs-new-box">
    <?php if ($is_success): ?>
        <!-- 🌟 หน้าจอโฉมใหม่หลังจากส่งข้อมูลซ่อมเข้า phpMyAdmin สำเร็จ (รีเฟรชหน้าเท่าไหร่ฟอร์มก็ไม่เด้งซ้ำ) -->
        <div class="success-display">
            <div class="success-circle">✓</div>
            <h2 class="success-title">ส่งข้อมูลแจ้งซ่อมสำเร็จ!</h2>
            <p class="success-desc">
                ระบบได้นำส่งข้อมูลเข้าฐานข้อมูลหลักเรียบร้อยแล้ว<br>
                พร้อมเปิดสิทธิ์การเข้าถึงข้อมูลให้ฝั่ง Admin และผู้บริหารแล้วค่ะ
            </p>
            <div class="success-meta-info">
                <div class="meta-item">
                    <span class="meta-label">สถานะข้อมูล:</span>
                    <span class="meta-value" style="color: #0284c7;">รอดำเนินการ (Pending)</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">การนำส่งปลายทาง:</span>
                    <span class="meta-value" style="color: #16a34a;">phpMyAdmin, Admin, Executive</span>
                </div>
            </div>
            <a href="send_repair.php" class="btn-back-main">กลับสู่หน้าหลักเพื่อแจ้งซ่อมเพิ่ม</a>
        </div>
    <?php else: ?>
        <!-- หน้าจอแบบฟอร์มกรอกข้อมูลตามปกติ -->
        <div class="mbs-logo-section">
            <img src="mbs_logo.png" alt="MBS Logo">
            <h1 class="mbs-gradient-title">ระบบแจ้งซ่อมและติดตามอุปกรณ์เพื่อเพิ่มประสิทธิภาพการบริการและการรายงานสถิติเชิงบริหาร คณะการบัญชีและการจัดการ มหาวิทยาลัยมหาสารคาม</h1>
        </div>

        <form action="send_repair.php" method="POST" id="repairForm">
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
                    <option value="">-- กำลังโหลดข้อมูลอาคารและห้องเรียน... --</option>
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

window.addEventListener('DOMContentLoaded', () => {
    const locationSelect = document.getElementById('locationSelect');
    if(!locationSelect) return;

    fetch('get_rooms.php')
        .then(res => res.json())
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

                const b = room.building.toString().toUpperCase();
                if (b.includes('ACC') || b.includes('ตึก ACC') || b.includes('อาคาร ACC') || b.includes('การบัญชี')) {
                    groupACC.appendChild(option);
                } else {
                    groupSBS.appendChild(option);
                }
            });
            locationSelect.appendChild(groupACC);
            locationSelect.appendChild(groupSBS);
        })
        .catch(err => {
            console.error(err);
            locationSelect.innerHTML = '<option value="">เกิดข้อผิดพลาดในการโหลดห้องเรียน</option>';
        });
});

if (document.getElementById('locationSelect')) {
    document.getElementById('locationSelect').addEventListener('change', function() {
        if(this.value) {
            document.getElementById('room_type').value = this.value.split('|')[2];
        }
    });
}

// หากเกิดข้อผิดพลาดฝั่ง Database แสดงผลให้ผู้ใช้งานเห็นผ่าน SweetAlert2
<?php if (!empty($db_error)): ?>
    Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาดในการบันทึก!',
        text: '<?php echo $db_error; ?>',
        confirmButtonColor: '#e11d48',
        confirmButtonText: 'ปิด'
    });
<?php endif; ?>
</script>
</body>
</html>