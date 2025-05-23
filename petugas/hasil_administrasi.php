<?php
session_start();
include "../koneksi.php";
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
if (!isset($_SESSION['username'])) {
    die("Anda belum login, klik <a href=\"../index.php\">disini</a> untuk login");
} else {
    $username = $_SESSION['username'];
}

$pengId = $_POST['pengId'];
$tanggapan = htmlspecialchars($_POST['tanggapan'] ?? '', ENT_QUOTES, 'UTF-8');
$nama = htmlspecialchars($_POST['nama'] ?? '', ENT_QUOTES, 'UTF-8');
$petugas = htmlspecialchars($_SESSION['nama'] ?? '', ENT_QUOTES, 'UTF-8');
$file_name = htmlspecialchars($_FILES['file']['name'] ?? '', ENT_QUOTES, 'UTF-8');
$file_tmp = $_FILES['file']['tmp_name'];
$direktori = __DIR__ . "/../upload-file/administrasi/";
$linkberkas = $direktori . $file_name;
$allowed_ext = ['pdf'];

if (isset($_POST['simpan'])) {
    if (!empty($pengId) && !empty($nama) && !empty($tanggapan) && !empty($file_name)) {
        if ($_FILES['data']['size'] >= 50000000) {
            echo "<script>
              alert('Ukuran maksimal file yang boleh dikirim adalah 50MB');
              setTimeout(function() {
                window.location.href = 'aturan_layanan.php';
              }, 2000);
            </script>";
            exit;
        }
        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($extension !== 'pdf') {
            echo "Only allow PDF file!";
            die;
        }
        $query = "INSERT INTO hasil_administrasi (administrasiId, nama, deskripsi, file, tanggal, petugas) VALUES (?, ?, ?, ?, NOW(), ?)";
        $a = $koneksi->prepare($query);
        $a->bind_param("issss", $pengId, $nama, $tanggapan, $file_name, $petugas);

        if ($a->execute() === true) {
            move_uploaded_file($file_tmp, $linkberkas);
            echo "<script>alert('Berhasil Mengirim Hasil Administrasi!');</script>";
            header("refresh:2;url=hasil_administrasi.php");
            // echo "<script>
            // alert('Hapus Data dari administrasi Sukses!');
            // location='hasil_administrasi.php';
            // </script>";

        } else {
            echo "<script>alert('Gagal Mengirim Aturan!');</script>";
            // echo "<script>location('aturan_layanan.php?status=gagal');</script>";
            header("refresh:2;url=hasil_administrasi.php");
        }
    } else {
        echo "<script>alert('Ada Input yang Kosong!');</script>";
        echo "<script>history.back();</script>";
        // echo "<script>location('aturan_layanan.php?status=gagal');</script>";
    }
} else {
    // echo "<script>alert('Isi Form / Gagal Mengirim administrasi!');</script>";
    echo "<script>location('hasil_administrasi.php');</script>";
}

// tombol edit tabel
if (isset($_GET['hal'])) {
    if ($_GET['hal'] == "hapus") {

        $query = "DELETE FROM hasil_administrasi WHERE id=?";
        $sql = $koneksi->prepare($query);
        $sql->bind_param("i",$_GET['id']);
        if ($sql->execute()) {
            echo "<script>
            alert('Hapus Data Sukses!');
            location='hasil_administrasi.php';
            </script>";
        } else {
            echo "<script>alert('Gagal menghapus data');</script>";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HASIL LAYANAN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <style>
        h1 {
            margin-top: 80px;
            text-align: center;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        .text-start {
            font-weight: bold;
        }

        .aturan {
            width: 90%;
        }

        button {
            background-color: #2d2d44;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #fa736b;
        }

        .navbar-brand {
            font-weight: bold;
            color: #cdc2ae;
        }

        .hasil {
            width: 95%;
        }

        .form-hasil {
            width: 85%;
            text-align: left;
        }
    </style>
</head>

<body>
    <!-- navbar -->
    <nav class="navbar navbar-expand-lg navbar-light shadow-lg fixed-top" style="background-color: #68A7AD;">
        <div class="container-fluid">
            <a class="navbar-brand" href="../home.php">S I L A D U</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="../home.php">Home</a>
                    </li>
                    <li class="nav-item layanan">
                        <a class="nav-link" href="#" aria-current="page">Layanan</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="../petugas/aturan_layanan.php">
                                <?php if ($_SESSION['level'] == 'petugas') { ?>
                                    Input Aturan Layanan
                                <?php } else {?>
                                    Aturan Layanan
                                <?php }?>
                            </a></li>
                            <li><a class="dropdown-item" href="../petugas/layanan.php">Spesifikasi Layanan</a></li>
                        </ul>
                    </li>
                    <li class="nav-item kepengurusan">
                        <a class="nav-link" aria-current="page" href="../petugas/kepengurusan/staff.php">Kepengurusan</a>
                    </li>
                    <li class="nav-item about">
                        <a class="nav-link" href="../home.php#tentang">Tentang</a>
                    </li>
                    <li class="nav-item feedback">
                        <a class="nav-link" href="../pengguna/feedback.php">Feedback</a>
                    </li>
                </ul>
                
                <span class="navbar-profile">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php $username = $_SESSION['username'];
                                echo "$username"; ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </span>
            </div>
        </div>
    </nav>
    
    <h1 style="padding-top: 30px;"> Hasil Pelayanan Administrasi</h1>
    <div class="container-fluid hasil">
        <?php if ($_SESSION['level'] == 'petugas') { ?>
            <center>
                <div class="card-wrap mt-5 form-hasil">
                    <div class="card hasil-form">
                        <div class="card-header">
                            <h2 class="card-title text-center">INPUT TANGGAPAN ADMINISTRASI</h2>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="mb-3 row">
                                    <label class="col-sm-3 col-form-label text-start">ID Administrasi</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="pengId" placeholder="Harus Sesuai dengan Kode Administrasi">
                                        <label>*pastikan id sesuai dengan administrasi <a href="../pengguna/administrasi.php">disini</a></label>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-3 col-form-label text-start">Nama Pemohon</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="nama" placeholder="Harus Sesuai dengan Kode Administrasi">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-3 col-form-label text-start">Tanggapan</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" name="tanggapan"></textarea>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="formFileMultiple" class="col-sm-3 col-form-label text-start">File</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="file" name="file" id="formFileMultiple" accept="application/pdf">
                                    </div>
                                </div>
                                <div class="button-align text-end">
                                    <button type="submit" name="simpan" class="btn btn-success">SIMPAN</button>
                                    <button type="reset" name="reset" class="btn btn-danger">RESET</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </center>
        <?php } ?>

        <div class="card-wrap mt-4">
            <!-- <div class="card tabel-form"> -->
            <!-- <div class="card-header">
                    <h3 class="card-title text-center">Hasil Administrasi</h3>
                </div> -->
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tr class="text-center">
                        <th>No.</th>
                        <th>Kode Administrasi</th>
                        <th>Nama</th>
                        <th>Tanggapan</th>
                        <th>File</th>
                        <th>Tanggal</th>
                        <th>Petugas</th>
                        <?php if ($_SESSION['level'] == 'petugas') { ?>
                            <th>Aksi</th>
                        <?php } ?>
                    </tr>
                    <?php
                    $no = 1;
                    if ($_SESSION['level'] == "petugas") {
                        $a = mysqli_query($koneksi, "SELECT * FROM hasil_administrasi");
                    } elseif ($_SESSION['level'] == "warga") {
                        $a = mysqli_query($koneksi, "SELECT * FROM hasil_administrasi WHERE nama= '$_SESSION[nama]' ");
                    }
                    while ($tampil = mysqli_fetch_array($a)) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <!-- <td><?= $tampil['id'] ?></td> -->
                            <td><?= $tampil['administrasiId'] ?></td>
                            <td><?= $tampil['nama'] ?></td>
                            <td><?= $tampil['deskripsi'] ?></td>
                            <td><a href="downloadfile.php?admin=<?= $tampil['file']; ?>">Hasil</a></td>
                            <td><?= $tampil['tanggal'] ?></td>
                            <td><?= $tampil['petugas'] ?></td>
                            <?php if ($_SESSION['level'] == 'petugas') { ?>
                                <td class="text-center">
                                    <a href="hasil_administrasi.php?hal=hapus&id=<?= $tampil['id'] ?>" onclick="return confirm('Apakah yakin ingin menghapus data ini?')" name="hapus" class="btn btn-danger"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <!-- </div> -->
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>