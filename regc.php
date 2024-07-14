<?php 
// Mengaktifkan session PHP
session_start();

// Menghubungkan dengan koneksi
include 'inc/config.php';

// Menangkap data yang dikirim dari form
$username = $_POST['nim'];
$password = md5($_POST['password']);

// Mengecek apakah NIM ada dan belum diaktifkan
$data = mysqli_query($con,"SELECT * FROM tblusers WHERE nim='$username' AND Is_Active=0");

// Menghitung jumlah data yang ditemukan
$cek = mysqli_num_rows($data);

// Jika ditemukan
if($cek > 0){
    // Mengupdate password dan mengaktifkan akun
    $update = mysqli_query($con,"UPDATE tblusers SET password='$password', Is_Active=1 WHERE nim='$username'");
    // Jika update berhasil
    if($update){
        header("location:register.php?pesan=sukses");
    } else {
        // Jika update gagal
        header("location:register.php?pesan=gagal");
    }
} else {
    // Jika NIM tidak ditemukan atau akun sudah diaktifkan
    header("location:register.php?pesan=gagal");
}
?>
