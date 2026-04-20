<?php
session_start();
require_once '../config.php';

if (!isAdminLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$lulus_query = "SELECT * FROM users WHERE status_test = 'lulus' ORDER BY nilai_test DESC";
$lulus_result = mysqli_query($conn, $lulus_query);

$tidak_lulus_query = "SELECT * FROM users WHERE status_test = 'tidak_lulus' ORDER BY nilai_test DESC";
$tidak_lulus_result = mysqli_query($conn, $tidak_lulus_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Kelulusan - Admin</title>
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa;}
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
        .sidebar {position: fixed; left: 0; top: 60px; width: 220px; height: calc(100vh - 60px); background: #f8f9fa; box-shadow: 2px 0 10px rgba(0,0,0,0.1); overflow-y: auto;}
        .sidebar-menu {list-style: none; padding: 1rem 0;}
        .sidebar-menu li {margin: 0.25rem 0;}
        .sidebar-menu a {display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; color: #495057; text-decoration: none; transition: all 0.3s; border-radius: 8px; margin: 0 0.5rem;}
        .sidebar-menu a:hover, .sidebar-menu a.active {background: #e9ecef; color: #495057; border-radius: 8px;}
        .main-content {margin-left: 220px; padding: 2rem;}
        .content-card {background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 3px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;}
        .content-card h2 {margin-bottom: 1.5rem; color: #333;}
        table {width: 100%; border-collapse: collapse;}
        table th {background: #f8f9fa; padding: 1rem; text-align: left; font-weight: 600;}
        table td {padding: 1rem; border-bottom: 1px solid #f0f0f0;}
        table tr:hover {background: #f8f9fa;}
        .badge {display: inline-block; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;}
        .badge-success {background: #d4edda; color: #155724;}
        .badge-danger {background: #f8d7da; color: #721c24;}
    </style>
</head>
<body>
    <div class="navbar">
        <h1>List Hasil Kelulusan Test</h1>
        <a href="dashboard.php">← Dashboard</a>
    </div>

    <div class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="kelola-maba.php">Kelola Calon Maba</a></li>
            <li><a href="kelola-soal.php">Kelola Soal Test</a></li>
            <li><a href="list-kelulusan.php" class="active">List Kelulusan</a></li>
            <li><a href="list-daftar-ulang.php">List Daftar Ulang</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="content-card">
            <h2>User Yang Lulus Test</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Nomor Test</th>
                        <th>Nilai</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($lulus_result)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['nama_lengkap']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['nomor_test']; ?></td>
                        <td><strong><?php echo $row['nilai_test']; ?></strong></td>
                        <td><span class="badge badge-success">LULUS</span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="content-card">
            <h2>User Yang Tidak Lulus Test</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Nomor Test</th>
                        <th>Nilai</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($tidak_lulus_result)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['nama_lengkap']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['nomor_test']; ?></td>
                        <td><strong><?php echo $row['nilai_test']; ?></strong></td>
                        <td><span class="badge badge-danger">TIDAK LULUS</span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
