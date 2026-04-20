<?php
session_start();
require_once '../config.php';

if (!isAdminLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$daftar_ulang_query = "SELECT * FROM users WHERE status_daftar_ulang = 'sudah' ORDER BY nim";
$daftar_ulang_result = mysqli_query($conn, $daftar_ulang_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Daftar Ulang - Admin</title>
<style>
    /* Reset & Base Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        background: #f5f7fa;
        color: #333;
    }

    /* Navbar */
    .navbar {
        background: #e9ecef;
        color: white;
        padding: 0 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 60px;
        z-index: 1000;
        color: #495057;
    }

    .navbar h1 {
        font-size: 1.25rem;
        font-weight: 600;
    }

    .navbar a {
        color: white;
        text-decoration: none;
        padding: 0.5rem 1.2rem;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 6px;
        font-size: 0.9rem;
        transition: background 0.3s;
    }

    .navbar a:hover {
        background: rgba(255, 255, 255, 0.25);
    }

    /* Sidebar */
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
        }

        .sidebar-menu {
            list-style: none;
            padding: 1rem 0;
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

    /* Main Content Area */
    .main-content {
        margin-left: 240px;
        margin-top: 60px;
        padding: 2rem;
        min-height: calc(100vh - 60px);
    }

    .content-card {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }

    .content-card h2 {
        margin-bottom: 1.5rem;
        font-size: 1.5rem;
        color: #2d3748;
    }

    /* Table Styles */
    .table-responsive {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    table th {
        background: #f8f9fa;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #718096;
        border-bottom: 2px solid #edf2f7;
    }

    table td {
        padding: 1rem;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }

    table tr:hover {
        background: #fafafa;
    }

    /* Components */
    .badge-success {
        background: #d4edda;
        color: #155724;
        padding: 0.35rem 0.8rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }
</style>
</head>
<body>
    <div class="navbar">
        <h1>List User yang Melakukan Daftar Ulang</h1>
        <a href="dashboard.php">← Dashboard</a>
    </div>

    <div class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="kelola-maba.php">Kelola Calon Maba</a></li>
            <li><a href="kelola-soal.php">Kelola Soal Test</a></li>
            <li><a href="list-kelulusan.php">List Kelulusan</a></li>
            <li><a href="list-daftar-ulang.php" class="active">List Daftar Ulang</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="content-card">
            <h2>Mahasiswa Baru (Sudah Daftar Ulang)</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>No Telepon</th>
                        <th>Nilai Test</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($daftar_ulang_result)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><strong><?php echo $row['nim']; ?></strong></td>
                        <td><?php echo $row['nama_lengkap']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['no_telepon']; ?></td>
                        <td><?php echo $row['nilai_test']; ?></td>
                        <td><span class="badge-success">Aktif</span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
