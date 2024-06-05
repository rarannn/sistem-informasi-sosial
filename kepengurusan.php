<?php
include "koneksi.php";
session_start();
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
if (!isset($_SESSION['username'])) {
  die("Anda belum login, klik <a href=\"index.php\">disini</a> untuk login");
} else {
  $username = $_SESSION['username'];
  $nama = $_SESSION['nama'];
  $level = $_SESSION['level'];
}

$id = $_POST['id'];
$nama = $_POST['nama'];
$nip = $_POST['nip'];
$jabatan = $_POST['jabatan'];
$file_name = $_FILES['foto']['name'];
$direktori = "uploads-gambar/";


// Proses penambahan data pengurus ke dalam database
if (isset($_POST['simpan'])) {
  if (empty($id && $nama && $nip && $jabatan && $file_name) != true) {
    $ekstensi_boleh = array('png', 'jpg', 'jpeg', 'webp');
    $x = explode('.', $file_name);
    $ekstensi = strtolower(end($x));
    $file_tmp = $_FILES['foto']['tmp_name'];
    $angka_acak = rand(1, 999);
    $nama_gambar_baru = $angka_acak . '-' . $file_name;

    if (in_array($ekstensi, $ekstensi_boleh)) {
      move_uploaded_file($file_tmp, $direktori . $nama_gambar_baru);

      $query = "INSERT INTO kepengurusan (id, nip, foto, nama, jabatan)
               values ('$id','$nip','$nama_gambar_baru','$nama','$jabatan')";
      $result = mysqli_query($koneksi, $query);

      if ($result) {
        echo "<script>alert('Berhasil Mengirim Aturan Layanan!'); window.location.href='kepengurusan.php';</script>";
      } else {
        echo "<script>alert('Query Error: " . mysqli_error($koneksi) . "');</script>";
      }
    } else {
      echo "<script>alert('Ekstensi gambar hanya bisa jpg, jpeg, webp dan png');</script>";
      echo "<script>window.location.href='kepengurusan.php';</script>";
    }
  } else {
    echo "<script>alert('Ada Input Kosong!');</script>";
    echo "<script>history.back()</script>";
  }
}

if (isset($_GET['hal']) && $_SESSION['level'] == 'petugas') {
  if ($_GET['hal'] == "hapus") {
    $hapus = mysqli_query($koneksi, "DELETE FROM kepengurusan WHERE id='$_GET[id]'");
    if ($hapus) {
      echo "<script>
          alert('Hapus Data Sukses!');
          location='kepengurusan.php';
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
  <title>Kepengurusan</title>
  <link rel="stylesheet" href="globals.css" />
  <link rel="stylesheet" href="styleguide.css" />
  <link rel="stylesheet" href="style-kepengurusan.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>

<body>
  <div class="kepengurusan">
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
          <a class="label" href="#">Layanan</a>
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
        <li class="item"><a class="label-2" href="kepengurusan.php">Kepengurusan</a></li>
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
        <p class="heading">Staff Pelayanan <br />Masyarakat</p>
        <p class="description">Bertanggung jawab dalam mengoperasikan dan memfasilitasi sistem untuk memberikan layanan yang cepat dan efisien kepada masyarakat.</p>
      </div>

      <?php if ($_SESSION['level'] == 'petugas') { ?>
        <form class="form-isi" id="aturanForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
          <div class="frame-3">
            <div class="div-2">
              <div class="frame-4">
                <div class="text-wrapper-3">Kode pengurus</div>
                <div class="field-form-isi">
                  <input type="number" class="form-control" name="id">
                </div>
              </div>
              <div class="frame-4">
                <div class="text-wrapper-3">Nama lengkap</div>
                <div class="field-form-isi">
                  <input type="text" class="form-control" name="nama">
                </div>
              </div>
              <div class="frame-4">
                <div class="text-wrapper-3">NIP</div>
                <div class="field-form-isi">
                  <input type="text" class="form-control" name="nip">
                </div>
              </div>
              <div class="frame-4">
                <div class="text-wrapper-3">Gambar</div>
                <div class="field-up">
                  <div class="smithy-weber-wrapper">
                    <label for="foto" class="file-label">Choose file</label>
                    <input type="file" class="file-input" id="foto" name="foto" accept="image/*" required>
                  </div>
                  <div class="frame-6">
                    <div class="smithy-weber" id="file-chosen">No file chosen</div>
                  </div>
                </div>
              </div>
              <div class="frame-4">
                <div class="text-wrapper-3">Jabatan</div>
                <div class="field-form-isi">
                  <input type="text" class="form-control" name="jabatan">
                </div>
              </div>

              <div class=" frame-7">
                <button type="submit" name="simpan" class="button">
                  <div class="text-3">Simpan</div>
                </button>
                <button class="button-2" type="reset" name="reset" id="resetButton"><span class="text-4">Reset</span></button>
              </div>
            </div>
          </div>
        </form>
        <br>
        <div class="heading-2">Daftar Staff Pengurus</div>


        <div class="frame-3">
          <div class="wrapper-table" style="width: 100%;">
            <table class="table">
              <tr class="text-wrapper-2" style="background-color: var(  --stroke); border-radius:30%">
                <th class="text-wrapper-2">No.</th>
                <th class="text-wrapper-2">ID</th>
                <th class="text-wrapper-2">NIP</th>
                <th class="text-wrapper-2">Foto</th>
                <th class="text-wrapper-2">Nama</th>
                <th class="text-wrapper-2">Jabatan</th>
                <?php if ($_SESSION['level'] == 'petugas') { ?>
                  <th class="text-wrapper-2" style="text-align: center;">Aksi</th>
                <?php } ?>
              </tr>
              <?php
              include "../../koneksi.php";
              $no = 1;
              $sql = "SELECT * FROM kepengurusan ORDER BY id ASC";
              $result = mysqli_query($koneksi, $sql);

              if (!$result) {
                die("Query Error: " . mysqli_error($koneksi) . "-" . mysqli_error($koneksi));
              }
              while ($tampil = mysqli_fetch_array($result)) { ?>
                <tr>
                  <td class="text-wrapper-2"><?= $no++ ?></td>
                  <td class="text-wrapper-2"><?= $tampil['id'] ?></td>
                  <td class="text-wrapper-2"><?= $tampil['nip'] ?></td>
                  <td class="text-wrapper-2"><img src="uploads-gambar/<?= $tampil['foto'] ?>" width="100" height="100"></td>
                  <td class="text-wrapper-2"><?= $tampil['nama'] ?></td>
                  <td class="text-wrapper-2"><?= $tampil['jabatan'] ?></td>
                  <?php if ($_SESSION['level'] == "petugas") { ?>
                    <td class="text-wrapper-2" style="text-align: center;">
                      <a href="kepengurusan.php?hal=hapus&id=<?= $tampil['id'] ?>" onclick="return confirm('Apakah yakin ingin menghapus data ini?')">
                        <button type="submit" class="button-trash">
                          <img class="icon" src="img/trash.png" />
                        </button></a>
                    </td>
                  <?php } ?>
                </tr>
              <?php } ?>
            </table>
          </div>
        </div>
      <?php } ?>

      <div class="frame-wrapper">
        <div class="frame-9">
          <div class="list-kepengurusan">
            <?php
            $query = "SELECT * FROM kepengurusan";
            $result = $koneksi->query($query);

            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo ' <div class="frame-11"> 
                <div class="frame-10">
                <img class="mask-group" src="uploads-gambar/' . $row['foto'] . '" />
      <div class="frame-12">
        <div class="div-3">
          <div class="frame-13">
          <div  class="element-rules-of" >' . (empty($row["nama"]) ? $row['userId'] : $row["nama"]) . '</div>
          </div>
          <div class="frame-14">
            <p class="nama">' . $row['jabatan'] . '</p>
          </div>
        </div>
      </div>
      </div>
    </div>';
              }
            } else {
              echo '<p>Tidak ada artikel ditemukan untuk kategori ini.</p>';
            }
            ?>
          </div>
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
  <script>
    document.getElementById('foto').addEventListener('change', function(event) {
      const input = event.target;
      const fileName = input.files.length > 0 ? input.files[0].name : 'No file chosen';
      const fileChosenElement = document.getElementById('file-chosen');
      fileChosenElement.textContent = fileName;
      fileChosenElement.classList.add('form-input2');
    });
  </script>
</body>

</html>