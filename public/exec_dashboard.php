<?php
// public/exec_dashboard.php
include '../config.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'executive') {
    header("Location: login.php");
    exit();
}

// สรุปข้อมูลเชิงตัวเลข (KPIs)
$total = $conn->query("SELECT COUNT(*) as c FROM repair_requests")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) as c FROM repair_requests WHERE status='รอดำเนินการ'")->fetch_assoc()['c'];
$progress = $conn->query("SELECT COUNT(*) as c FROM repair_requests WHERE status='กำลังดำเนินการ'")->fetch_assoc()['c'];
$success = $conn->query("SELECT COUNT(*) as c FROM repair_requests WHERE status='ซ่อมเสร็จสิ้น'")->fetch_assoc()['c'];
$total_cost = $conn->query("SELECT SUM(repair_cost) as c FROM repair_requests")->fetch_assoc()['c'];

// ดึงสถิติตามประเภทอุปกรณ์เพื่อทำแผนภูมิวงกลม
$device_stats = $conn->query("SELECT device_type, COUNT(*) as count FROM repair_requests GROUP BY device_type");
$device_labels = []; $device_counts = [];
while($r = $device_stats->fetch_assoc()) {
    $device_labels[] = $r['device_type'];
    $device_counts[] = $r['count'];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Executive Dashboard - รายงานสถิติเชิงบริหาร คณะการบัญชีฯ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>body{ font-family: 'Sarabun', sans-serif; background-color: #f1f5f9; }</style>
</head>
<body>
<nav class="navbar navbar-dark bg-primary px-4">
    <span class="navbar-brand fw-bold">MBS Management Dashboard (ข้อมูลเชิงสถิติสำหรับผู้บริหาร)</span>
    <span class="text-white">ผู้บริหาร: <?php echo $_SESSION['fullname']; ?> | <a href="login.php" class="text-white-50 text-decoration-none">ออกจากระบบ</a></span>
</nav>

<div class="container-fluid py-4">
    <div class="row g-3 mb-4">
        <div class="col-md-2.5 col-xl-3">
            <div class="card bg-white border-0 shadow-sm p-3">
                <div class="text-muted small fw-bold">งานแจ้งซ่อมทั้งหมด</div>
                <h2 class="text-dark fw-bold mt-2"><?php echo $total; ?> งาน</h2>
            </div>
        </div>
        <div class="col-md-2.5 col-xl-2">
            <div class="card bg-warning text-dark border-0 shadow-sm p-3">
                <div class="small fw-bold">รอดำเนินการ</div>
                <h2 class="fw-bold mt-2"><?php echo $pending; ?></h2>
            </div>
        </div>
        <div class="col-md-2.5 col-xl-2">
            <div class="card bg-primary text-white border-0 shadow-sm p-3">
                <div class="small fw-bold">กำลังดำเนินการ</div>
                <h2 class="fw-bold mt-2"><?php echo $progress; ?></h2>
            </div>
        </div>
        <div class="col-md-2.5 col-xl-2">
            <div class="card bg-success text-white border-0 shadow-sm p-3">
                <div class="small fw-bold">ซ่อมเสร็จสิ้น</div>
                <h2 class="fw-bold mt-2"><?php echo $success; ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white border-0 shadow-sm p-3">
                <div class="small fw-bold">งบประมาณที่ใช้ไปรวม</div>
                <h2 class="fw-bold mt-2"><?php echo number_format($total_cost, 2); ?> ฿</h2>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 bg-white">
                <h5 class="fw-bold mb-3 text-secondary">สถิติการซ่อมแยกตามประเภทครุภัณฑ์</h5>
                <div style="max-height: 300px; display: flex; justify-content: center;">
                    <canvas id="deviceChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 bg-white">
                <h5 class="fw-bold mb-3 text-secondary">สรุปค่าใช้จ่ายการซ่อมแยกรายเดือน (ภาพรวมปี 2026)</h5>
                <div style="max-height: 300px;">
                    <canvas id="costChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// สถิติครุภัณฑ์เสียบ่อย (Pie Chart)
const ctxDevice = document.getElementById('deviceChart').getContext('2d');
new Chart(ctxDevice, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($device_labels); ?>,
        datasets: [{
            data: <?php echo json_encode($device_counts); ?>,
            backgroundColor: ['#38bdf8', '#fb923c', '#4ade80', '#f43f5e', '#a855f7', '#64748b']
        }]
    }
});

// กราฟสรุปค่าใช้จ่ายประจำเดือน (Bar Chart จำลองข้อมูลเบื้องต้น)
const ctxCost = document.getElementById('costChart').getContext('2d');
new Chart(ctxCost, {
    type: 'bar',
    data: {
        labels: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.'],
        datasets: [{
            label: 'ค่าใช้จ่ายรายเดือน (บาท)',
            data: [4500, 12000, 8900, 3400, 7500, 11000, <?php echo $total_cost; ?>],
            backgroundColor: '#1d4ed8'
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});
</script>
</body>
</html>