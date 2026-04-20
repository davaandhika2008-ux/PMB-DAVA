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
    $delete_query = "DELETE FROM soal_test WHERE id = $id";
    if (mysqli_query($conn, $delete_query)) {
        $success = 'Soal berhasil dihapus!';
    } else {
        $error = 'Gagal menghapus soal!';
    }
}

// Handle Add/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $pertanyaan = clean_input($_POST['pertanyaan']);
    $pilihan_a = clean_input($_POST['pilihan_a']);
    $pilihan_b = clean_input($_POST['pilihan_b']);
    $pilihan_c = clean_input($_POST['pilihan_c']);
    $pilihan_d = clean_input($_POST['pilihan_d']);
    $jawaban_benar = clean_input($_POST['jawaban_benar']);
    
    if ($id) {
        // Update
        $update_query = "UPDATE soal_test SET 
                        pertanyaan = '$pertanyaan',
                        pilihan_a = '$pilihan_a',
                        pilihan_b = '$pilihan_b',
                        pilihan_c = '$pilihan_c',
                        pilihan_d = '$pilihan_d',
                        jawaban_benar = '$jawaban_benar'
                        WHERE id = $id";
        if (mysqli_query($conn, $update_query)) {
            $success = 'Soal berhasil diupdate!';
        } else {
            $error = 'Gagal update soal!';
        }
    } else {
        // Add
        $insert_query = "INSERT INTO soal_test (pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar) 
                       VALUES ('$pertanyaan', '$pilihan_a', '$pilihan_b', '$pilihan_c', '$pilihan_d', '$jawaban_benar')";
        if (mysqli_query($conn, $insert_query)) {
            $success = 'Soal berhasil ditambahkan!';
        } else {
            $error = 'Gagal menambahkan soal!';
        }
    }
}

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_query = "SELECT * FROM soal_test WHERE id = $id";
    $edit_result = mysqli_query($conn, $edit_query);
    $edit_data = mysqli_fetch_assoc($edit_result);
}

// Get all soal
$soal_query = "SELECT * FROM soal_test ORDER BY id";
$soal_result = mysqli_query($conn, $soal_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Soal Test - Admin</title>
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
            margin-left: 220px;
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

        .badge-answer {
            background: #28a745;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Kelola Soal Test</h1>
        <a href="dashboard.php">← Dashboard</a>
    </div>

    <div class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="kelola-maba.php">Kelola Calon Maba</a></li>
            <li><a href="kelola-soal.php" class="active">Kelola Soal Test</a></li>
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
            <h2><?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Soal Test</h2>
            <form method="POST" action="">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Pertanyaan *</label>
                    <textarea name="pertanyaan" required><?php echo $edit_data ? $edit_data['pertanyaan'] : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Pilihan A *</label>
                    <input type="text" name="pilihan_a" value="<?php echo $edit_data ? $edit_data['pilihan_a'] : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label>Pilihan B *</label>
                    <input type="text" name="pilihan_b" value="<?php echo $edit_data ? $edit_data['pilihan_b'] : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label>Pilihan C *</label>
                    <input type="text" name="pilihan_c" value="<?php echo $edit_data ? $edit_data['pilihan_c'] : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label>Pilihan D *</label>
                    <input type="text" name="pilihan_d" value="<?php echo $edit_data ? $edit_data['pilihan_d'] : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label>Jawaban Benar *</label>
                    <select name="jawaban_benar" required>
                        <option value="">Pilih Jawaban</option>
                        <option value="A" <?php echo ($edit_data && $edit_data['jawaban_benar'] == 'A') ? 'selected' : ''; ?>>A</option>
                        <option value="B" <?php echo ($edit_data && $edit_data['jawaban_benar'] == 'B') ? 'selected' : ''; ?>>B</option>
                        <option value="C" <?php echo ($edit_data && $edit_data['jawaban_benar'] == 'C') ? 'selected' : ''; ?>>C</option>
                        <option value="D" <?php echo ($edit_data && $edit_data['jawaban_benar'] == 'D') ? 'selected' : ''; ?>>D</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <?php echo $edit_data ? 'Update Soal' : 'Tambah Soal'; ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="kelola-soal.php" class="btn btn-warning">Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="content-card">
            <h2>Daftar Soal Test</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pertanyaan</th>
                        <th>Jawaban Benar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($soal_result)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['pertanyaan']; ?></td>
                        <td><span class="badge-answer"><?php echo $row['jawaban_benar']; ?></span></td>
                        <td>
                            <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger" 
                               onclick="return confirm('Yakin ingin menghapus soal ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
