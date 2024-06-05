<?php
include "koneksi.php";
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit;
}

$kode = isset($_GET['id']) ? $_GET['id'] : null;
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : null;
$jenis = isset($_POST['jenis']) ? $_POST['jenis'] : null;
$layanan = isset($_POST['spesifikasi']) ? $_POST['spesifikasi'] : null;


if (isset($_POST['simpan'])) {
  if (!empty($jenis) && !empty($layanan)) {
    $sql = "INSERT INTO layanan (jenis, spesifikasi) values ('$jenis', '$layanan')";
    $a = $koneksi->query($sql);
    if ($a === true) {
      echo "<script>alert('Berhasil Mengirim Spesifikasi Layanan!');</script>";
      header("refresh:2;url=spesifikasi_layanan.php");
      // echo "<script>location('spesifikasi_layanan.php?status=sukses');</script>";
    } else {
      echo "<script>alert('Gagal Menginput Spesifikasi Layanan!');</script>";
      // echo "<script>location('spesifikasi_layanan.php?status=gagal');</script>";
      header("refresh:2;url=spesifikasi_layanan.php");
    }
  } else {
    echo "<script>alert('Ada Input yang Kosong!');</script>";
    // echo "<script>location('spesifikasi_layanan.php?status=gagal');</script>";
    header("refresh:2;url=spesifikasi_layanan.php");
  }
} else {
  //  echo "<script>alert('Gagal Mengirim Pengaduan!');</script>";
  echo "<script>location('spesifikasi_layanan.php');</script>";
}

// tombol edit tabel
if (isset($_GET['hal'])) {
  if ($_GET['hal'] == "hapus") {
    $hapus = mysqli_query($koneksi, "DELETE FROM layanan WHERE id='$_GET[id]'");
    if ($hapus) {
      echo "<script>
          alert('Hapus Data Sukses!');
          location='spesifikasi_layanan.php';
          </script>";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <title>Spesifikasi Layanan</title>
  <link rel="stylesheet" href="globals.css" />
  <link rel="stylesheet" href="styleguide.css" />
  <link rel="stylesheet" href="style-spesifikasi-layanan.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>

<body>
  <div class="spesifikasi-layanan">
    <nav class="navigation">
      <div class="logo">
        <img class="group" src="img/group-1-2.png" />
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
        <li class="item"><a class="label" href="home.php">Home</a></li>
        <li class="item layanan">
          <a class="label-2" href="#">Layanan</a>
          <ul class="dropdown-menu">
            <li>
              <a class="label" href="aturan_layanan.php">
                <?php if ($_SESSION['level'] == 'petugas') { ?>
                  Input Aturan Layanan
                <?php } else { ?>
                  Aturan Layanan
                <?php } ?>
              </a>
            </li>
            <li><a class="label" href="spesifikasi_layanan.php">Spesifikasi Layanan</a></li>
          </ul>
        </li>

        <li class="item"><a href="<?php echo ($_SESSION['level'] == 'petugas') ? 'artikel-admin.php' : 'artikel-user.php'; ?>" class="label">Informasi</a></li>
        <li class="item"><a class="label" href="kepengurusan.php">Kepengurusan</a></li>
        <li class="item"><a class="label" href="home.php#tentang">Tentang</a></li>
        <li class="item"><a class="label" href="fitur_feedback.php">Feedback</a></li>
      </div>
      <div class="frame">
        <div class="button" id="dropdownButton">
          <div class="text-2">
            <?php $username = $_SESSION['username'];
            echo "$username"; ?>
          </div>
          <img class="vuesax-outline-arrow" src="img/vuesax-outline-arrow-down-2.svg" />
        </div>
        <ul class="dropdown-menu-log" id="dropdownMenu">
          <li><a href="logout.php" class="label">Logout</a></li>
        </ul>
      </div>
    </nav>

    <div class="frame-2-1">
      <div class="BG-wrapper"><img class="BG" src="img/bg.png" /></div>
      <div class="paragraph-container">
        <p class="heading">Spesifikasi Pelayanan <br />SosialNet</p>
        <p class="description">Jenis layanan masyarakat yang memfasilitasi untuk menyampaikan aspirasi dan permintaan, serta memberikan layanan dengan efisien.</p>
      </div>

      <?php if ($_SESSION['level'] == 'petugas') { ?>
        <form class="form-isi" id="layananForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
          <div class="frame-3">

            <div class="div-2">
              <div class="frame-4">
                <div class="text-wrapper-3">Jenis Layanan</div>
                <div class="field-form-dropdown">
                  <select class="form-dropdown" name="jenis" required>
                    <option value="" disabled selected>Pilih jenis layanan</option>
                    <option value="Pengaduan">Pengaduan</option>
                    <option value="Administrasi">Administrasi</option>
                  </select>
                  <span class="dropdown-icon"></span>
                </div>
              </div>
              <div class="frame-4">
                <div class="text-wrapper-3">Spesifikasi Layanan</div>
                <div class="field-form-isi">
                  <input type="text" class="form-input" name="spesifikasi">
                </div>
              </div>
              <div class=" frame-7">
                <button type="submit" name="simpan" class="button" onclick="return validateForm()">
                  <div class="text-3">Simpan</div>
                </button>
                <button class="button-2" type="reset" id="resetButton" type="reset" name="reset"><span class="text-4">Reset</span></button>
              </div>
            </div>
          </div>
        </form>
        <br>
        <div class="heading-2">Daftar Layanan</div>
      <?php } ?>
      <div class="frame-3">
        <div class="wrapper-table" style="width: 100%;">
          <table class="table">
            <tr class="text-wrapper-2" style="background-color: var(  --stroke);">
              <th class="text-wrapper-2">No.</th>
              <th class="text-wrapper-2">Jenis Pengaduan</th>
              <th class="text-wrapper-2">Spesifikasi Layanan</th>
              <?php if ($_SESSION['level'] == 'petugas') { ?>
                <th class="text-wrapper-2">Aksi</th>
              <?php } ?>
            </tr>
            <?php
            $no = 1;
            $a = mysqli_query($koneksi, "SELECT * FROM layanan");
            while ($tampil = mysqli_fetch_array($a)) : ?>
              <tr class="text-wrapper-2">
                <td class="text-wrapper-2"><?= $no++ ?></td>
                <td class="text-wrapper-2"><?= $tampil['jenis'] ?></td>
                <td class="text-wrapper-2"><?= $tampil['spesifikasi'] ?></td>
                <?php if ($_SESSION['level'] == 'petugas') { ?>
                  <td class="text-wrapper-2">
                    <a href="spesifikasi_layanan.php?hal=hapus&id=<?= $tampil['id'] ?>" onclick="return confirm('Apakah yakin ingin menghapus data ini?')">
                      <button type="submit" class="button-trash">
                        <img class="icon" src="img/trash.png" />
                      </button></a>
                  </td>
                <?php } ?>
              </tr>
            <?php endwhile; ?>
          </table>
        </div>
      </div>
    </div>

    <footer class="footer">
      <div class="frame-16">
        <div class="logo-2">
          <img class="group" src="img/group-1-3.png" />
          <div class="text">
            <div class="g">
              <div class="text-7">Sosial</div>
            </div>
            <div class="div-wrapper">
              <div class="div">Net</div>
            </div>
          </div>
        </div>
        <div class="frame-17">
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
          <img class="ic-baseline" src="img/ic-baseline-copyright.svg" />
          <p class="item-4">2024. All right reserved by: Della Fitria Lestari, Ninda, Irvianti Dwityara Sany</p>
        </div>
      </div>
    </footer>

  </div>

</body>

</html>