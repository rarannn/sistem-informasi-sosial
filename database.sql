CREATE DATABASE IF NOT EXISTS manajemen_sosial;
USE manajemen_sosial;

CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    alamat TEXT,
    tlp VARCHAR(20),
    level ENUM('warga', 'petugas') NOT NULL,
    nip VARCHAR(20) NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS layanan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    jenis VARCHAR(255),
    spesifikasi TEXT
);

CREATE TABLE IF NOT EXISTS feedback (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userId VARCHAR(255),
    tanggapan TEXT,
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS aturan_layanan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_layanan VARCHAR(255),
    aturan TEXT,
    template_data VARCHAR(255),
    petugas VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS kepengurusan (
    id VARCHAR(255) PRIMARY KEY,
    nip VARCHAR(255),
    foto VARCHAR(255),
    nama VARCHAR(255),
    jabatan VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS artikel (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userId VARCHAR(255),
    nama VARCHAR(255),
    kategori VARCHAR(255),
    link VARCHAR(255),
    gambar VARCHAR(255),
    judul VARCHAR(255),
    deskripsi TEXT,
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS administrasi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userId VARCHAR(255),
    nama VARCHAR(255),
    jenis VARCHAR(255),
    deskripsi TEXT,
    `data` VARCHAR(255),
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pengaduan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userId VARCHAR(255),
    nama VARCHAR(255),
    jenis VARCHAR(255),
    deskripsi TEXT,
    `data` VARCHAR(255),
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS request (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userId VARCHAR(255),
    nama VARCHAR(255),
    jenis VARCHAR(255),
    request TEXT,
    alasan TEXT
);

CREATE TABLE IF NOT EXISTS hasil_administrasi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    administrasiId VARCHAR(255),
    nama VARCHAR(255),
    deskripsi TEXT,
    file VARCHAR(255),
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    petugas VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS hasil_pengaduan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pengaduanId VARCHAR(255),
    nama VARCHAR(255),
    deskripsi TEXT,
    file VARCHAR(255),
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    petugas VARCHAR(255)
);