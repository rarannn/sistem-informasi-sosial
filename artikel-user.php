<?php
include "koneksi.php";
if (empty($_SESSION))
  session_start();

if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit;
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <title>Artikel User</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="globals.css" />
  <link rel="stylesheet" href="styleguide.css" />
  <link rel="stylesheet" href="style-artikel-user.css" />
</head>

<body>
  <div class="artikel">
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
        <li class="item">
          <a href="<?php echo ($_SESSION['level'] == 'petugas') ? 'artikel-admin.php' : 'artikel-user.php'; ?>" class="label-2">Informasi</a>
        </li>
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
          <img class="img" src="img/vuesax-outline-arrow-down-2.svg" />
        </div>
        <ul class="dropdown-menu-log" id="dropdownMenu">
          <li><a href="logout.php" class="label">Logout</a></li>
        </ul>
      </div>
    </nav>
    <div class="frame-2">
      <div class="BG-wrapper"><img class="BG" src="img/bg.png" /></div>
      <div class="paragraph-container">
        <div class="heading">Informasi Terbaru <br />masyarakat</div>
        <p class="description">Informasi yang relevan dan dapat menjadi sumber pengetahuan yang berharga.</p>
      </div>
      <div class="frame-3">
        <div class="category">
          <button type="button" data-category="bansos" onclick="changeCategory('bansos')" class="button-no">
            <div class="text-3">Bantuan Sosial</div>
          </button>
          <button type="button" data-category="Keamanan" onclick="changeCategory('Keamanan')" class="button-4">
            <div class="text-4">Keamanan</div>
          </button>
        </div>
        <div class="frame-wrapper">
          <div class="list-artikel">
            <?php
            // Kategori default
            $selected_category = isset($_POST['category']) ? $_POST['category'] : "bansos";

            // Query untuk mengambil artikel berdasarkan kategori
            $query = "SELECT * FROM artikel WHERE kategori = ?";
            $sql = $koneksi->prepare($query);
            $sql->bind_param("s",$selected_category);
            $sql->execute();
            $result = $sql->get_result();

            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo '<div class="frame-5">
                            <img class="mask-group" src="' . $row['gambar'] . '" />
                            <div class="frame-6">
                              <div class="div-7">
                                <div class="frame-8">
                                  <div class="penulis">' . (empty($row["nama"]) ? $row['userId'] : $row["nama"]) . '</div>
                                  <div class="text-wrapper-2">' . $row['tanggal'] . '</div>
                                </div>
                                <div class="frame-9">
                                  <p class="element-rules-of">' . $row['judul'] . '</p>
                                  <p class="p">';
                // Batasi deskripsi maksimal 20 kata
                $deskripsi = $row['deskripsi'];
                $deskripsi = implode(' ', array_slice(explode(' ', $deskripsi), 0, 20));
                echo $deskripsi . '...';
                echo '
                                  </p>
                                </div>
                              </div>';
                // Periksa apakah link kosong atau tidak valid
                if (!empty($row['link']) && filter_var($row['link'], FILTER_VALIDATE_URL)) {
                  echo '<a href="' . $row['link'] . '" class="link-detail">Selengkapnya ...</a>';
                } else {
                  echo '<a href="detail-artikel.php?id=' . $row['id'] . '" class="link-detail">Selengkapnya ...</a>';
                }
                echo '  
                            </div>
                          </div>';
              }
            } else {
              echo '<p>Tidak ada artikel ditemukan untuk kategori ini.</p>';
            }
            ?>
          </div>

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
                  $('.list-artikel').html($(response).find('.list-artikel').html()).slideDown('fast');

                  // Mengubah gaya tombol
                  $('.category button').each(function() {
                    var buttonCategory = $(this).data('category');
                    if (buttonCategory === category) {
                      $(this).removeClass('button-4').addClass('button-no');
                      $(this).find('.text-4').removeClass('text-4').addClass('text-3');
                    } else {
                      $(this).removeClass('button-no').addClass('button-4');
                      $(this).find('.text-3').removeClass('text-3').addClass('text-4');
                    }
                  });

                  $('.category button').removeClass('active');
                  $('.category button[data-category="' + category + '"]').addClass('active');
                }
              });
            }
          </script>

        </div>
      </div>
    </div>
    <footer class="footer">
      <div class="frame-10">
        <div class="logo-2">
          <img class="group" src="img/group-1-3.png" />
          <div class="text">
            <div class="g">
              <div class="text-5">Sosial</div>
            </div>
            <div class="div-wrapper">
              <div class="div">Net</div>
            </div>
          </div>
        </div>
        <div class="frame-11">
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
  </div>
</body>

</html>