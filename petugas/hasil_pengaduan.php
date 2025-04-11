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
$petugas = $_SESSION['nama'];
$file_name = htmlspecialchars($_FILES['file']['name'] ?? '', ENT_QUOTES, 'UTF-8');
$file_tmp = $_FILES['file']['tmp_name'];
$direktori_pengaduan = __DIR__ . "/../upload-file/pengaduan/";
$direktori_administrasi = __DIR__ . "/..upload-file/administrasi/";
$jenis_layanan = htmlspecialchars($_POST['jenis'] ?? '', ENT_QUOTES, "UTF-8");
$allowed_ext = ['pdf', 'txt', 'docx', 'doc'];

if ($jenis_layanan == 'Pengaduan') {
    $direktori = $direktori_pengaduan;
} else {
    $direktori = $direktori_administrasi;
}
$linkberkas = $direktori . $file_name;

if (isset($_POST['simpan'])) {

    $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if (!in_array($extension, $allowed_ext)) {
        echo "<script>alert('Hanya menerima file yang memiliki ekstensi pdf, txt ,docx, doc');</script>";
        die;
    }

    if (empty($pengId) || empty($nama) || empty($tanggapan) || empty($file_name)) {
        echo "<script>alert('Ulangi ada input yang kosong');</script>";
        echo "<script>history.back();</script>";
    } else {
        if ($jenis_layanan == 'Pengaduan') {
            $sql_check = "SELECT * FROM pengaduan WHERE id=? AND nama=?";
        } else {
            $sql_check = "SELECT * FROM administrasi WHERE id=? AND nama=?";
        }
        $result_check = $koneksi->prepare($sql_check);
        $result_check->bind_param("ss", $pengId, $nama);
        $result_check->execute();
        $result_check->store_result();

        if ($result_check->num_rows > 0) {
            if ($jenis_layanan == 'Pengaduan') {
                $sql = "INSERT INTO hasil_pengaduan (pengaduanId, nama, deskripsi, file, tanggal, petugas) 
                        VALUES (?, ?, ?, ?, NOW(), ?)";
            } else {
                $sql = "INSERT INTO hasil_administrasi (administrasiId, nama, deskripsi, file, tanggal, petugas) 
                        VALUES (?, ?, ?, ?, NOW(), ?)";
            }
            $a = $koneksi->prepare($sql);
            $a->bind_param("sssss", $pengId, $nama, $tanggapan, $file_name, $petugas);
            if ($a->execute()) {
                move_uploaded_file($file_tmp, $linkberkas);
                echo "<script>alert('Berhasil Mengirim Hasil $jenis_layanan!');</script>";
                header("refresh:2;url=hasil_pengaduan.php");
            } else {
                echo "<script>alert('Gagal Mengirim Hasil $jenis_layanan!');</script>";
                header("refresh:2;url=hasil_pengaduan.php");
            }
        } else {
            if ($jenis_layanan == 'Pengaduan') {
                echo "<script>alert('Kode pengaduan atau nama pengaduan tidak sesuai dengan id pengaduan');</script>";
            } else {
                echo "<script>alert('Kode administrasi atau nama administrasi tidak sesuai dengan id administrasi');</script>";
            }
            echo "<script>history.back();</script>";
        }
    }
} else {
    echo "<script>location('hasil_pengaduan.php');</script>";
}

// tombol hapus
if (isset($_GET['hal'])) {
    $query1 = "DELETE FROM hasil_pengaduan WHERE id=?";
    $query2 = "DELETE FROM hasil_administrasi WHERE id=?";
    try {
        $sql = $koneksi->prepare($query1);
        $sql->bind_param("i",$_GET['id']);
        $sql->execute();

        $sql = $koneksi->prepare($query2);
        $sql->bind_param("i",$_GET['id']);
        $sql->execute();
        // If both succeed, commit the query.
        $koneksi->commit();

        echo "<script>
            alert('Hapus Data Sukses!');
            location='hasil_pengaduan.php';
        </script>";

        } catch (Exception $e) {
            // If something failed, roll back
            $koneksi->rollback();
            error_log("Database error: " . $e->getMessage());
            echo "<script>
                alert('Hapus Data Gagal:')
                location='hasil_pengaduan.php';
            </script>";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Layanan</title>
    <link rel="stylesheet" href="../globals.css" />
    <link rel="stylesheet" href="../styleguide.css" />
    <link rel="stylesheet" href="../style-hasil-pengaduan.css">
</head>

<body>
    <div class="hasil-pengaduan">
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
                <?php if ($_SESSION['level'] == 'petugas') { ?>

                    <?php
                    $a = mysqli_query($koneksi, "select * from user where username='$_SESSION[username]'");
                    $tampil = mysqli_fetch_array($a);
                    ?>
                    <div class="frame-3">
                        <div class="sebelum-mengisi-wrapper">
                            <p class="sebelum-mengisi">
                                <span class="span">Pastikan ID sesuai dengan pengaduan atau ajuan layanan
                                </span>
                                <a href="javascript:void(0)" class="text-wrapper-2" onclick="openModal()"> disini</a>
                            </p>
                        </div>

                        <!-- The Modal -->
                        <div id="myModal" class="modal">
                            <!-- Modal content -->
                            <div class="modal-content">
                                <span class="close" onclick="closeModal()">&times;</span>
                                <p>Pilih salah satu opsi berikut:</p>
                                <a href="../pengguna/pengaduan.php" class="popup-link">Pengaduan</a>
                                <a href="../pengguna/administrasi.php" class="popup-link">Administrasi</a>
                            </div>
                        </div>

                        <script>
                            function openModal() {
                                document.getElementById("myModal").style.display = "block";
                            }

                            function closeModal() {
                                document.getElementById("myModal").style.display = "none";
                            }

                            window.onclick = function(event) {
                                if (event.target == document.getElementById("myModal")) {
                                    closeModal();
                                }
                            }
                        </script>
                        <div class="div-2">
                            <div class="frame-4">
                                <div class="text-wrapper-3">Respon Ajuan</div>
                                <div class="field-form-dropdown">
                                    <select class="form-dropdown" value="<?= $jenis_layanan ?>" id="jenis" name="jenis" required>
                                        <option value="" disabled selected>Jenis Layanan</option>
                                        <option value="Pengaduan" name="jenis"> Pengaduan</option>
                                        <option value="Administrasi" name="jenis"> Administrasi</option>
                                    </select>
                                    <span class="dropdown-icon"></span>
                                </div>
                            </div>
                            <div class="frame-4">
                                <div class="text-wrapper-3">ID pengaduan</div>
                                <div class="field-form-isi">
                                    <input type="text" class="form-input" placeholder="Harus sesuai dengan kode pengaduan" name="pengId">
                                </div>
                            </div>
                            <div class="frame-4">
                                <div class="text-wrapper-3">Nama Pemohon</div>
                                <div class="field-form-isi">
                                    <input type="text" class="form-input" placeholder="Harus sesuai dengan kode pengaduan" name="nama">
                                </div>
                            </div>
                            <div class="frame-4">
                                <div class="text-wrapper-3">Tanggapan</div>
                                <div class="field-form-isi">
                                    <textarea type="text" class="form-input" value="<?= @$vdesk ?>" style="resize: vertical; width: 100%; max-width: 100%; min-width: 100%; min-height: 180px;" placeholder="Masukkan detail tanggapan maksimal 200 kata" id="deskripsi" name="tanggapan"><?= $vdesk ?></textarea>
                                </div>
                            </div>
                            <div class="frame-4">
                                <div class="text-wrapper-3">Data pendukung</div>
                                <div class="field-up-wrapper">
                                    <div class="field-up">
                                        <div class="smithy-weber-wrapper">
                                            <label for="formFileMultiple" class="file-label">Chose file</label>
                                            <input class="file-input" type="file" id="formFileMultiple" name="file" value="<?= @$data ?>" accept="application/pdf">
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
                <?php } ?>
            </form>
        </div>

        <!-- daftar administrasi -->
        <div class="frame-9">
            <div class="paragraph-container-2">
                <div class="heading-2">Daftar Tanggapan Ajuan</div>
                <p class="p">
                    Respon ajuan pengaduan dan layanan administrasi dengan segera untuk memastikan kepuasan pelanggan yang optimal! </p>
            </div>

            <div class="frame-wrapper">
                <div class="type category">
                    <button type="button" data-category="pengaduan" onclick="changeCategory('pengaduan')" class="button-no">
                        <div class="text-5">Pengaduan</div>
                    </button>
                    <button type="button" data-category="administrasi" onclick="changeCategory('administrasi')" class="button-4">
                        <div class="text-6">Administrasi</div>
                    </button>
                </div>
                <script>
                    function changeCategory(category) {
                        document.getElementById('selectedCategory').value = category;
                        document.getElementById('categoryForm').submit();
                    }
                </script>
                <table class="frame-10">
                    <tr class="frame-11">
                        <td class="frame-12">
                            <div class="text-wrapper-6">No</div>
                        </td>
                        <td class="frame-13">
                            <div class="text-wrapper-7">ID</div>
                        </td>
                        <td class="frame-14">
                            <div class="text-wrapper-7">Nama</div>
                        </td>
                        <td class="frame-14">
                            <div class="text-wrapper-7">Tanggapan</div>
                        </td>
                        <td class="frame-14">
                            <div class="text-wrapper-7">Tanggal Tanggapan</div>
                        </td>
                        <td class="frame-14">
                            <div class="text-wrapper-7">Nama Petugas</div>
                        </td>
                        <td class="frame-14">
                            <div class="text-wrapper-7">Dokumen Pendukung</div>
                        </td>
                        <?php if ($_SESSION['level'] == 'petugas') { ?>
                            <td class="frame-15">
                                <div class="text-wrapper-8">Aksi</div>
                            </td><?php } ?>
                    </tr>
                    <?php
                    $selected_type = isset($_POST['category']) ? $_POST['category'] : "pengaduan";

                    $no = 1;
                    if ($selected_type == "pengaduan") {
                        if ($_SESSION['level'] == "petugas") {
                            $a = mysqli_query($koneksi, "SELECT * FROM hasil_pengaduan");
                        } elseif ($_SESSION['level'] == "warga") {
                            $a = mysqli_query($koneksi, "SELECT * FROM hasil_pengaduan WHERE nama= '$_SESSION[nama]' ");
                        }
                    } else if ($selected_type == "administrasi") {
                        if ($_SESSION['level'] == "petugas") {
                            $a = mysqli_query($koneksi, "SELECT * FROM hasil_administrasi");
                        } elseif ($_SESSION['level'] == "warga") {
                            $a = mysqli_query($koneksi, "SELECT * FROM hasil_administrasi WHERE nama= '$_SESSION[nama]' ");
                        }
                    }

                    while ($tampil = mysqli_fetch_array($a)) : ?>
                        <tr class="frame-11-content-row">
                            <?php
                            $isOwner = $tampil['userId'] == $username = $_SESSION['username']; // Check if the current user is the owner of this data
                            ?>
                            <td class="frame-16">
                                <div class="text-wrapper-6"><?= $no++ ?></div>
                            </td>
                            <td class="frame-17">
                                <div class="text-wrapper-7"> <?= $tampil['administrasiId'] ?> <?= $tampil['pengaduanId'] ?></div>
                            </td>
                            <td class="frame-18">
                                <div class="text-wrapper-7"><?= $tampil['nama'] ?></div>
                            </td>
                            <td class="frame-18">
                                <div class="text-wrapper-7"><?= $tampil['deskripsi'] ?></div>
                            </td>
                            <td class="frame-18">
                                <div class="text-wrapper-7"><?= $tampil['tanggal'] ?></div>
                            </td>
                            <td class="frame-18">
                                <div class="text-wrapper-7"><?= $tampil['petugas'] ?></div>
                            </td>
                            <td class="frame-18">
                                <div class="text-wrapper-7">
                                    <a href="downloadfile.php?type=pengaduan&file=<?= $tampil['file']; ?>"><?php echo $tampil['file']; ?></a>
                                </div>
                            </td>

                            <?php if ($_SESSION['level'] == 'petugas') { ?>
                                <td class="frame-20">
                                    <a href="hasil_pengaduan.php?hal=hapus&id=<?= $tampil['id'] ?>&jenis=<?= $jenis_layanan ?>" onclick="return confirm('Apakah yakin ingin menghapus data ini?')">
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
                            <div class="text-7">Sosial</div>
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
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            // Fungsi untuk mengubah kategori artikel
            function changeCategory(category) {
                $.ajax({
                    type: "POST",
                    url: "",
                    data: {
                        category: category
                    },
                    success: function(response) {
                        $('.frame-10').html($(response).find('.frame-10').html()).slideDown('fast');

                        // Mengubah gaya tombol
                        $('.category button').each(function() {
                            var buttonCategory = $(this).data('category');
                            if (buttonCategory === category) {
                                $(this).removeClass('button-4').addClass('button-no');
                                $(this).find('.text-6').removeClass('text-6').addClass('text-5');
                            } else {
                                $(this).removeClass('button-no').addClass('button-4');
                                $(this).find('.text-5').removeClass('text-5').addClass('text-6');
                            }
                        });

                        $('.category button').removeClass('active');
                        $('.category button[data-category="' + category + '"]').addClass('active');
                    }
                });
            }
        </script>
    </div>
</body>

</html>