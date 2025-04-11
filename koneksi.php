<?php
    $host = "db";
    $username = getenv("MYSQL_USER");
    $password = getenv("MYSQL_PASSWORD");
    $database = getenv("MYSQL_DATABASE");
    $koneksi = mysqli_connect($host, $username, $password, $database);
    if (!$koneksi) {
        // die( mysqli_connect_error()); // DEBUGGING PURPOSES ONLY!
        // die("<script>alert('Gagal tersambung dengan database.')</script>");
        exit;
    }
    
    // $host = "";
    // $username = "root";
    // $password = "";
    // $database = "manajemen_sosial";
    // $koneksi = mysqli_connect($host, $username, $password, $database);
    // if (!$koneksi) {
    //     die("<script>alert('Gagal tersambung dengan database.')</script>");
    // }
?>