<?php
session_start();
include "../koneksi.php";

$allowed_dirs = [
    'url' => __DIR__ . '/template',
    'pengaduan' => '../upload-file/pengaduan/',
    'administrasi' => '../upload-file/administrasi/'
];

// Validasi input parameter
$type = isset($_GET['type']) ? $_GET['type'] : null;
if (!array_key_exists($type, $allowed_dirs)) {
    die("Invalid download type");
}

if (empty($_GET['file'])) {
    die("No file specified");
}

$fileName = basename(path: $_GET['file']);
$filePath = $allowed_dirs[$type] . $fileName;

if (!file_exists($filePath)) {
    echo $filePath . "<br>";
    die("File not found");
}

if (!is_file($filePath)) {
    die("Invalid file");
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $filePath);
finfo_close($finfo);

// Kirim file header
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=\"" . htmlspecialchars($fileName) . "\"");
header("Content-Type: " . $mime);
header("Content-Length: " . filesize($filePath));

readfile($filePath);
exit;

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