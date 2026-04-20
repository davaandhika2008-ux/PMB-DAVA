<?php
session_start();
require_once '../config.php';

if (!isAdminLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$success = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete_query = "DELETE FROM users WHERE id = $id";
    if (mysqli_query($conn, $delete_query)) {
        $success = 'Data berhasil dihapus!';
    } else {
        $error = 'Gagal menghapus data!';
    }
}

// Handle Add/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $nama_lengkap = clean_input($_POST['nama_lengkap']);
    $email = clean_input($_POST['email']);
    $tanggal_lahir = clean_input($_POST['tanggal_lahir']);
    $jenis_kelamin = clean_input($_POST['jenis_kelamin']);
    $alamat = clean_input($_POST['alamat']);
    $no_telepon = clean_input($_POST['no_telepon']);
    
    if ($id) {
        // Update
        $update_query = "UPDATE users SET 
                        nama_lengkap = '$nama_lengkap',
                        email = '$email',
                        tanggal_lahir = '$tanggal_lahir',
                        jenis_kelamin = '$jenis_kelamin',
                        alamat = '$alamat',
                        no_telepon = '$no_telepon'
                        WHERE id = $id";
        if (mysqli_query($conn, $update_query)) {
            $success = 'Data berhasil diupdate!';
        } else {
            $error = 'Gagal update data!';
        }
    } else {
        // Add
        $password = clean_input($_POST['password']);
        $nomor_test = generateNomorTest();
        
        $insert_query = "INSERT INTO users (email, password, nama_lengkap, tanggal_lahir, jenis_kelamin, alamat, no_telepon, nomor_test) 
                       VALUES ('$email', MD5('$password'), '$nama_lengkap', '$tanggal_lahir', '$jenis_kelamin', '$alamat', '$no_telepon', '$nomor_test')";
        if (mysqli_query($conn, $insert_query)) {
            $success = 'Data berhasil ditambahkan! Nomor Test: ' . $nomor_test;
        } else {
            $error = 'Gagal menambahkan data: ' . mysqli_error($conn);
        }
    }
}

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_query = "SELECT * FROM users WHERE id = $id";
    $edit_result = mysqli_query($conn, $edit_query);
    $edit_data = mysqli_fetch_assoc($edit_result);
}

// Get all users
$users_query = "SELECT * FROM users ORDER BY created_at DESC";
$users_result = mysqli_query($conn, $users_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Calon Maba - Admin</title>
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

        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }

        .content-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .content-card h2 {
            margin-bottom: 1.5rem;
            color: #333;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-family: inherit;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
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
        }

        table td {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }

        table tr:hover {
            background: #f8f9fa;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
            margin-right: 0.5rem;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-warning {
            background: #ffc107;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Kelola Calon Mahasiswa Baru</h1>
        <a href="dashboard.php">← Dashboard</a>
    </div>

    <div class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="kelola-maba.php" class="active">Kelola Calon Maba</a></li>
            <li><a href="kelola-soal.php">Kelola Soal Test</a></li>
            <li><a href="list-kelulusan.php">List Kelulusan</a></li>
            <li><a href="list-daftar-ulang.php">List Daftar Ulang</a></li>
        </ul>
    </div>

    <div class="main-content">
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="content-card">
            <h2><?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Data Calon Mahasiswa</h2>
            <form method="POST" action="">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Nama Lengkap *</label>
                    <input type="text" name="nama_lengkap" value="<?php echo $edit_data ? $edit_data['nama_lengkap'] : ''; ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" value="<?php echo $edit_data ? $edit_data['email'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nomor Telepon *</label>
                        <input type="tel" name="no_telepon" value="<?php echo $edit_data ? $edit_data['no_telepon'] : ''; ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Lahir *</label>
                        <input type="date" name="tanggal_lahir" value="<?php echo $edit_data ? $edit_data['tanggal_lahir'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin *</label>
                        <select name="jenis_kelamin" required>
                            <option value="">Pilih</option>
                            <option value="L" <?php echo ($edit_data && $edit_data['jenis_kelamin'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="P" <?php echo ($edit_data && $edit_data['jenis_kelamin'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Alamat *</label>
                    <textarea name="alamat" required><?php echo $edit_data ? $edit_data['alamat'] : ''; ?></textarea>
                </div>

                <?php if (!$edit_data): ?>
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required>
                </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary">
                    <?php echo $edit_data ? 'Update Data' : 'Tambah Data'; ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="kelola-maba.php" class="btn btn-warning">Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="content-card">
            <h2>Daftar Calon Mahasiswa</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Nomor Test</th>
                        <th>Jenis Kelamin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($users_result)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['nama_lengkap']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['nomor_test']; ?></td>
                        <td><?php echo $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                        <td>
                            <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger" 
                               onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
