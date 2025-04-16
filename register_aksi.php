<?php
session_start();
include "koneksi.php";

$username = htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8');
$password = $_POST['psw'];
$nama = htmlspecialchars($_POST['nama'] ?? '', ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8');
$alamat = htmlspecialchars($_POST['alamat'] ?? '', ENT_QUOTES, 'UTF-8');
$tlp = $_POST['tlp'];
$level = $_POST['level'];
$nip = htmlspecialchars($_POST['nip'] ?? '', ENT_QUOTES, 'UTF-8');

if (!empty($username) && !empty($password) && !empty($nama) && !empty($email) && !empty($alamat) && !empty($tlp) && !empty($level)) {
    if ($level != 'warga' && $level != 'petugas') {
        die("<script>
            alert('Invalid level! it's either petugas/warga');
        </script>");
    }
    $sql = $koneksi->prepare("SELECT * FROM user WHERE username = ? OR email = ?");
    $sql->bind_param('ss',$username, $email);
    $sql->execute();
    $cek = mysqli_num_rows($sql->get_result());

    if ($cek > 0) {
        echo "<script>
        alert('Email/Username sudah digunakan oleh user lain!');
            setTimeout(function() {
            window.location.href = '/home.php';
            }, 2000);
        </script>";
        die;
    }
    $newpsw = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $koneksi->prepare("INSERT INTO user (username, password, nama, email, alamat, tlp, level, nip) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $username, $newpsw, $nama, $email, $alamat, $tlp, $level, $nip);

    if ($stmt->execute()) { ?>
        <script>
            alert('Anda sukses registrasi');
            location.replace('home.php');
        </script><?php
                } else {
                    echo "<script>alert('Error memasukkan data!');</script>";
                    echo "<script>history.back();</script>";
                }
            }
        ?>
<script>
        alert('Ulangi, Ada Input yang Kosong');
        history.back();
    </script>