<?php
include "koneksi.php";
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit;
}

// Fetch data for the specific article
if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $query = "SELECT * FROM artikel WHERE id = ?";
  $sql = $koneksi->prepare($query);
  $sql->bind_param("i",$id);
  $sql->execute();
  $result = $sql->get_result();
  $row = $result->fetch_assoc();
} else {
  // Redirect or handle case where article id is not provided
  header("Location: home.php");
  exit;
}

$nama = isset($row["nama"]) ? $row["nama"] : $row['userId'];

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <title>Detail Artikel</title>
  <link rel="stylesheet" href="globals.css" />
  <link rel="stylesheet" href="styleguide.css" />
  <link rel="stylesheet" href="style-detail-artikel.css" />
</head>

<body>
  <div class="detail-artikel">
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
        <li class="item"><a class="label-2" href="home.php">Home</a></li>
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

        <li class="item"><a class="label" href="#">Informasi</a></li>
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
    <div class="frame-2">
      <div class="frame-3">
        <div class="blog-information">
          <div class="frame-4">
            <div class="penulis"><?php echo $nama; ?></div>
            <div class="text-wrapper-2"><?php echo $row['tanggal']; ?></div>
          </div>
        </div>
        <p class="how-to-make-a-game"><?php echo $row['judul']; ?></p>
      </div>
      <img class="mask-group" src="<?php echo $row['gambar'] ?>" />
      <p class="google-has-been">
        <?php echo $row['deskripsi']; ?>
      </p>
    </div>
    <div class="frame-5">
      <div class="div-2">
        <div class="heading">Artikel Lainnya</div>
        <p class="description">
          Tulisan yang menyajikan informasi yang relevan dan dapat menjadi sumber pengetahuan yang berharga.
        </p>
      </div>
      <div class="frame-wrapper">
        <div class="frame-6">
          <div class="list-artikel">
            <?php
            // Fetch existing data from the database for Kategori
            $query = "SELECT * FROM artikel WHERE kategori IN ('bansos', 'keamanan') ORDER BY tanggal ASC LIMIT 3";
            $result_kategori = $koneksi->query($query);
            while ($row = $result_kategori->fetch_assoc()) { ?>
              <div class="frame-8">
                <img class="img" src="<?php echo $row['gambar']; ?>" />
                <div class="frame-9">
                  <div class="div-2">
                    <div class="frame-10">
                      <div class="penulis"><?php echo empty($nama) ? $row['userId'] : $nama; ?></div>
                      <div class="text-wrapper-3"><?php echo $row['tanggal']; ?></div>
                    </div>
                    <div class="frame-11">
                      <p class="element-rules-of"><?php echo $row['judul']; ?></p>
                      <p class="p">
                        <?php
                        // Limit deskripsi maksimal 3 kalimat
                        $deskripsi = $row['deskripsi'];
                        $deskripsi = implode(' ', array_slice(explode(' ', $deskripsi), 0, 20));
                        echo $deskripsi . "...";
                        ?>
                      </p>
                    </div>
                  </div>
                  <?php
                  // Periksa apakah link kosong atau tidak valid
                  if (!empty($row['link']) && filter_var($row['link'], FILTER_VALIDATE_URL)) { ?>
                    <a href="<?php echo $row['link']; ?>" class="link-detail">Selengkapnya ...</a>
                  <?php } else { ?>
                    <a href="detail-artikel.php?id=<?php echo $row['id']; ?>" class="link-detail">Selengkapnya ...</a>
                  <?php } ?>
                </div>
              </div>
            <?php } ?>
            <!-- ---- -->
          </div>
        </div>
      </div>
    </div>
    <footer class="footer">
      <div class="frame-12">
        <div class="logo-2">
          <img class="group" src="img/group-1-3.png" />
          <div class="text">
            <div class="g">
              <div class="text-3">Sosial</div>
            </div>
            <div class="div-wrapper">
              <div class="div">Net</div>
            </div>
          </div>
        </div>
        <div class="frame-13">
          <div class="menu-2">
            <div class="heading-2">Fitur</div>
            <div class="list">
              <div class="item-2">Home</div>
              <div class="item-3">Layanan</div>
              <div class="item-3">Kepengurusan</div>
              <div class="item-3">Tentang</div>
              <div class="item-3">Artikel</div>
              <div class="item-3">Feedback</div>
            </div>
          </div>
          <div class="menu-2">
            <div class="heading-2">Jenis Layanan</div>
            <div class="list">
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