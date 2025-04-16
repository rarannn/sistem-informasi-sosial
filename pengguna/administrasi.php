<?php
session_start();
include "../koneksi.php";
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
if (!isset($_SESSION['username'])) {
    die("Anda belum login, klik <a href=\"../index.php\">disini</a> untuk login");
} else {
    $username = $_SESSION['username'];
}

$kode = $_GET['id'];
$username = $_SESSION['username'];
$nama = $_SESSION['nama'];
//  $data = $_POST['data'];
$jenis = htmlspecialchars($_POST['jenis'] ?? '', ENT_QUOTES, 'UTF-8');
$deskripsi = htmlspecialchars($_POST['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8');
$file_name = htmlspecialchars(basename($_FILES['data']['name'] ?? ''), ENT_QUOTES, 'UTF-8');
$direktori = __DIR__ . "/../upload-file/administrasi/";


if (isset($_POST['simpan'])) {
    if (!empty($jenis) && !empty($deskripsi)) {
        if ($_FILES['data']['size'] >= 50000000) {
            echo "<script>
              alert('Ukuran maksimal file yang boleh dikirim adalah 50MB');
              setTimeout(function() {
                window.location.href = 'aturan_layanan.php';
              }, 2000);
            </script>";
            exit;
        }
        $ekstensi_boleh = array('pdf');
        $x = explode('.', $file_name);
        $ekstensi = strtolower(end($x));
        if (in_array($ekstensi, $ekstensi_boleh) === true) {
            if ($_GET['hal'] == "edit") {
                $sql = $koneksi->prepare("UPDATE administrasi SET userId = ?, nama = ?, jenis = ?, deskripsi = ?, `data` = ?, tanggal = NOW() WHERE id = ? AND userId = ?");
                $sql->bind_param("sssssis", $username, $nama, $jenis, $deskripsi, $file_name, $_GET['id'], $username);
                $edit = $sql->execute();

                if ($edit) {
                    if (move_uploaded_file($_FILES['data']['tmp_name'], $direktori . $file_name)){
                    echo "<script>alert('Berhasil Memperbarui administrasi!');</script>";
                    header("refresh:2;url=administrasi.php");
                } else {
                    echo "<script>alert('Gagal memindahkan file!');</script>";
                    header("refresh:2;url=administrasi.php");
                }
                } else {
                    echo "<script>alert('Edit Data Gagal!');</script>";
                    header("refresh:2;url=administrasi.php");
                }
            } else {

                $sql = "INSERT INTO administrasi (userId, nama, jenis, deskripsi, `data`, tanggal)
                VALUES (?, ?, ?, ?, ?, NOW())";
                $stmt = $koneksi->prepare($sql);
                $stmt->bind_param("sssss", $username, $nama, $jenis, $deskripsi, $file_name);
                $a = $stmt->execute();
                if ($a === true) {
                    move_uploaded_file($_FILES['data']['tmp_name'], $direktori . $file_name);
                    echo "<script>alert('Berhasil Mengirim administrasi!');</script>";
                    echo "<script>location('administrasi.php?status=sukses');</script>";
                } else {
                    echo "<script>alert('Gagal Mengirim administrasi!');</script>";
                    echo "<script>location('administrasi.php?status=gagal');</script>";
                }
            }
        } else {
            echo "<script>alert('Ekstensi gambar hanya bisa pdf');</script>";
            echo "<script>history.back();</script>";
        }
    } else {
        echo "<script>alert('Ada Input yang Kosong!');</script>";
        echo "<script>history.back();</script>";
    }
} else {
    echo "<script>location('administrasi.php');</script>";
}

// tombol hapus tabel
if (isset($_GET['hal'])) {
    if ($_GET['hal'] == "edit") {
        $b = $koneksi->prepare("SELECT * FROM administrasi where id=?");
        $b->bind_param("i", $_GET['id']);
        $b->execute();
        $result = $b->get_result();
        $data = $result->fetch_array(MYSQLI_ASSOC);
        if ($data) {
            $vjenis = $data['jenis'];
            $vdesk = $data['deskripsi'];
            $vdata = $data['data'];

        }
    } elseif ($_GET['hal'] == "hapus") {
        $hapus = $koneksi->prepare("DELETE FROM administrasi WHERE id=? AND userId=?");
        $hapus->bind_param('is', $_GET['id'], $username);
        $hapus->execute();
        if ($hapus) {
            echo "<script>
            alert('Hapus Data Sukses!');
            location='administrasi.php';
            </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>administrasi</title>
    <link rel="stylesheet" href="../globals.css" />
    <link rel="stylesheet" href="../styleguide.css" />
    <link rel="stylesheet" href="../style-layanan-administrasi.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">

</head>

<body>
    <div class="layanan-administrasi">
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
        <div class="frame-2-1">
            <div class="BG-wrapper"><img class="BG" src="../img/bg.png" /></div>
            <div class="paragraph-container">
                <div class="heading">Layanan Administrasi<br />masyarakat</div>
                <p class="description">
                    Ajukan permintaanmu terkait surat keterangan, dan semacamnya. Hanya dengan mengisi persyaratan yang
                    diminta, dan pastikan biodatamu benar.
                </p>
            </div>
            <!-- form -->
            <form class="form-isi" action="" method="POST" enctype="multipart/form-data">
                <?php
                $a = mysqli_query($koneksi, "select * from user where username='$_SESSION[username]'");
                $tampil = mysqli_fetch_array($a);
                ?>
                <div class="frame-3">
                    <div class="sebelum-mengisi-wrapper">
                        <p class="sebelum-mengisi">
                            <span class="span">Sebelum Mengisi Aministrasi, lihat persyaratan yang diinginkan terlebih dahulu pada
                            </span>
                            <a href="../aturan_layanan.php" class="text-wrapper-2">aturan layanan</a>
                        </p>
                    </div>
                    <div class="div-2">
                        <div class="frame-4">
                            <div class="text-wrapper-3">ID administrasi</div>
                            <div class="field-form-isi">
                                <input type="text" class="form-input" placeholder="Otomatis oleh sistem" name="id" value="<?= $data['id'] ?>" disabled>
                            </div>
                        </div>
                        <div class="frame-4">
                            <div class="text-wrapper-3">Nama Lengkap</div>
                            <div class="field-form-isi">
                                <input type="text" class="form-input" placeholder="Otomatis oleh sistem" name="username" value="<?= $tampil['nama'] ?>" disabled>
                            </div>
                        </div>
                        <div class="frame-4">
                            <div class="text-wrapper-3">Jenis administrasi</div>
                            <div class="field-form-dropdown">
                                <select class="form-dropdown" id="jenis" value="<?= $vjenis ?>" name="jenis" required>
                                    <option value="" disabled selected>Jenis Administrasi</option>
                                    <?php
                                    include "../koneksi.php";
                                    $a = "SELECT * FROM layanan WHERE jenis = 'administrasi'";
                                    $b = mysqli_query($koneksi, $a);
                                    while ($c = $b->fetch_array()) { ?>
                                        <option value="<?php echo $c['spesifikasi'] ?>">
                                            <?php echo $c['spesifikasi']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="dropdown-icon"></span>
                            </div>
                        </div>
                        <div class="frame-4">
                            <div class="text-wrapper-3">Deskripsi</div>
                            <div class="field-form-isi">
                                <textarea type="text" class="form-input" value="<?= @$vdesk ?>" style="resize: vertical; width: 100%; max-width: 100%; min-width: 100%; min-height: 180px;" placeholder="Masukkan detail administrasi maksimal 200 kata" id="deskripsi" name="deskripsi"><?= $vdesk ?></textarea>
                            </div>
                        </div>
                        <div class="frame-4">
                            <div class="text-wrapper-3">Data pendukung</div>
                            <div class="field-up-wrapper">
                                <div class="field-up">
                                    <div class="smithy-weber-wrapper">
                                        <label for="formFileMultiple" class="file-label">Chose file</label>
                                        <input class="file-input" type="file" id="formFileMultiple" name="data" value="<?= @$data ?>" accept="application/pdf">
                                    </div>
                                    <div class="frame-7">
                                        <div class="smithy-weber" id="file-chosen">No file chosen</div>
                                    </div>
                                </div>
                                <p class="format-pdf-pastikan">
                                    <span class="text-wrapper-4">Format .pdf </span>
                                    <span class="text-wrapper-5">pastikan sesuai dengan format yang diminta</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="frame-8">
                        <button type="submit" name="simpan" class="button">
                            <div class="text-3">Tambahkan</div>
                        </button>
                        <button type="reset" id="resetButton" class="button-2">
                            <div class="text-4">Reset Data</div>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- daftar administrasi -->
        <div class="frame-9">
            <div class="paragraph-container-2">
                <div class="heading-2">Daftar Administrasi Anda</div>
                <p class="p">
                    Kami akan merespons administrasi Anda dengan segera untuk memastikan kepuasan pelanggan yang optimal!
                </p>
            </div>
            <div class="frame-wrapper">
                <table class="frame-10">
                    <tr class="frame-11">
                        <td class="frame-12">
                            <div class="text-wrapper-7">No</div>
                        </td>
                        <td class="frame-13">
                            <div class="text-wrapper-7">ID</div>
                        </td>
                        <td class="frame-14">
                            <div class="text-wrapper-7">Nama</div>
                        </td>
                        <td class="frame-14">
                            <div class="text-wrapper-7">Jenis Administrasi</div>
                        </td>
                        <td class="frame-14">
                            <div class="text-wrapper-7">Deskripsi</div>
                        </td>
                        <td class="frame-14">
                            <div class="text-wrapper-7">Dokumen Pendukung</div>
                        </td>
                        <td class="frame-15">
                            <div class="text-wrapper-8">Aksi</div>
                        </td>
                    </tr>
                    <?php
                    $no = 1;
                    if ($_SESSION['level'] == "petugas") {
                        $a = mysqli_query($koneksi, "SELECT * FROM administrasi");
                    } elseif ($_SESSION['level'] == "warga") {
                        $a = mysqli_query($koneksi, "SELECT * FROM administrasi where userId='$_SESSION[username]'");
                    }
                    while ($tampil = mysqli_fetch_array($a)) : ?>
                        <tr class="frame-11-content-row">
                            <?php
                            $isOwner = $tampil['userId'] == $username = $_SESSION['username']; // Check if the current user is the owner of this data
                            ?>
                            <td class="frame-16">
                                <div class="text-wrapper-7"><?= $no++ ?></div>
                            </td>
                            <td class="frame-17">
                                <div class="text-wrapper-7"><?= $tampil['id'] ?></div>
                            </td>
                            <td class="frame-18">
                                <div class="text-wrapper-7"><?= $tampil['nama'] ?></div>
                            </td>
                            <td class="frame-18">
                                <div class="text-wrapper-7"><?= $tampil['jenis'] ?></div>
                            </td>
                            <td class="frame-19">
                                <div class="text-wrapper-7">
                                    <?= $tampil['deskripsi'] ?>
                                </div>
                            </td>
                            <td class="frame-18">
                                <div class="text-wrapper-7"><a href="downloadfile.php?adm=<?= $tampil['data']; ?>"><?php echo $tampil['data']; ?></a></div>
                            </td>
                            <?php if ($_SESSION['level'] == "warga" || $_SESSION['level'] == "petugas") { ?>
                                <td class="frame-20">
                                    <!-- Only display edit button if the user is the owner -->
                                    <?php if ($isOwner) { ?>
                                        <a href="administrasi.php?hal=edit&id=<?= $tampil['id'] ?>">
                                            <img class="img" src="../img/edit.png" /></a>
                                    <?php } ?>
                                    <!-- Display delete button for all -->
                                    <a href="administrasi.php?hal=hapus&id=<?= $tampil['id'] ?>" onclick="return confirm('Apakah yakin ingin menghapus data ini?')">
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
            <div class="frame-21">
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
                <div class="frame-22">
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
                    <p class="item-4">2024. All right reserved by: Della Fitria Lestari, Ninda, Irvianti Dwityara Sany
                    </p>
                </div>
            </div>
        </footer>
        <script>
            document.getElementById('formFileMultiple').addEventListener('change', function() {
                var fileInput = document.getElementById('formFileMultiple');
                var fileChosen = document.getElementById('file-chosen');

                if (fileInput.files.length > 0) {
                    fileChosen.textContent = fileInput.files[0].name;
                } else {
                    fileChosen.textContent = 'No file chosen';
                }
            });
        </script>
</body>

</html>