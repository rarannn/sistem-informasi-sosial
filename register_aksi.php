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
$nip = $_POST['nip'];

if (!empty($username) && !empty($password) && !empty($nama) && !empty($email) && !empty($alamat) && !empty($tlp) && !empty($level)) {
    if ($level != 'warga' && $level != 'petugas') {
        die("<script>
            alert('Invalid level! it's either petugas/warga');
        </script>");
    }
    $sql = $koneksi->prepare("SELECT * FROM user WHERE username = ?");
    $sql->bind_param('s',$username);
    $sql->execute();
    $cek_login = mysqli_num_rows($sql->get_result());

    if ($cek_login > 0) {
        echo "<script>
            alert('username milik orang lain. Pakai username lain!');
            </script>"; 
        echo "<script>history.back();</script>";
    } else {
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
            } else { ?>
    <script>
        alert('Ulangi, Ada Input yang Kosong');
        history.back();
    </script>
<?php
            }
            $koneksi->close()
?>