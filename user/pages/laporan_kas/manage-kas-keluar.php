<?php
// Memulai session yang disimpan pada browser
session_start();
ob_start(); // Memulai output buffering
include ('../../partials/config.php');

// Cek apakah sudah login, jika belum akan kembali ke form login
if ($_SESSION['status'] != "sudah_login") {
  // Melakukan pengalihan
  header("location:../../index.php");
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
$error = $sukses = $nama = $tanggal = $pengeluaran = $jumlah = $pengecek = "";

// Mendapatkan username dari session
$username = $_SESSION['username'];

if ($op == 'delete') {
  $id = $_GET['id'];
  $sql1 = "DELETE FROM kas_keluar WHERE id = '$id'";
  $q1 = mysqli_query($mysqli, $sql1);
  if ($q1) {
    $sukses = "Berhasil hapus data";
  } else {
    $error = "Gagal melakukan delete data";
  }
}

if ($op == 'edit') {
  $id = $_GET['id'];
  $sql1 = "SELECT * FROM kas_keluar WHERE id ='$id'";
  $q1 = mysqli_query($mysqli, $sql1);
  $r1 = mysqli_fetch_array($q1);
  $nama = $r1['nama'];
  $tanggal = $r1['tanggal'];
  $pengeluaran = $r1['pengeluaran'];
  $jumlah = $r1['jumlah'];
  $pengecek = $r1['pengecek'];

  if ($nama == '') {
    $error = "Data tidak ditemukan";
  }
}

if (isset($_POST["simpan"])) { // untuk create
  $nama = $_POST["nama"];
  $tanggal = $_POST["tanggal"];
  $pengeluaran = $_POST["pengeluaran"];
  $jumlah = $_POST["jumlah"];
  $pengecek = $_POST['pengecek'];

  if ($nama && $tanggal && $pengeluaran && $jumlah && $pengecek) {
    if ($op == 'edit') { // untuk update
      $sql1 = "UPDATE kas_keluar SET nama = '$nama', tanggal = '$tanggal', pengeluaran = '$pengeluaran', jumlah = '$jumlah', pengecek = '$pengecek', username = '$username' WHERE id = '$id'";
      $q1 = mysqli_query($mysqli, $sql1);
      if ($q1) {
        $sukses = "Data berhasil diupdate";
      } else {
        $error = "Data gagal diupdate";
      }
    } else { // untuk insert
      $sql1 = "INSERT INTO kas_keluar (nama, tanggal, pengeluaran, jumlah, pengecek, username) VALUES ('$nama', '$tanggal', '$pengeluaran', '$jumlah', '$pengecek', '$username')";
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
// Fetch data with filters
$where = [];
if (isset($_GET['filter_tanggal']) && $_GET['filter_tanggal'] != '') {
  $filter_tanggal = mysqli_real_escape_string($mysqli, $_GET['filter_tanggal']);
  $where[] = "tanggal = '$filter_tanggal'";
}
if (isset($_GET['cari']) && $_GET['cari'] != '') {
  $cari = mysqli_real_escape_string($mysqli, $_GET['cari']);
  $where[] = "(nama LIKE '%$cari%' OR pengecek LIKE '%$cari%')";
}
$whereSQL = '';
if (count($where) > 0) {
  $whereSQL = 'WHERE ' . implode(' AND ', $where);
}
$sql2 = "SELECT * FROM kas_keluar $whereSQL ORDER BY id DESC";
$q2 = mysqli_query($mysqli, $sql2);

// Calculate total kas
$totalKasQuery = "SELECT SUM(jumlah) as total_kas FROM kas_keluar";
$totalKasResult = mysqli_query($mysqli, $totalKasQuery);
$totalKasRow = mysqli_fetch_assoc($totalKasResult);
$totalKas = $totalKasRow['total_kas'];

// Calculate total filtered kas
$totalFilteredKasQuery = "SELECT SUM(jumlah) as total_filtered_kas FROM kas_keluar $whereSQL";
$totalFilteredKasResult = mysqli_query($mysqli, $totalFilteredKasQuery);
$totalFilteredKasRow = mysqli_fetch_assoc($totalFilteredKasResult);
$totalFilteredKas = $totalFilteredKasRow['total_filtered_kas'];

?>



<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Corona Admin</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="../../assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="../../assets/vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <!-- endinject -->
  <!-- Layout styles -->
  <link rel="stylesheet" href="../../assets/css/stylee.css">
  <!-- End layout styles -->
  <link rel="shortcut icon" href="../../assets/images/favicon.png" />
</head>

<body>
  <div class="container-scroller">
    <!-- partial:../../partials/_sidebar.html -->
    <nav class="sidebar sidebar-offcanvas" id="sidebar">
      <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
        <a class="sidebar-brand brand-logo" href="../../index.php"><img src="../../assets/images/logo.svg"
            alt="logo" /></a>
        <a class="sidebar-brand brand-logo-mini" href="../../index.php"><img src="../../assets/images/logo-mini.svg"
            alt="logo" /></a>
      </div>
      <ul class="nav">
        <li class="nav-item profile">
          <div class="profile-desc">
            <div class="profile-pic">
              <div class="count-indicator">
                <img class="img-xs rounded-circle " src="../../assets/images/faces/face15.jpg" alt="">
                <span class="count bg-success"></span>
              </div>
              <div class="profile-name">
                <h5 class="mb-0 font-weight-normal">Henry Klein</h5>
                <span>Gold Member</span>
              </div>
            </div>
            <a href="#" id="profile-dropdown" data-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>
            <div class="dropdown-menu dropdown-menu-right sidebar-dropdown preview-list"
              aria-labelledby="profile-dropdown">
              <a href="#" class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-dark rounded-circle">
                    <i class="mdi mdi-settings text-primary"></i>
                  </div>
                </div>
                <div class="preview-item-content">
                  <p class="preview-subject ellipsis mb-1 text-small">Account settings</p>
                </div>
              </a>
              <div class="dropdown-divider"></div>
              <a href="#" class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-dark rounded-circle">
                    <i class="mdi mdi-onepassword  text-info"></i>
                  </div>
                </div>
                <div class="preview-item-content">
                  <p class="preview-subject ellipsis mb-1 text-small">Change Password</p>
                </div>
              </a>
              <div class="dropdown-divider"></div>
              <a href="#" class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-dark rounded-circle">
                    <i class="mdi mdi-calendar-today text-success"></i>
                  </div>
                </div>
                <div class="preview-item-content">
                  <p class="preview-subject ellipsis mb-1 text-small">To-do list</p>
                </div>
              </a>
            </div>
          </div>
        </li>
        <li class="nav-item nav-category">
          <span class="nav-link">Navigation</span>
        </li>
        <li class="nav-item menu-items">
          <a class="nav-link" href="../../index.php">
            <span class="menu-icon">
              <i class="mdi mdi-speedometer"></i>
            </span>
            <span class="menu-title">Dashboard</span>
          </a>
        </li>
        <li class="nav-item menu-items">
          <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
            <span class="menu-icon">
              <i class="mdi mdi-laptop"></i>
            </span>
            <span class="menu-title">Kegiatan</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="ui-basic">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"><a class="nav-link" href="../../pages/ui-featuress/kegiatanku.php">Jadwal Kegiatan</a>
              </li>
              <li class="nav-item"><a class="nav-link" href="../../pages/ui-featuress/manage-kegiatan.php">Kelola
                  JadwalKegiatan</a>
              </li>
              <li class="nav-item"><a class="nav-link" href="../../pages/ui-features/typography.html">Typography</a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item menu-items">
          <a class="nav-link" data-toggle="collapse" href="#ui-advanced" aria-expanded="false"
            aria-controls="ui-advanced">
            <span class="menu-icon">
              <i class="mdi mdi-chart-line"></i>
            </span>
            <span class="menu-title">Keuangan</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="ui-advanced">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="../../pages/laporan_kas/laporankas.php">Laporan Kas</a>
              </li>
              <li class="nav-item"> <a class="nav-link" href="../../pages/laporan_kas/manage-kas.php">Kelola Kas Masuk</a></li>
              <li class="nav-item"> <a class="nav-link" href="../../pages/laporan_kas/manage-kas-keluar.php">Kelola Kas Keluar</a></li>
              <li class="nav-item"> <a class="nav-link" href="../../pages/ui-featuress/laporan-tahunan.php">Laporan
                  Tahunan</a></li>
            </ul>
          </div>
        </li>

        <li class="nav-item menu-items">
          <a class="nav-link" href="../../pages/forms/basic_elements.html">
            <span class="menu-icon">
              <i class="mdi mdi-playlist-play"></i>
            </span>
            <span class="menu-title">Form Elements</span>
          </a>
        </li>
        <li class="nav-item menu-items">
          <a class="nav-link" href="../../pages/tables/basic-table.html">
            <span class="menu-icon">
              <i class="mdi mdi-table-large"></i>
            </span>
            <span class="menu-title">Tables</span>
          </a>
        </li>
        <li class="nav-item menu-items">
          <a class="nav-link" href="../../pages/charts/chartjs.html">
            <span class="menu-icon">
              <i class="mdi mdi-chart-bar"></i>
            </span>
            <span class="menu-title">Charts</span>
          </a>
        </li>
        <li class="nav-item menu-items">
          <a class="nav-link" href="../../pages/icons/mdi.html">
            <span class="menu-icon">
              <i class="mdi mdi-contacts"></i>
            </span>
            <span class="menu-title">Icons</span>
          </a>
        </li>
        <li class="nav-item menu-items">
          <a class="nav-link" data-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
            <span class="menu-icon">
              <i class="mdi mdi-security"></i>
            </span>
            <span class="menu-title">User Pages</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="auth">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="../../pages/samples/blank-page.html"> Blank Page </a></li>
              <li class="nav-item"> <a class="nav-link" href="../../pages/samples/error-404.html"> 404 </a></li>
              <li class="nav-item"> <a class="nav-link" href="../../pages/samples/error-500.html"> 500 </a></li>
              <li class="nav-item"> <a class="nav-link" href="../../pages/samples/login.html"> Login </a></li>
              <li class="nav-item"> <a class="nav-link" href="../../pages/samples/register.html"> Register </a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item menu-items">
          <a class="nav-link"
            href="http://www.bootstrapdash.com/demo/corona-free/jquery/documentation/documentation.html">
            <span class="menu-icon">
              <i class="mdi mdi-file-document-box"></i>
            </span>
            <span class="menu-title">Documentation</span>
          </a>
        </li>
      </ul>
    </nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:../../partials/_navbar.html -->
      <nav class="navbar p-0 fixed-top d-flex flex-row">
        <div class="navbar-brand-wrapper d-flex d-lg-none align-items-center justify-content-center">
          <a class="navbar-brand brand-logo-mini" href="../../index.html"><img src="../../assets/images/logo-mini.svg"
              alt="logo" /></a>
        </div>
        <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
          <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
          </button>
          <ul class="navbar-nav w-100">
            <li class="nav-item w-100">
              <form class="nav-link mt-2 mt-md-0 d-none d-lg-flex search">
                <input type="text" class="form-control" placeholder="Search products">
              </form>
            </li>
          </ul>
          <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item dropdown d-none d-lg-block">
              <a class="nav-link btn btn-success create-new-button" id="createbuttonDropdown" data-toggle="dropdown"
                aria-expanded="false" href="#">+ Create New Project</a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                aria-labelledby="createbuttonDropdown">
                <h6 class="p-3 mb-0">Projects</h6>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-dark rounded-circle">
                      <i class="mdi mdi-file-outline text-primary"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <p class="preview-subject ellipsis mb-1">Software Development</p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-dark rounded-circle">
                      <i class="mdi mdi-web text-info"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <p class="preview-subject ellipsis mb-1">UI Development</p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-dark rounded-circle">
                      <i class="mdi mdi-layers text-danger"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <p class="preview-subject ellipsis mb-1">Software Testing</p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <p class="p-3 mb-0 text-center">See all projects</p>
              </div>
            </li>
            <li class="nav-item nav-settings d-none d-lg-block">
              <a class="nav-link" href="#">
                <i class="mdi mdi-view-grid"></i>
              </a>
            </li>
            <li class="nav-item dropdown border-left">
              <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#" data-toggle="dropdown"
                aria-expanded="false">
                <i class="mdi mdi-email"></i>
                <span class="count bg-success"></span>
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                aria-labelledby="messageDropdown">
                <h6 class="p-3 mb-0">Messages</h6>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="../../assets/images/faces/face4.jpg" alt="image" class="rounded-circle profile-pic">
                  </div>
                  <div class="preview-item-content">
                    <p class="preview-subject ellipsis mb-1">Mark send you a message</p>
                    <p class="text-muted mb-0"> 1 Minutes ago </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="../../assets/images/faces/face2.jpg" alt="image" class="rounded-circle profile-pic">
                  </div>
                  <div class="preview-item-content">
                    <p class="preview-subject ellipsis mb-1">Cregh send you a message</p>
                    <p class="text-muted mb-0"> 15 Minutes ago </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="../../assets/images/faces/face3.jpg" alt="image" class="rounded-circle profile-pic">
                  </div>
                  <div class="preview-item-content">
                    <p class="preview-subject ellipsis mb-1">Profile picture updated</p>
                    <p class="text-muted mb-0"> 18 Minutes ago </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <p class="p-3 mb-0 text-center">4 new messages</p>
              </div>
            </li>
            <li class="nav-item dropdown border-left">
              <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                data-toggle="dropdown">
                <i class="mdi mdi-bell"></i>
                <span class="count bg-danger"></span>
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                aria-labelledby="notificationDropdown">
                <h6 class="p-3 mb-0">Notifications</h6>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-dark rounded-circle">
                      <i class="mdi mdi-calendar text-success"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <p class="preview-subject mb-1">Event today</p>
                    <p class="text-muted ellipsis mb-0"> Just a reminder that you have an event today </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-dark rounded-circle">
                      <i class="mdi mdi-settings text-danger"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <p class="preview-subject mb-1">Settings</p>
                    <p class="text-muted ellipsis mb-0"> Update dashboard </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-dark rounded-circle">
                      <i class="mdi mdi-link-variant text-warning"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <p class="preview-subject mb-1">Launch Admin</p>
                    <p class="text-muted ellipsis mb-0"> New admin wow! </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <p class="p-3 mb-0 text-center">See all notifications</p>
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
                <div class="navbar-profile">
                  <img class="img-xs rounded-circle" src="../../assets/images/faces/face15.jpg" alt="">
                  <p class="mb-0 d-none d-sm-block navbar-profile-name">Henry Klein</p>
                  <i class="mdi mdi-menu-down d-none d-sm-block"></i>
                </div>
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                aria-labelledby="profileDropdown">
                <h6 class="p-3 mb-0">Profile</h6>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-dark rounded-circle">
                      <i class="mdi mdi-settings text-success"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <p class="preview-subject mb-1">Settings</p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-dark rounded-circle">
                      <i class="mdi mdi-logout text-danger"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <p class="preview-subject mb-1">Log out</p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <p class="p-3 mb-0 text-center">Advanced settings</p>
              </div>
            </li>
          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-toggle="offcanvas">
            <span class="mdi mdi-format-line-spacing"></span>
          </button>
        </div>
      </nav>
      <!-- partial -->
      <div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <?php if ($error) : ?>
              <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
              </div>
              <?php header("refresh:5;url=manage-kas-keluar.php"); // 5 detik 
            endif; ?>
            <?php if ($sukses) : ?>
              <div class="alert alert-success" role="alert">
                <?php echo $sukses; ?>
              </div>
              <?php header("refresh:5;url=manage-kas-keluar.php"); // 5 detik 
            endif; ?>
            <h4 class="card-title">KELOLA KAS KELUAR</h4>
            <p class="card-description">Basic form elements</p>
            <form action="" class="forms-sample" method="POST">
              <div class="form-group">
                <label for="exampleInputName1">Nama</label>
                <input type="text" class="form-control" id="exampleInputName1" name="nama" placeholder="Name" value="<?php echo htmlspecialchars($nama); ?>">
              </div>
              <div class="form-group">
                <label for="exampleInputEmail3">Tanggal</label>
                <input type="date" class="form-control" id="exampleInputEmail3" name="tanggal" placeholder="Tanggal" value="<?php echo htmlspecialchars($tanggal); ?>">
              </div>
              <div class="form-group">
                <label for="exampleInputPassword4">Pengeluaran</label>
                <input type="text" class="form-control" id="exampleInputPassword4" name="pengeluaran" placeholder="Pengeluaran" value="<?php echo htmlspecialchars($pengeluaran); ?>">
              </div>
              <div class="form-group">
                <label for="exampleInputCity1">Jumlah</label>
                <input type="text" class="form-control" id="exampleInputCity1" name="jumlah" placeholder="Jumlah" value="<?php echo htmlspecialchars($jumlah); ?>">
              </div>
              <div class="form-group">
                <label for="exampleInputCity1">Pengecek</label>
                <input type="text" class="form-control" id="exampleInputCity1" name="pengecek" placeholder="Pengecek" value="<?php echo htmlspecialchars($pengecek); ?>">
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
            <h4 class="card-title">TABEL KAS KELUAR</h4>
            <p class="card-description">Add class <code>.table</code></p>
            <form class="form-inline mb-3" method="GET" action="manage-kas-keluar.php">
              <div class="input-group mb-2 mr-sm-2">
                <div class="input-group-prepend">
                  <span class="input-group-text">Pilih tanggal</span>
                </div>
                <input type="date" class="form-control" name="filter_tanggal" id="filter_tanggal" value="<?= isset($_GET['filter_tanggal']) ? htmlspecialchars($_GET['filter_tanggal']) : ''; ?>">
              </div>
              <div class="input-group mb-2 mr-sm-2">
                <input type="hidden" id="username" value="<?= htmlspecialchars($ambilNama); ?>">
              </div>
              <div class="input-group mb-2 mr-sm-2">
                <div class="input-group-prepend">
                  <span class="input-group-text">Search</span>
                </div>
                <input type="text" class="form-control" id="inlineFormInputGroupUsername2" name="cari" placeholder="Search" value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : ''; ?>" autocomplete="off">
              </div>
              <button type="submit" class="btn btn-primary mb-2">Submit</button>
            </form>


            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Pengeluaran</th>
                    <th>Jumlah</th>
                    <th>Pengecek</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $urut = 1;
                  while ($r2 = mysqli_fetch_array($q2)) {
                    $id = $r2["id"];
                    $nama = $r2["nama"];
                    $tanggal = $r2["tanggal"];
                    $pengeluaran = $r2["pengeluaran"];
                    $jumlah = $r2["jumlah"];
                    $pengecek = $r2["pengecek"];
                    ?>
                    <tr>
                      <th scope="row"><?php echo $urut++; ?></th>
                      <td><?php echo htmlspecialchars($nama); ?></td>
                      <td><?php echo htmlspecialchars($tanggal); ?></td>
                      <td><?php echo htmlspecialchars($pengeluaran); ?></td>
                      <td><?php echo htmlspecialchars($jumlah); ?></td>
                      <td><?php echo htmlspecialchars($pengecek); ?></td>
                      <td>
                        <a href="manage-kas-keluar.php?op=edit&id=<?php echo $id; ?>"><button type="button" class="badge badge-warning">Edit</button></a>
                        <a href="manage-kas-keluar.php?op=delete&id=<?php echo $id; ?>" onclick="return confirm('Yakin mau delete data?')"><button type="button" class="badge badge-danger">Delete</button></a>
                      </td>
                    </tr>
                    <?php
                  }
                  ?>
                  

                </tbody>
        
              </table>
              <br>
              <div class="table-responsive">
                    <table class="table table-bordered table-contextual">
                      <thead>
                        <tr class="table-warning">
                          <td>Total Kas Sesuai Filter: </td>
                          <td> Rp <?php echo number_format($totalFilteredKas, 2, ',', '.'); ?> </td>
                        </tr>
                        <tr class="table-danger">
                          <td> Total Kas Keseluruhan: </td>
                          <td> Rp <?php echo number_format($totalKas, 2, ',', '.'); ?> </td>
                        </tr>
                        
                      </tbody>
                    </table>
                  </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © bootstrapdash.com
              2020</span>
            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Free <a
                href="https://www.bootstrapdash.com/bootstrap-admin-template/" target="_blank">Bootstrap admin
                templates</a> from Bootstrapdash.com</span>
          </div>
        </footer>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- Required JS files -->
  <!-- plugins:js -->
  <script src="../../assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="../../assets/vendors/chart.js/Chart.min.js"></script>
  <script src="../../assets/vendors/progressbar.js/progressbar.min.js"></script>
  <script src="../../assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
  <script src="../../assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
  <script src="../../assets/vendors/owl-carousel-2/owl.carousel.min.js"></script>
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="../../assets/js/off-canvas.js"></script>
  <script src="../../assets/js/hoverable-collapse.js"></script>
  <script src="../../assets/js/misc.js"></script>
  <script src="../../assets/js/settings.js"></script>
  <script src="../../assets/js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page -->
  <script src="../../assets/js/dashboard.js"></script>
  <!-- End custom js for this page -->
</body>

</html>

<?php
ob_end_flush(); // Mengakhiri output buffering dan mengirim output
?>