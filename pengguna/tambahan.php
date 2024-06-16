<?php
session_start();
include "../koneksi.php";
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

if (!isset($_SESSION['username'])) {
    die("Anda belum login, klik <a href=\"../index.php\">disini</a> untuk login");
} else {
    $username = $_SESSION['username'];
}

// Initialize variables
$kode = $_GET['id'];
$jenis = $_POST['jenis'];
$request = $_POST['request'];
$alasan = $_POST['alasan'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['simpan'])) {
        $jenis = $_POST['jenis'];
        $request = $_POST['request'];
        $alasan = $_POST['alasan'];

        if (!empty($jenis) && !empty($request) && !empty($alasan)) {
            if ($_GET['hal'] == "edit") {
                $query = "UPDATE request SET
                    jenis = '$jenis',
                    request = '$request',
                    alasan = '$alasan'
                    WHERE request.id = '$kode'";
                $edit = mysqli_query($koneksi, $query) or die("Error in query: $query");

                if ($edit) {
                    echo "<script>alert('Berhasil Memperbarui Permintaan!');</script>";
                    echo "<script>window.location.href='tambahan.php';</script>";
                } else {
                    echo "<script>alert('Edit Data Gagal!');</script>";
                    echo "<script>window.location.href='tambahan.php';</script>";
                }
            } else {
                $sql = "INSERT INTO request (userId, nama, jenis, request, alasan)
                    VALUES ('$username', '" . $_SESSION['nama'] . "', '$jenis', '$request', '$alasan')";
                $result = $koneksi->query($sql);

                if ($result === true) {
                    echo "<script>alert('Berhasil Mengirim Permintaan!');</script>";
                    echo "<script>window.location.href='tambahan.php';</script>";
                } else {
                    echo "<script>alert('Gagal Mengirim Permintaan!');</script>";
                    echo "<script>window.location.href='tambahan.php';</script>";
                }
            }
        } else {
            echo "<script>alert('Ada Input yang Kosong!');</script>";
        }
    }
}

// tombol edit tabel
if (isset($_GET['hal'])) {
    if ($_GET['hal'] == "edit") {
        $b = mysqli_query($koneksi, "SELECT * FROM request WHERE id='$_GET[id]'");
        $data = mysqli_fetch_array($b);
        if ($data) {
            $vjenis = $data['jenis'];
            $vrequest = $data['request'];
            $valasan = $data['alasan'];
        }
    } elseif ($_GET['hal'] == "hapus") {
        $hapus = mysqli_query($koneksi, "DELETE FROM request WHERE id='$_GET[id]'");
        if ($hapus) {
            echo "<script>
            alert('Hapus Data Sukses!');
            location='tambahan.php';
            </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Permintaan Pelayanan</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../globals.css" />
    <link rel="stylesheet" href="../styleguide.css" />
    <link rel="stylesheet" href="../style-permintaan-layanan.css" />
</head>

<body>
    <div class="permintaan-layanan">
        <!-- navbar -->
        <nav class="navigation">
            <div class="logo">
                <img class="group" src="../img/group-1-2.png" />
                <div class="text">
                    <div class="g">
                        <div class="text-wrapper">Sosial</div>
                    </div>
                    <div class="div-wrapper">
                        <div class="div">Net</div>
                    </div>
                </div>
            </div>
            <div class="menu">
                <li class="item"><a class="label" href="../home.php">Home</a></li>
                <li class="item layanan">
                    <a class="label" href="#">Layanan</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="label" href="../aturan_layanan.php">
                                <?php if ($_SESSION['level'] == 'petugas') { ?>
                                    Input Aturan Layanan
                                <?php } else { ?>
                                    Aturan Layanan
                                <?php } ?>
                            </a>
                        </li>
                        <li><a class="label" href="../spesifikasi_layanan.php">Spesifikasi Layanan</a></li>
                    </ul>
                </li>
                <li class="item">
                    <a href="<?php echo ($_SESSION['level'] == 'petugas') ? '../artikel-admin.php' : '../artikel-user.php'; ?>" class="label">Informasi</a>
                </li>
                <li class="item"><a class="label" href="../kepengurusan.php">Kepengurusan</a></li>
                <li class="item"><a class="label" href="../home.php#tentang">Tentang</a></li>
                <li class="item"><a class="label" href="../fitur_feedback.php">Feedback</a></li>
            </div>
            <div class="frame">
                <div class="button" id="dropdownButton">
                    <div class="text-2">
                        <?php $username = $_SESSION['username'];
                        echo "$username"; ?>
                    </div>
                    <img class="img" src="../img/vuesax-outline-arrow-down-2.svg" />
                </div>
                <ul class="dropdown-menu-log" id="dropdownMenu">
                    <li><a href="../logout.php" class="label">Logout</a></li>
                </ul>
            </div>
        </nav>
        <!-- background -->
        <div class="frame-2-1">
            <div class="BG-wrapper">
                <img class="BG" src="../img/bg.png" />
            </div>
            <div class="paragraph-container">
                <p class="heading">Permintaan Spesifikasi Layanan Yang Dibutuhkan</p>
                <p class="description">
                    Pelayanan yang anda inginkan belum tersedia? isi form yang ada agar kami dapat segera memproses
                    permintaanmu
                </p>
            </div>
            <!-- form -->
            <form class="form-isi" action="" method="POST" enctype="multipart/form-data">
                <?php
                $a = mysqli_query($koneksi, "select * from user where username='$_SESSION[username]'");
                $tampil = mysqli_fetch_array($a);
                ?>
                <div class="frame-3">
                    <div class="permintaan-ini-wrapper">
                        <p class="permintaan-ini">
                            <span class="span">Permintaan ini bersifat </span>
                            <span class="text-wrapper-2">tidak langsung</span>
                            <span class="span">. Maksudnya, permintaan yang diajukan tidak dapat langsung menjadi opsi tambahan, karena harus
                                diproses dahulu oleh petugas</span>
                        </p>
                    </div>
                    <div class="div-2">
                        <div class="frame-4">
                            <div class="text-wrapper-3">Nama Lengkap</div>
                            <div class="field-form-isi">
                                <input type="text" class="form-input" placeholder="Otomatis oleh sistem" name="username" value="<?= $tampil['nama'] ?>" disabled>
                            </div>
                        </div>
                        <div class="frame-4">
                            <div class="text-wrapper-3">Jenis Pelayanan</div>
                            <div class="field-form-dropdown">
                                <select class="form-dropdown" value="<?= $vjenis ?>" id="jenis" name="jenis" required>
                                    <option value="" disabled selected>Jenis Layanan</option>
                                    <option value="Pengaduan"> Pengaduan</option>
                                    <option value="Administrasi"> Administrasi</option>
                                </select>
                                <span class="dropdown-icon"></span>
                            </div>
                        </div>
                        <div class="frame-4">
                            <div class="text-wrapper-3">Permintaan tambahan spesifikasi layanan</div>
                            <div class="field-form-isi">
                                <input type="text" class="form-input" value="<?= $vrequest ?>" name="request" placeholder="Masukkan spesifikasi layanan">
                            </div>
                        </div>
                        <div class="frame-4">
                            <div class="text-wrapper-3">Alasan</div>
                            <div class="field-form-isi">
                                <textarea type="text" value="<?= $valasan ?>" class="form-input" name="alasan" style="resize: vertical; width: 100%; max-width: 100%; min-width: 100%; min-height: 180px;" placeholder="Masukkan alasan maksimal 100 kata"><?= $valasan ?></textarea>
                            </div>
                        </div>
                        <div class="frame-6">
                            <button type="submit" name="simpan" class="button">
                                <div class="text-3">Tambahkan</div>
                            </button>
                            <button type="reset" class="button-2">
                                <div class="text-4">Reset Data</div>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- daftar permintaan layanan -->
        <div class="frame-7">
            <div class="paragraph-container-2">
                <div class="heading-2">Daftar Administrasi Anda</div>
                <p class="p">
                    Kami akan merespons administrasi Anda dengan segera untuk memastikan kepuasan pelanggan yang optimal!
                </p>
            </div>
            <div class="frame-wrapper">
                <table class="frame-8">
                    <tr class="frame-9">
                        <td class="frame-10">
                            <div class="text-wrapper-4">No</div>
                        </td>
                        <td class="frame-11">
                            <div class="text-wrapper-5">ID</div>
                        </td>
                        <td class="frame-12">
                            <div class="text-wrapper-5">Nama</div>
                        <td>
                        <td class="frame-12">
                            <div class="text-wrapper-5">Jenis Layanan</div>
                        </td>
                        <td class="frame-12">
                            <div class="text-wrapper-5">Permintaan</div>
                        </td>
                        <td class="frame-12">
                            <div class="text-wrapper-5">Alasan</div>
                        </td>
                        <td class="frame-13">
                            <div class="text-wrapper-6">Aksi</div>
                        </td>
                    </tr>
                    <?php
                    $no = 1;
                    if ($_SESSION['level'] == "petugas") {
                        $a = mysqli_query($koneksi, "SELECT * FROM request");
                    } elseif ($_SESSION['level'] == "warga") {
                        $a = mysqli_query($koneksi, "SELECT * FROM request where userId='$_SESSION[username]'");
                    }
                    while ($tampil = mysqli_fetch_array($a)) : ?>
                        <tr class="frame-9-content-row">
                            <?php
                            $isOwner = $tampil['userId'] == $username = $_SESSION['username']; // Check if the current user is the owner of this data
                            ?>
                            <td class="frame-14">
                                <div class="text-wrapper-4"><?= $no++ ?></div>
                            </td>
                            <td class="frame-15">
                                <div class="text-wrapper-5"><?= $tampil['id'] ?></div>
                            </td>
                            <td class="frame-16">
                                <div class="text-wrapper-5"><?= $tampil['nama'] ?></div>
                            </td>
                            <td class="frame-16">
                                <div class="text-wrapper-5"><?= $tampil['jenis'] ?></div>
                            </td>
                            <td class="frame-17">
                                <div class="text-wrapper-5"><?= $tampil['request'] ?></div>
                            </td>
                            <td class="frame-16">
                                <p class="text-wrapper-5"><?= $tampil['alasan'] ?></p>
                            </td>
                            <?php if ($_SESSION['level'] == "warga" || $_SESSION['level'] == "petugas") { ?>
                                <td class="frame-18">
                                    <!-- Only display edit button if the user is the owner -->
                                    <?php if ($isOwner) { ?>
                                        <a href="tambahan.php?hal=edit&id=<?= $tampil['id'] ?>">
                                            <img class="img" src="../img/edit.png" /></a>
                                    <?php } ?>
                                    <!-- Display delete button for all -->
                                    <a href="tambahan.php?hal=hapus&id=<?= $tampil['id'] ?>" onclick="return confirm('Apakah yakin ingin menghapus data ini?')">
                                        <img class="img" src="../img/trash.png" /></a>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>

        <!-- footer -->
        <footer class="footer">
            <div class="frame-19">
                <div class="logo-2">
                    <img class="group" src="../img/group-1-3.png" />
                    <div class="text">
                        <div class="g">
                            <div class="text-5">Sosial</div>
                        </div>
                        <div class="div-wrapper">
                            <div class="div">Net</div>
                        </div>
                    </div>
                </div>
                <div class="frame-20">
                    <div class="menu-2">
                        <div class="heading-3">Fitur</div>
                        <div class="div-2">
                            <div class="item-2">Home</div>
                            <div class="item-3">Layanan</div>
                            <div class="item-3">Kepengurusan</div>
                            <div class="item-3">Tentang</div>
                            <div class="item-3">Artikel</div>
                            <div class="item-3">Feedback</div>
                        </div>
                    </div>
                    <div class="menu-2">
                        <div class="heading-3">Jenis Layanan</div>
                        <div class="div-2">
                            <div class="item-2">Laporkan Pengaduan</div>
                            <div class="item-3">Pengajuan Administrasi</div>
                            <div class="item-3">Permintaan Layanan</div>
                            <div class="item-3">Informasi Umum</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="link-wrapper">
                <div class="link">
                    <img class="ic-baseline" src="../img/ic-baseline-copyright.svg" />
                    <p class="item-4">2024. All right reserved by: Della Fitria Lestari, Ninda, Irvianti Dwityara Sany</p>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>