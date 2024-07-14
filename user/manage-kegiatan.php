<?php
// Memulai session yang disimpan pada browser
session_start();
ob_start(); // Memulai output buffering
include('partials/config.php');

// Cek apakah sudah login, jika belum akan kembali ke form login
if ($_SESSION['status'] != "sudah_login") {
    // Melakukan pengalihan
    header("location:index.php");
    exit();
}

// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db = "login";

$mysqli = mysqli_connect($host, $user, $pass, $db);

if (!$mysqli) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$op = isset($_GET["op"]) ? $_GET["op"] : "";
$error = $sukses = $nama_kegiatan = $tanggal = $waktu = $tempat = $penyelenggara = "";

// Mendapatkan username dari session
$username = $_SESSION['username'];

if ($op == 'delete') {
    $id = $_GET['id'];
    $sql1 = "DELETE FROM kegiatan WHERE id = '$id'";
    $q1 = mysqli_query($mysqli, $sql1);
    if ($q1) {
        $sukses = "Berhasil hapus data";
    } else {
        $error = "Gagal melakukan delete data";
    }
}

if ($op == 'edit') {
    $id = $_GET['id'];
    $sql1 = "SELECT * FROM kegiatan WHERE id ='$id'";
    $q1 = mysqli_query($mysqli, $sql1);
    $r1 = mysqli_fetch_array($q1);
    $nama_kegiatan = $r1['nama_kegiatan'];
    $tanggal = $r1['tanggal'];
    $waktu = $r1['waktu'];
    $tempat = $r1['tempat'];
    $penyelenggara = $r1['penyelenggara'];

    if ($nama_kegiatan == '') {
        $error = "Data tidak ditemukan";
    }
}

if (isset($_POST["simpan"])) { // untuk create
    $nama_kegiatan = $_POST["nama_kegiatan"];
    $tanggal = $_POST["tanggal"];
    $waktu = $_POST["waktu"];
    $tempat = $_POST["tempat"];
    $penyelenggara = $_POST['penyelenggara'];

    if ($nama_kegiatan && $tanggal && $waktu && $tempat && $penyelenggara) {
        if ($op == 'edit') { // untuk update
            $sql1 = "UPDATE kegiatan SET nama_kegiatan = '$nama_kegiatan', tanggal = '$tanggal', waktu = '$waktu', tempat = '$tempat', penyelenggara = '$penyelenggara', username = '$username' WHERE id = '$id'";
            $q1 = mysqli_query($mysqli, $sql1);
            if ($q1) {
                $sukses = "Data berhasil diupdate";
            } else {
                $error = "Data gagal diupdate";
            }
        } else { // untuk insert
            $sql1 = "INSERT INTO kegiatan (nama_kegiatan, tanggal, waktu, tempat, penyelenggara, username) VALUES ('$nama_kegiatan', '$tanggal', '$waktu', '$tempat', '$penyelenggara', '$username')";
            $q1 = mysqli_query($mysqli, $sql1);
            if ($q1) {
                $sukses = "Berhasil memasukkan data baru";
            } else {
                $error = "Gagal memasukkan data";
            }
        }
    } else {
        $error = "Silakan masukkan semua data";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Corona Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="assets/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel-2/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel-2/owl.theme.default.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />
</head>

<body>

    <div class="container-scroller">
        <?php include_once('partials/_navbar.php'); ?>
        <?php include_once('partials/_sidebar.php'); ?>
        
        <div class="main-panel">
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <?php if ($error) { ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $error ?>
                                    </div>
                                    <?php header("refresh:5;url=manage-kegiatan.php"); //5 detik 
                                } ?>
                                <?php if ($sukses) { ?>
                                    <div class="alert alert-success" role="alert">
                                        <?php echo $sukses ?>
                                    </div>
                                    <?php header("refresh:5;url=manage-kegiatan.php"); //5 detik 
                                } ?>
                                <h4 class="card-title">Jadwal Kegiatan</h4>
                                <p class="card-description"> Basic form elements </p>
                                <form action="" class="forms-sample" method="POST">
                                    <div class="form-group">
                                        <label for="exampleInputName1">Nama Kegiatan</label>
                                        <input type="text" class="form-control" id="exampleInputName1" name="nama_kegiatan" placeholder="Name" value="<?php echo htmlspecialchars($nama_kegiatan); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail3">Tanggal</label>
                                        <input type="date" class="form-control" id="exampleInputEmail3" name="tanggal" placeholder="Tanggal" value="<?php echo htmlspecialchars($tanggal); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword4">Waktu</label>
                                        <input type="text" class="form-control" id="exampleInputPassword4" name="waktu" placeholder="Waktu" value="<?php echo htmlspecialchars($waktu); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputCity1">Tempat</label>
                                        <input type="text" class="form-control" id="exampleInputCity1" name="tempat" placeholder="Tempat" value="<?php echo htmlspecialchars($tempat); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputCity1">Penyelenggara</label>
                                        <input type="text" class="form-control" id="exampleInputCity1" name="penyelenggara" placeholder="Penyelenggara" value="<?php echo htmlspecialchars($penyelenggara); ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary mr-2" name="simpan">Submit</button>
                                    <button type="reset" class="btn btn-dark">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Basic Table</h4>
                                <p class="card-description"> Add class <code>.table</code> </p>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nama Kegiatan</th>
                                                <th>Tanggal</th>
                                                <th>Waktu</th>
                                                <th>Tempat</th>
                                                <th>Penyelenggara</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql2 = "SELECT * FROM kegiatan ORDER BY id DESC";
                                            $q2 = mysqli_query($mysqli, $sql2);
                                            $urut = 1;
                                            while ($r2 = mysqli_fetch_array($q2)) {
                                                $id = $r2["id"];
                                                $nama_kegiatan = $r2["nama_kegiatan"];
                                                $tanggal = $r2["tanggal"];
                                                $waktu = $r2["waktu"];
                                                $tempat = $r2["tempat"];
                                                $penyelenggara = $r2["penyelenggara"];
                                            ?>
                                                <tr>
                                                    <th scope="row"><?php echo $urut++ ?></th>
                                                    <td scope="row"><?php echo htmlspecialchars($nama_kegiatan); ?></td>
                                                    <td scope="row"><?php echo htmlspecialchars($tanggal); ?></td>
                                                    <td scope="row"><?php echo htmlspecialchars($waktu); ?></td>
                                                    <td scope="row"><?php echo htmlspecialchars($tempat); ?></td>
                                                    <td scope="row"><?php echo htmlspecialchars($penyelenggara); ?></td>
                                                    <td scope="row">
                                                        <a href="manage-kegiatan.php?op=edit&id=<?php echo $id ?>"><button type="button" class="badge badge-warning">Edit</button></a>
                                                        <a href="manage-kegiatan.php?op=delete&id=<?php echo $id ?>" onclick="return confirm('Yakin mau delete data?')"><button type="button" class="badge badge-danger">Delete</button></a>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
           
        </div>
        <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
    </div>
    <?php include('partials/_footer.php'); ?>
</body>

</html>

<?php
ob_end_flush(); // Mengakhiri output buffering dan mengirim output
?>
