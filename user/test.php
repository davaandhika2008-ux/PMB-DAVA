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

if ($user['status_test'] != 'belum') {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$show_test = false;

// Cek apakah nomor test sudah diverifikasi
if (isset($_SESSION['nomor_test_verified']) && $_SESSION['nomor_test_verified'] === true) {
    $show_test = true;
}

// Proses verifikasi nomor test
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_nomor_test'])) {
    $input_nomor_test = clean_input($_POST['nomor_test']);
    
    if ($input_nomor_test === $user['nomor_test']) {
        $_SESSION['nomor_test_verified'] = true;
        $show_test = true;
    } else {
        $error = 'Nomor test tidak sesuai!';
    }
}

// Proses submit jawaban test
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_test'])) {
    $total_soal = 0;
    $benar = 0;
    
    // Ambil semua soal
    $soal_query = "SELECT * FROM soal_test ORDER BY id";
    $soal_result = mysqli_query($conn, $soal_query);
    
    while ($soal = mysqli_fetch_assoc($soal_result)) {
        $total_soal++;
        $jawaban_user = isset($_POST['jawaban_' . $soal['id']]) ? $_POST['jawaban_' . $soal['id']] : '';
        $is_benar = ($jawaban_user == $soal['jawaban_benar']) ? 1 : 0;
        
        if ($is_benar) $benar++;
        
        // Simpan hasil per soal
        $insert_hasil = "INSERT INTO hasil_test (user_id, soal_id, jawaban, benar) 
                        VALUES ($user_id, {$soal['id']}, '$jawaban_user', $is_benar)";
        mysqli_query($conn, $insert_hasil);
    }
    
    // Hitung nilai
    $nilai = ($benar / $total_soal) * 100;
    $status = ($nilai >= 70) ? 'lulus' : 'tidak_lulus';
    
    // Update status user
    $update_query = "UPDATE users SET status_test = '$status', nilai_test = $nilai WHERE id = $user_id";
    mysqli_query($conn, $update_query);
    
    // Reset session
    unset($_SESSION['nomor_test_verified']);
    
    header('Location: dashboard.php');
    exit();
}

// Ambil soal jika sudah terverifikasi
if ($show_test) {
    $soal_query = "SELECT * FROM soal_test ORDER BY id";
    $soal_result = mysqli_query($conn, $soal_query);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Seleksi - PMB</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .verify-card {
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
        }

        .verify-card h2 {
            color: #333;
            margin-bottom: 1rem;
        }

        .verify-card p {
            color: #666;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1.2rem;
            text-align: center;
            letter-spacing: 2px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .error-message {
            background: #fee;
            color: #c33;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .btn {
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

        .test-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .test-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px 15px 0 0;
            margin: -2rem -2rem 2rem -2rem;
            text-align: center;
        }

        .test-header h2 {
            margin-bottom: 0.5rem;
        }

        .soal-item {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .soal-number {
            background: #667eea;
            color: white;
            display: inline-block;
            width: 35px;
            height: 35px;
            line-height: 35px;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
            margin-right: 1rem;
        }

        .soal-pertanyaan {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .pilihan-jawaban {
            margin-left: 3rem;
        }

        .pilihan-item {
            padding: 0.8rem;
            margin-bottom: 0.5rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }

        .pilihan-item:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .pilihan-item input[type="radio"] {
            margin-right: 1rem;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .pilihan-item label {
            cursor: pointer;
            flex: 1;
        }

        .submit-section {
            text-align: center;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .timer {
            background: #ffeaa7;
            color: #d63031;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>📝 Test Seleksi Mahasiswa Baru</h1>
        <a href="dashboard.php">Kembali</a>
    </div>

    <div class="container">
        <?php if (!$show_test): ?>
            <div class="verify-card">
                <h2>🔐 Verifikasi Nomor Test</h2>
                <p>Silakan masukkan nomor test Anda untuk memulai ujian seleksi.<br>
                Nomor test dapat dilihat di dashboard Anda.</p>

                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <input type="text" name="nomor_test" placeholder="Masukkan Nomor Test" required>
                    </div>
                    <button type="submit" name="verify_nomor_test" class="btn">Verifikasi & Mulai Test</button>
                </form>
            </div>
        <?php else: ?>
            <div class="test-card">
                <div class="test-header">
                    <h2>Test Seleksi Calon Mahasiswa Baru</h2>
                    <p>Jawab semua pertanyaan dengan baik. Nilai minimal kelulusan: 70</p>
                </div>

                <div class="timer" id="timer">Waktu: 30:00</div>

                <form method="POST" action="" id="testForm">
                    <?php 
                    $no = 1;
                    while ($soal = mysqli_fetch_assoc($soal_result)): 
                    ?>
                        <div class="soal-item">
                            <div class="soal-pertanyaan">
                                <span class="soal-number"><?php echo $no; ?></span>
                                <?php echo $soal['pertanyaan']; ?>
                            </div>
                            <div class="pilihan-jawaban">
                                <div class="pilihan-item">
                                    <input type="radio" name="jawaban_<?php echo $soal['id']; ?>" 
                                           id="soal_<?php echo $soal['id']; ?>_a" value="A" required>
                                    <label for="soal_<?php echo $soal['id']; ?>_a">
                                        A. <?php echo $soal['pilihan_a']; ?>
                                    </label>
                                </div>
                                <div class="pilihan-item">
                                    <input type="radio" name="jawaban_<?php echo $soal['id']; ?>" 
                                           id="soal_<?php echo $soal['id']; ?>_b" value="B">
                                    <label for="soal_<?php echo $soal['id']; ?>_b">
                                        B. <?php echo $soal['pilihan_b']; ?>
                                    </label>
                                </div>
                                <div class="pilihan-item">
                                    <input type="radio" name="jawaban_<?php echo $soal['id']; ?>" 
                                           id="soal_<?php echo $soal['id']; ?>_c" value="C">
                                    <label for="soal_<?php echo $soal['id']; ?>_c">
                                        C. <?php echo $soal['pilihan_c']; ?>
                                    </label>
                                </div>
                                <div class="pilihan-item">
                                    <input type="radio" name="jawaban_<?php echo $soal['id']; ?>" 
                                           id="soal_<?php echo $soal['id']; ?>_d" value="D">
                                    <label for="soal_<?php echo $soal['id']; ?>_d">
                                        D. <?php echo $soal['pilihan_d']; ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php 
                    $no++;
                    endwhile; 
                    ?>

                    <div class="submit-section">
                        <p style="margin-bottom: 1rem; color: #666;">
                            Pastikan semua jawaban sudah terisi sebelum submit
                        </p>
                        <button type="submit" name="submit_test" class="btn" onclick="return confirm('Apakah Anda yakin ingin submit test? Pastikan semua jawaban sudah terisi!');">
                            Submit Test
                        </button>
                    </div>
                </form>
            </div>

            <script>
                // Timer 30 menit
                let timeLeft = 30 * 60; // 30 minutes in seconds
                
                function updateTimer() {
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    
                    document.getElementById('timer').textContent = 
                        `Waktu: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    
                    if (timeLeft <= 0) {
                        alert('Waktu habis! Test akan otomatis di-submit.');
                        document.getElementById('testForm').submit();
                    }
                    
                    timeLeft--;
                }
                
                setInterval(updateTimer, 1000);
            </script>
        <?php endif; ?>
    </div>
</body>
</html>
