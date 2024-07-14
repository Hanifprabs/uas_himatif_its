<?php 
// Mengaktifkan session PHP
session_start();

// Menghubungkan dengan koneksi
include 'inc/config.php';

// Menangkap data yang dikirim dari form
$nim = $_POST['nim'];
$password = md5($_POST['password']);

// Mencari data di database berdasarkan nim dan password yang dikirim
$login = mysqli_query($con, "SELECT * FROM tblusers WHERE nim='$nim' AND password='$password'");
// Menghitung jumlah data yang ditemukan
$cek = mysqli_num_rows($login);

// Cek apakah nim dan password ditemukan di database
if($cek > 0){
    $data = mysqli_fetch_assoc($login);
    
    // Set session berdasarkan tipe user
    $_SESSION['login'] = $nim;
    $_SESSION['id'] = $data['id'];
    $_SESSION['nim'] = $nim;
    $_SESSION['type'] = $data['type'];
    
    // Alihkan user ke halaman dashboard sesuai tipe user
    switch($data['type']) {
        case "admin":
            header("location:admin");
            break;
        case "anggota":
            header("location:user");
            break;
        case "pengurus":
            header("location:user");
            break;
        default:
            header("location:login.php?pesan=gagal");
            break;
    }
} else {
    // Jika nim atau password tidak ditemukan, alihkan ke halaman login dengan pesan gagal
    header("location:login.php?pesan=gagal");
}
?>
