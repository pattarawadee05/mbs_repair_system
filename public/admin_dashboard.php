<?php
// public/admin_dashboard.php
include '../config.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// อัปเดตสถานะและข้อมูลการซ่อมโดยช่าง
if (isset($_POST['update_status'])) {
    $id = intval($_POST['request_id']);
    $status = $conn->real_escape_string($_POST['status']);
    $notes = $conn->real_escape_string($_POST['technician_notes']);
    $cost = floatval($_POST['repair_cost']);

    $sql = "UPDATE repair_requests SET status='$status', technician_notes='$notes', repair_cost=$cost WHERE id=$id";
    $conn->query($sql);
    echo "<script>alert('อัปเดตสถานะงานเรียบร้อย'); window.location.href='admin_dashboard.php';</script>";
}

$sql = "SELECT * FROM repair_requests ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ส่วนจัดการสำหรับเจ้าหน้าที่เทคนิค (Admin)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>body{ font-family: 'Sarabun', sans-serif; background-color: #f8fafc; }</style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">MBS Repair Center - เจ้าหน้าที่เทคนิค</span>
    <span class="text-white">สวัสดี, <?php echo $_SESSION['fullname']; ?> | <a href="login.php" class="text-warning text-decoration-none">ออกจากระบบ</a></span>
</nav>

<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 fw-bold text-primary">รายการแจ้งซ่อมและครุภัณฑ์ทั้งหมด</div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>ผู้แจ้ง / เบอร์โทร</th>
                        <th>สถานที่ / ห้อง</th>
                        <th>อุปกรณ์</th>
                        <th>อาการชำรุด</th>
                        <th>สถานะปัจจุบัน</th>
                        <th>การจัดการ / อัปเดต</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><strong><?php echo $row['reporter_name']; ?></strong><br><small class="text-muted"><?php echo $row['phone']; ?></small></td>
                        <td><?php echo $row['building']; ?><br><small class="badge bg-secondary"><?php echo $row['room_number']; ?> (<?php echo $row['room_type']; ?>)</small></td>
                        <td><span class="badge bg-info text-dark"><?php echo $row['device_type']; ?></span></td>
                        <td><?php echo $row['description']; ?></td>
                        <td>
                            <?php 
                            $badge = 'bg-warning';
                            if($row['status'] == 'กำลังดำเนินการ') $badge = 'bg-primary';
                            if($row['status'] == 'ซ่อมเสร็จสิ้น') $badge = 'bg-success';
                            ?>
                            <span class="badge <?php echo $badge; ?>"><?php echo $row['status']; ?></span>
                        </td>
                        <td>
                            <form action="" method="POST" class="row g-1">
                                <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                <div class="col-8">
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="รอดำเนินการ" <?php if($row['status']=='รอดำเนินการ') echo 'selected'; ?>>รอดำเนินการ</option>
                                        <option value="กำลังดำเนินการ" <?php if($row['status']=='กำลังดำเนินการ') echo 'selected'; ?>>กำลังดำเนินการ</option>
                                        <option value="ซ่อมเสร็จสิ้น" <?php if($row['status']=='ซ่อมเสร็จสิ้น') echo 'selected'; ?>>ซ่อมเสร็จสิ้น</option>
                                    </select>
                                    <input type="number" step="0.01" name="repair_cost" class="form-control form-control-sm mt-1" placeholder="ค่าใช้จ่าย (บาท)" value="<?php echo $row['repair_cost']; ?>">
                                    <input type="text" name="technician_notes" class="form-control form-control-sm mt-1" placeholder="บันทึกอาการซ่อม" value="<?php echo $row['technician_notes']; ?>">
                                </div>
                                <div class="col-4">
                                    <button type="submit" name="update_status" class="btn btn-sm btn-dark w-100 h-100">บันทึก</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>