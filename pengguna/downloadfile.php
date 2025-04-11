<?php
session_start();
include "../koneksi.php";

// Menggunakan list direktori yang diizinkan dengan path relatif
$allowedDirs = [
    'peng' => __DIR__ . '/../upload-file/pengaduan/',
    'adm'  => __DIR__ . '/../upload-file/administrasi/'
];

$param = null;
$folder = null;

if (!empty($_GET['peng'])) {
    $param = $_GET['peng'];
    $folder = $allowedDirs['peng'];
} elseif (!empty($_GET['adm'])) {
    $param = $_GET['adm'];
    $folder = $allowedDirs['adm'];
}

if ($param && $folder) {
    // Hanya mengizinkan berberapa tipe ekstensi file
    $allowedExtensions = ['zip', 'pdf', 'docx', 'jpg', 'png'];
    $fileName = basename($param); // strips directory info
    $filePath = $folder . $fileName;

    // Validasi ekstensi
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($fileExt, $allowedExtensions)) {
        die("Tipe file tidak diizinkan.");
    }

    // Check apabila file ada
    if (file_exists($filePath)) {
        // Memasang content type yang sesuai
        $mimeType = mime_content_type($filePath);

        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=\"" . htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8') . "\"");
        header("Content-Type: " . $mimeType);
        header("Content-Transfer-Encoding: binary");

        readfile($filePath);
        exit;
    } else {
        echo "File tidak ditemukan.";
    }
} else {
    echo "Request tidak valid!";
}

// // Tentukan folder file yang boleh di download
// $folder = "template/";
// // Lalu cek menggunakan fungsi file_exist
// if (!file_exists($folder.$_GET['url'])) {
//   echo "<h1>Access forbidden!</h1>
//       <p> Anda tidak diperbolehkan mendownload file ini.</p>";
//   exit;
// }

// // Apabila mendownload file di folder files
// else {
//   header("Content-Type: octet/stream");
//   header("Content-Disposition: attachment;
//   filename=\"".$_GET['url']."\"");
//   $fp = fopen($folder.$_GET['url'], "r");
//   $data = fread($fp, filesize($folder.$_GET['url']));
//   fclose($fp);
//   print $data;
// }

// if (isset($_GET['url'])) {
//     $filename = $_GET['url'];

//     $back_dir = "template/";
//     $file = $back_dir.$_GET['url'];

//     if (file_exists($file)) {
//         header('Content-Description: File Transfer');
//         header('Content-Type: application/octet-stream');
//         header('Content-Disposition: attachment; url="'.basename($file).'"');
//         header('Content-Transfer-Encoding: binary');
//         header('Expires: 0');
//         header('Cache-Control: private');
//         header('Pragma: private');
//         header('Content-Length: ' . filesize($file));
//         ob_clean();
//         flush();
//         readfile($file);
//         exit;
//     }else{
//         echo "<script>alert('Oops!File tidak ditemukan');</script>";
//         header("location: aturan_layanan.php");
//     }
// }
// $file = $_GET['url'];

// if (file_exists($file)) {
//     header('Content-Description: File Transfer');
//     header('Content-Type: application/octet-stream');
//     header('Content-Disposition: attachment; url="'.basename($file).'"');
//     header('Expires: 0');
//     header('Cache-Control: must-revalidate');
//     header('Pragma: public');
//     header('Content-Length: ' . filesize($file));
//     readfile($file);
//     exit;
// }
?>