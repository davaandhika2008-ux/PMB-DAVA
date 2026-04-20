<?php
session_start();
require_once '../config.php';

if (!isAdminLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Statistik
$total_pendaftar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$total_lulus = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE status_test = 'lulus'"))['total'];
$total_tidak_lulus = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE status_test = 'tidak_lulus'"))['total'];
$total_daftar_ulang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE status_daftar_ulang = 'sudah'"))['total'];
$total_soal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM soal_test"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PMB</title>
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
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            height: 60px;
            color: #495057;
        }

        .navbar h1 {
            font-size: 1.5rem;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
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

        .main-content {
            margin-left: 220px;
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            font-size: 3rem;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .stat-icon.blue {
            background: #e3f2fd;
        }

        .stat-icon.green {
            background: #e8f5e9;
        }

        .stat-icon.red {
            background: #ffebee;
        }

        .stat-icon.purple {
            background: #f3e5f5;
        }

        .stat-info h3 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 0.3rem;
        }

        .stat-info p {
            color: #666;
            font-size: 0.9rem;
        }

        .content-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .content-card h2 {
            color: #333;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background: #f8f9fa;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #333;
        }

        table td {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }

        table tr:hover {
            background: #f8f9fa;
        }

        .badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Admin Dashboard - PMB</h1>
    </div>

    <div class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="kelola-maba.php">Kelola Calon Maba</a></li>
            <li><a href="kelola-soal.php">Kelola Soal Test</a></li>
            
            <li><a href="list-kelulusan.php">List Kelulusan</a></li>
            <li><a href="list-daftar-ulang.php">List Daftar Ulang</a></li>
        </ul>
        <div class="sidebar-footer">
            <div class="user-info">
                <span>Halo, <?php echo $_SESSION['admin_name']; ?></span>
            </div>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <h2 style="margin-bottom: 2rem; color: #333;">Statistik Pendaftaran</h2>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">👥</div>
                <div class="stat-info">
                    <h3><?php echo $total_pendaftar; ?></h3>
                    <p>Total Pendaftar</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">✅</div>
                <div class="stat-info">
                    <h3><?php echo $total_lulus; ?></h3>
                    <p>Lulus Test</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon red">❌</div>
                <div class="stat-info">
                    <h3><?php echo $total_tidak_lulus; ?></h3>
                    <p>Tidak Lulus</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon purple">🎓</div>
                <div class="stat-info">
                    <h3><?php echo $total_daftar_ulang; ?></h3>
                    <p>Sudah Daftar Ulang</p>
                </div>
            </div>
        </div>

        <div class="content-card">
            <h2>Pendaftar Terbaru</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Nomor Test</th>
                        <th>Status Test</th>
                        <th>Tanggal Daftar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $recent_query = "SELECT * FROM users ORDER BY created_at DESC LIMIT 10";
                    $recent_result = mysqli_query($conn, $recent_query);
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($recent_result)):
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['nama_lengkap']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['nomor_test']; ?></td>
                        <td>
                            <?php
                            if ($row['status_test'] == 'belum') {
                                echo '<span class="badge badge-warning">Belum Test</span>';
                            } elseif ($row['status_test'] == 'lulus') {
                                echo '<span class="badge badge-success">Lulus</span>';
                            } else {
                                echo '<span class="badge badge-danger">Tidak Lulus</span>';
                            }
                            ?>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="content-card">
            <h2>Informasi Sistem</h2>
            <table>
                <tr>
                    <td><strong>Total Soal Test:</strong></td>
                    <td><?php echo $total_soal; ?> soal</td>
                </tr>
                <tr>
                    <td><strong>Passing Grade:</strong></td>
                    <td>70</td>
                </tr>
                <tr>
                    <td><strong>Status Sistem:</strong></td>
                    <td><span class="badge badge-success">Aktif</span></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
