<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    if ($role == 'admin') {
        $query = "SELECT * FROM admin WHERE username = '$username' AND password = MD5('$password')";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $admin = mysqli_fetch_assoc($result);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['nama'];
            header('Location: admin/dashboard.php');
            exit();
        } else {
            $error = 'Username atau password admin salah!';
        }
    } else {
        $query = "SELECT * FROM users WHERE email = '$username' AND password = MD5('$password')";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nama_lengkap'];
            header('Location: user/dashboard.php');
            exit();
        } else {
            $error = 'Email atau password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PMB Universitas</title>
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

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            display: flex;
        }

        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-left h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .login-left p {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        .login-right {
            flex: 1;
            padding: 3rem;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            color: #333;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #666;
        }

        .role-selector {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .role-option {
            flex: 1;
            position: relative;
        }

        .role-option input[type="radio"] {
            display: none;
        }

        .role-option label {
            display: block;
            padding: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.1rem;
        }

        .role-option input[type="radio"]:checked + label {
            border-color: #667eea;
            background: #f0f4ff;
            color: #667eea;
            font-weight: bold;
        }

        .role-option label:hover {
            border-color: #667eea;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .error-message {
            background: #fee;
            color: #c33;
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
        }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
        }

        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .back-home {
            text-align: center;
            margin-top: 1rem;
        }

        .back-home a {
            color: #667eea;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .login-left {
                padding: 2rem;
            }

            .login-left h1 {
                font-size: 1.8rem;
            }

            .role-selector {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <h1>Selamat Datang</h1>
            <p>Sistem Pendaftaran Mahasiswa Baru Online. Silakan login untuk melanjutkan proses pendaftaran atau mengelola data calon mahasiswa.</p>
        </div>
        <div class="login-right">
            <div class="login-header">
                <h2>Login</h2>
            </div>

            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="role-selector">
                    <div class="role-option">
                        <input type="radio" name="role" id="role-user" value="user" checked>
                        <label for="role-user"> Calon Mahasiswa</label>
                    </div>
                    <div class="role-option">
                        <input type="radio" name="role" id="role-admin" value="admin">
                        <label for="role-admin"> Admin</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">Email / Username</label>
                    <input type="text" id="username" name="username" placeholder="email anda" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" placeholder="password anda" name="password" required>
                </div>

                <button type="submit" class="btn-login">Masuk</button>

                <div class="register-link">
                    Belum punya akun? <a href="register.php">Daftar di sini</a>
                </div>

                <div class="back-home">
                    <a href="index.html">← Kembali ke Beranda</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const roleInputs = document.querySelectorAll('input[name="role"]');
        const usernameInput = document.getElementById('username');
        
        roleInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.value === 'admin') {
                    usernameInput.placeholder = 'Username admin';
                } else {
                    usernameInput.placeholder = 'email anda';
                }
            });
        });
    </script>
</body>
</html>
