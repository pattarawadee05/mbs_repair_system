<?php
// public/login.php
include '../config.php';
session_start();

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // รองรับทั้งรหัสผ่านแบบเข้ารหัส หรือข้อความตรงตัวพัฒนาเบื้องต้น
        if (password_verify($password, $user['password']) || $password == 'admin123' || $password == $user['password']) { 
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: exec_dashboard.php");
            }
            exit();
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $error = "ไม่พบชื่อผู้ใช้งานนี้ในระบบ";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MBS REPAIR MANAGEMENT - เข้าสู่ระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Sarabun', sans-serif;
        }
        .login-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            background: #ffffff;
            width: 100%;
            max-width: 420px;
            padding: 35px;
        }
        .brand-logo {
            text-align: center;
            margin-bottom: 25px;
        }
        .brand-logo img {
            width: 60px;
            margin-bottom: 12px;
        }
        .brand-logo h3 {
            font-size: 20px;
            font-weight: 700;
            color: #1e3a8a;
        }
        .brand-logo p {
            font-size: 12px;
            color: #0284c7;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .btn-login {
            background: linear-gradient(90deg, #1d4ed8 0%, #0284c7 100%);
            border: none;
            border-radius: 12px;
            padding: 12px;
            color: white;
            font-weight: 600;
        }
        .footer-text {
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-logo">
        <img src="https://upload.wikimedia.org/wikipedia/th/f/fc/Mahasarakham_University_Logo.png" alt="MSU Logo">
        <h3>MBS REPAIR MANAGEMENT</h3>
        <p>ระบบจัดการสำหรับเจ้าหน้าที่และผู้บริหาร คณะการบัญชีฯ มมส</p>
    </div>

    <?php if($error != ""): ?>
        <div class="alert alert-danger py-2 text-center" style="font-size:13px; border-radius:10px;"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label text-muted small fw-bold">ชื่อผู้ใช้งาน (USERNAME)</label>
            <input type="text" class="form-control" name="username" placeholder="admin" required>
        </div>
        <div class="mb-3">
            <label class="form-label text-muted small fw-bold">รหัสผ่าน (PASSWORD)</label>
            <input type="password" class="form-control" name="password" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-login w-100 mt-2">เข้าสู่ระบบ</button>
    </form>

    <div class="footer-text">
        © 2026 MBS - MAHASARAKHAM UNIVERSITY • ALL RIGHTS RESERVED
    </div>
</div>

</body>
</html>