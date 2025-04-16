<?php
error_reporting(0);
include 'koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $psw = $_POST['psw'];

    $sql = $koneksi->prepare("SELECT * FROM user WHERE username=?");
    $sql->bind_param('s', $username);
    $sql->execute();
    $result = $sql->get_result();
    $data = $result->fetch_assoc();
 
    if (password_verify($psw, $data['password']) != true && $username != $data['username']) {
        echo "<script>
            alert('Login gagal! Username atau password salah.');
            setTimeout(function() {
            window.location.href = '/';
            }, 2000);
        </script>";
      exit;
    }

    if ($data != NULL) {
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['level'] = $data['level'];
        
        echo "<script>
            alert('Login berhasil!');
            window.location.href='home.php';
        </script>";
    } else {
        echo "<script>
            alert('Login gagal! Username atau password salah.');
            window.location.href='index.php?alert=gagal';
        </script>";
    }
} else {
    if (isset($_GET['alert'])) {
        if ($_GET['alert'] == "gagal") {
            echo "<script>alert('Maaf! username & password salah');</script>";
        } elseif ($_GET['alert'] == "belum_login") {
            echo "<script>alert('Anda Harus Login terlebih dahulu!');</script>";
        } elseif ($_GET['alert'] == "logout") {
            echo "<script>alert('Anda telah logout!');</script>";
        }
    }
}
$koneksi->close();
?>