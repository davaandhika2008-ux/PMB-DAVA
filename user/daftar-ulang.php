<?php
session_start();
require_once '../config.php';

if (!isUserLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Cek apakah user lulus test
if ($user['status_test'] != 'lulus') {
    header('Location: dashboard.php');
    exit();
}

// Cek apakah sudah daftar ulang
if ($user['status_daftar_ulang'] == 'sudah') {
    header('Location: dashboard.php');
    exit();
}

$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Generate NIM
    $nim = generateNIM();
    
    // Update status daftar ulang dan NIM
    $update_query = "UPDATE users SET status_daftar_ulang = 'sudah', nim = '$nim' WHERE id = $user_id";
    
    if (mysqli_query($conn, $update_query)) {
        $success = 'Daftar ulang berhasil! NIM Anda: ' . $nim;
        header("refresh:3;url=dashboard.php");
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Ulang - PMB</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .daftar-ulang-container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .content {
            padding: 2rem;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .congratulations {
            text-align: center;
            margin-bottom: 2rem;
        }

        .congratulations h2 {
            color: #00b894;
            margin-bottom: 1rem;
            font-size: 2rem;
        }

        .congratulations p {
            color: #666;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .info-box {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #dee2e6;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #666;
        }

        .info-value {
            color: #333;
            font-weight: 500;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-back {
            width: 100%;
            padding: 1rem;
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-back:hover {
            background: #667eea;
            color: white;
        }

        .check-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="daftar-ulang-container">
        <div class="header">
            <h1>🎓 Daftar Ulang</h1>
            <p>Konfirmasi Kelulusan & Pendaftaran</p>
        </div>
        <div class="content">
            <?php if ($success): ?>
                <div class="success-message">
                    <?php echo $success; ?><br>
                    <small>Anda akan dialihkan ke dashboard...</small>
                </div>
            <?php else: ?>
                <div class="congratulations">
                    <div class="check-icon">✅</div>
                    <h2>Selamat!</h2>
                    <p>Anda dinyatakan <strong>LULUS</strong> dalam test seleksi mahasiswa baru. Silakan lakukan daftar ulang untuk mendapatkan Nomor Induk Mahasiswa (NIM).</p>
                </div>

                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">Nama:</span>
                        <span class="info-value"><?php echo $user['nama_lengkap']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo $user['email']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nomor Test:</span>
                        <span class="info-value"><?php echo $user['nomor_test']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nilai Test:</span>
                        <span class="info-value"><strong><?php echo $user['nilai_test']; ?></strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value" style="color: #00b894;"><strong>LULUS</strong></span>
                    </div>
                </div>

                <form method="POST" action="">
                    <button type="submit" class="btn" onclick="return confirm('Apakah Anda yakin ingin melakukan daftar ulang?');">
                        Konfirmasi Daftar Ulang
                    </button>
                </form>

                <a href="dashboard.php" class="btn-back">Kembali ke Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
