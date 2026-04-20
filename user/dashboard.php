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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Calon Mahasiswa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }

        .navbar {
            background: #e9ecef;
            color: #495057;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            height: 60px;
        }

        .navbar h1 {
            font-size: 1.5rem;
            text-align: center;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 60px;
            width: 220px;
            height: calc(100vh - 60px);
            background: #f8f9fa;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            overflow-y: auto;
            z-index: 999;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 1rem;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }

        .sidebar-header img {
            max-width: 100%;
            height: auto;
            max-height: 60px;
        }

        .sidebar-header span {
            display: block;
            margin-top: 0.5rem;
            font-weight: bold;
            color: #495057;
            font-size: 0.9rem;
        }

        .sidebar-menu {
            list-style: none;
            padding: 1rem 0;
            flex: 1;
        }

        .sidebar-menu li {
            margin: 0.25rem 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #495057;
            text-decoration: none;
            transition: all 0.3s;
            border-radius: 8px;
            margin: 0 0.5rem;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #e9ecef;
            color: #495057;
            border-radius: 8px;
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid #dee2e6;
        }

        .sidebar-footer .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            color: #495057;
        }

        .sidebar-footer a {
            color: #dc3545;
            text-decoration: none;
            padding: 0.5rem 1rem;
            background: rgba(220,53,69,0.1);
            border-radius: 5px;
            display: inline-block;
            transition: background 0.3s;
        }

        .sidebar-footer a:hover {
            background: rgba(220,53,69,0.2);
        }

        .container {
            max-width: 1200px;
            margin: 4rem auto 2rem 240px;
            padding: 0 3rem;
            padding-top: 2rem;
        }

        .welcome-card {
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .welcome-card h2 {
            color: #333;
            margin-bottom: 0.5rem;
        }

        .welcome-card p {
            color: #666;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .info-card h3 {
            color: #667eea;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-card .icon {
            font-size: 1.5rem;
        }

        .nomor-test {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        .status-belum {
            background: #ffeaa7;
            color: #d63031;
        }

        .status-lulus {
            background: #d4edda;
            color: #155724;
        }

        .status-tidak-lulus {
            background: #f8d7da;
            color: #721c24;
        }

        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .btn-disabled:hover {
            transform: none;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #eee;
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
        }
    </style>
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Nomor test berhasil disalin: ' + text);
            }, function(err) {
                console.error('Could not copy text: ', err);
                // Fallback for older browsers
                var textArea = document.createElement("textarea");
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    alert('Nomor test berhasil disalin: ' + text);
                } catch (err) {
                    alert('Gagal menyalin nomor test');
                }
                document.body.removeChild(textArea);
            });
        }
    </script>
</head>
<body>
    <div class="navbar">
        <h1>Dashboard Calon Mahasiswa</h1>
    </div>

    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../image/logo-unj.png" alt="Logo UNJ">
            <span>Universitas Negeri Jakarta</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
        </ul>
        <div class="sidebar-footer">
            <div class="user-info">
                <span><?php echo $user['nama_lengkap']; ?></span>
            </div>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome-card">
            <h2>Selamat Datang, <?php echo $user['nama_lengkap']; ?>!</h2>
            <p>Selamat datang di dashboard pendaftaran mahasiswa baru. Silakan ikuti tahapan pendaftaran di bawah ini.</p>
        </div>

        <div class="dashboard-grid">
            <div class="info-card">
                <h3><span class="icon"></span> Nomor Test Anda</h3>
                <div class="nomor-test">
                    <?php echo $user['nomor_test']; ?>
                </div>
                <p style="text-align: center; margin-top: 1rem; color: #666;">
                    Simpan nomor test ini untuk mengikuti ujian seleksi
                </p>
                <?php if (!empty($user['nomor_test'])): ?>
                <div style="text-align: center; margin-top: 1rem;">
                    <button class="btn" onclick="copyToClipboard('<?php echo $user['nomor_test']; ?>')">Salin Nomor Test</button>
                </div>
                <?php endif; ?>
            </div>

            <div class="info-card">
                <h3><span class="icon"></span> Status Pendaftaran</h3>
                <div class="info-row">
                    <span class="info-label">Status Test:</span>
                    <span class="info-value">
                        <?php
                        if ($user['status_test'] == 'belum') {
                            echo '<span class="status-badge status-belum">Belum Mengikuti Test</span>';
                        } elseif ($user['status_test'] == 'lulus') {
                            echo '<span class="status-badge status-lulus">Lulus</span>';
                        } else {
                            echo '<span class="status-badge status-tidak-lulus">Tidak Lulus</span>';
                        }
                        ?>
                    </span>
                </div>
                <?php if ($user['status_test'] != 'belum'): ?>
                <div class="info-row">
                    <span class="info-label">Nilai:</span>
                    <span class="info-value"><?php echo $user['nilai_test']; ?></span>
                </div>
                <?php endif; ?>
                <div class="info-row">
                    <span class="info-label">Daftar Ulang:</span>
                    <span class="info-value">
                        <?php echo $user['status_daftar_ulang'] == 'sudah' ? 'Sudah' : 'Belum'; ?>
                    </span>
                </div>
                <?php if ($user['nim']): ?>
                <div class="info-row">
                    <span class="info-label">NIM:</span>
                    <span class="info-value"><strong><?php echo $user['nim']; ?></strong></span>
                </div>
                <?php endif; ?>
            </div>

            <div class="info-card">
                <h3><span class="icon"></span>Data Pribadi</h3>
                <div class="info-row">
                    <span class="info-label">Nama:</span>
                    <span class="info-value"><?php echo $user['nama_lengkap']; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo $user['email']; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Telepon:</span>
                    <span class="info-value"><?php echo $user['no_telepon']; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Lahir:</span>
                    <span class="info-value"><?php echo date('d/m/Y', strtotime($user['tanggal_lahir'])); ?></span>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="info-card">
                <h3><span class="icon"></span> Ikuti Test</h3>
                <p style="margin-bottom: 1rem;">
                    <?php if ($user['status_test'] == 'belum'): ?>
                        Klik tombol di bawah untuk memulai test seleksi
                    <?php else: ?>
                        Anda sudah mengikuti test dengan nilai <?php echo $user['nilai_test']; ?>
                    <?php endif; ?>
                </p>
                <a href="test.php" class="btn <?php echo $user['status_test'] != 'belum' ? 'btn-disabled' : ''; ?>">
                    <?php echo $user['status_test'] == 'belum' ? 'Mulai Test' : 'Test Sudah Selesai'; ?>
                </a>
            </div>

            <div class="info-card">
                <h3><span class="icon"></span> Daftar Ulang</h3>
                <p style="margin-bottom: 1rem;">
                    <?php if ($user['status_test'] == 'lulus' && $user['status_daftar_ulang'] == 'belum'): ?>
                        Selamat! Anda lulus test. Silakan lakukan daftar ulang
                    <?php elseif ($user['status_daftar_ulang'] == 'sudah'): ?>
                        Anda sudah melakukan daftar ulang
                    <?php else: ?>
                        Daftar ulang tersedia setelah Anda lulus test
                    <?php endif; ?>
                </p>
                <a href="daftar-ulang.php" class="btn <?php echo ($user['status_test'] != 'lulus' || $user['status_daftar_ulang'] == 'sudah') ? 'btn-disabled' : ''; ?>">
                    <?php echo $user['status_daftar_ulang'] == 'sudah' ? 'Sudah Daftar Ulang' : 'Daftar Ulang'; ?>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
