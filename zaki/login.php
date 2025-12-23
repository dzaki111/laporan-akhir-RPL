<?php
include 'koneksi.php';

if (isset($_SESSION['siswa_id'])) {
    header('Location: home.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_input = $koneksi->real_escape_string($_POST['nama']);
    
    // Login berdasarkan Nama Siswa
    $sql = "SELECT siswa_id, nama FROM Siswa WHERE nama = '$nama_input'";
    $result = $koneksi->query($sql);

    if ($result->num_rows == 1) {
        $siswa = $result->fetch_assoc();
        $_SESSION['siswa_id'] = $siswa['siswa_id'];
        $_SESSION['nama'] = $siswa['nama'];
        header('Location: home.php');
        exit();
    } else {
        $error = "Nama siswa tidak ditemukan. Silakan coba lagi.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Siswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { 
            font-family: sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            background-color: #f4f4f4; 
            margin: 0; 
        }
        .login-container { 
            background: white; 
            padding: 50px 40px; 
            border-radius: 20px; 
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1); 
            width: 600px; /* Lebar Besar */
            text-align: center; 
        }
        .icon-user { 
            font-size: 100px; 
            color: #aaa; 
            background-color: #f0f0f0; 
            border-radius: 50%; 
            width: 140px; 
            height: 140px; 
            line-height: 140px; 
            margin: 0 auto 40px; 
        }
        .input-group input { 
            width: 100%; 
            padding: 15px 12px; 
            margin-bottom: 25px; 
            border: 1px solid #ccc; 
            border-radius: 10px; 
            box-sizing: border-box; 
            font-size: 18px; 
        }
        .btn-login { 
            background-color: #4CAF50; 
            color: white; 
            padding: 18px; 
            border: none; 
            border-radius: 10px; 
            cursor: pointer; 
            width: 100%; 
            font-size: 20px; 
            margin-top: 10px; 
        }
        .btn-login:hover { background-color: #45a049; }
        .forgot-password { font-size: 14px; color: #888; margin-bottom: 20px; display: block; }
        .error { color: red; margin-bottom: 15px; font-size: 16px; }
    </style>
</head>
<body>

<div class="login-container">
    <div class="icon-user">👤</div>
    
    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <form method="POST" action="login.php">
        <div class="input-group">
            <input type="text" name="nama" placeholder="Username (Nama: Rizky/Dewi)" required>
            <input type="text" name="kelas" placeholder="Password (Kosongkan)" value="">
        </div>

        <span class="forgot-password">Forgot Password?</span>
        
        <button type="submit" name="login" class="btn-login">Login</button>
    </form>
</div>

</body>
</html>