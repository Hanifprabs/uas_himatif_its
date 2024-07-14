<?php
session_start();
include('../../includes/config.php');
error_reporting(0);
if(($_SESSION['type'])=="anggota")
  { 
echo "hanya untuk pengurus";;
}
else{

$op = isset($_GET['op']) ? $_GET['op'] : '';
$sukses = "";
$error = "";

// Fetch data for editing if 'op' is 'edit'
if ($op == 'edit') {
    $id = $_GET['id'] ?? '';
    if ($id) {
        $sql1 = "SELECT * FROM kas_masuk WHERE id = ?";
        $stmt1 = $con->prepare($sql1);
        $stmt1->bind_param('i', $id);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        $r1 = $result1->fetch_assoc();

        if ($r1) {
            $nama = $r1['nama'];
            $tanggal = $r1['tanggal'];
            $pemasukan = $r1['pemasukan'];
            $jumlah = $r1['jumlah'];
            $penerima = $r1['penerima'];
        } else {
            $error = "Data tidak ditemukan";
        }
    } else {
        $error = "ID tidak ditemukan";
    }
}

// Handle form submission
if (isset($_POST["simpan"])) {
    $nama = trim($_POST["nama"] ?? '');
    $tanggal = trim($_POST["tanggal"] ?? '');
    $pemasukan = trim($_POST["pemasukan"] ?? '');
    $jumlah = trim($_POST["jumlah"] ?? '');
    $penerima = trim($_POST['penerima'] ?? '');

    // Get username from session
    $username = $_SESSION['username'] ?? '';

    // Validate the input data
    if (!$nama || !$tanggal || !$pemasukan || !$jumlah || !$penerima) {
        $error = "Silakan masukkan semua data";
    } elseif (!is_numeric($jumlah) || $jumlah <= 0) {
        $error = "Jumlah harus berupa angka positif";
    } elseif (!DateTime::createFromFormat('Y-m-d', $tanggal)) {
        $error = "Format tanggal tidak valid. Gunakan format YYYY-MM-DD.";
    } else {
        if ($op == 'edit' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $sql1 = "UPDATE kas_masuk SET nama = ?, tanggal = ?, pemasukan = ?, jumlah = ?, penerima = ? WHERE id = ?";
            $stmt1 = $con->prepare($sql1);
            $stmt1->bind_param('sssssi', $nama, $tanggal, $pemasukan, $jumlah, $penerima, $id);
            $q1 = $stmt1->execute();

            if ($q1) {
                $sukses = "Data berhasil diupdate";
            } else {
                $error = "Data gagal diupdate: " . $stmt1->error;
                error_log($error, 3, 'error_log.txt');  // Log error ke file error_log.txt
            }
        } else {
            $sql1 = "INSERT INTO kas_masuk (nama, tanggal, pemasukan, jumlah, penerima) VALUES (?, ?, ?, ?, ?)";
            $stmt1 = $con->prepare($sql1);
            $stmt1->bind_param('sssss', $nama, $tanggal, $pemasukan, $jumlah, $penerima);
            $q1 = $stmt1->execute();

            if ($q1) {
                $sukses = "Berhasil memasukkan data baru";
            } else {
                $error = "Gagal memasukkan data: " . $stmt1->error;
                error_log($error, 3, 'error_log.txt');  // Log error ke file error_log.txt
            }
        }
    }
}

// Handle deletion
if ($op == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($id) {
        $sql_delete = "DELETE FROM kas_masuk WHERE id = ?";
        $stmt_delete = $con->prepare($sql_delete);
        $stmt_delete->bind_param('i', $id);
        $delete_result = $stmt_delete->execute();

        if ($delete_result) {
            $sukses = "Data berhasil dihapus";
        } else {
            $error = "Gagal menghapus data: " . $stmt_delete->error;
        }
    } else {
        $error = "ID tidak ditemukan";
    }
}

// Fetch data with filters
$where = [];
if (isset($_GET['filter_tanggal']) && $_GET['filter_tanggal'] != '') {
    $filter_tanggal = $con->real_escape_string($_GET['filter_tanggal']);
    $where[] = "tanggal = '$filter_tanggal'";
}
if (isset($_GET['cari']) && $_GET['cari'] != '') {
    $cari = $con->real_escape_string($_GET['cari']);
    $where[] = "(nama LIKE '%$cari%' OR penerima LIKE '%$cari%')";
}
$whereSQL = '';
if (count($where) > 0) {
    $whereSQL = 'WHERE ' . implode(' AND ', $where);
}
$sql2 = "SELECT * FROM kas_masuk $whereSQL ORDER BY id DESC";
$q2 = $con->query($sql2);

// Calculate total kas
$totalKasQuery = "SELECT SUM(jumlah) as total_kas FROM kas_masuk";
$totalKasResult = $con->query($totalKasQuery);
$totalKasRow = $totalKasResult->fetch_assoc();
$totalKas = $totalKasRow['total_kas'];

// Calculate total filtered kas
$totalFilteredKasQuery = "SELECT SUM(jumlah) as total_filtered_kas FROM kas_masuk $whereSQL";
$totalFilteredKasResult = $con->query($totalFilteredKasQuery);
$totalFilteredKasRow = $totalFilteredKasResult->fetch_assoc();
$totalFilteredKas = $totalFilteredKasRow['total_filtered_kas'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
 <!-- Required meta tags -->
 <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Himatif</title>
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
                     <!-- ========== Left Sidebar Start ========== -->
                     <?php include('../includes/leftsidebar.php');?>
            <!-- Left Sidebar End -->
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:../../partials/_navbar.html -->
      <!-- includes/topheader -->
      <?php include('../includes/topheader.php');?>
      <!--TopHeader End-->
          <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <?php if ($error): ?>
                                        <div id="alert-error" class="alert alert-danger" role="alert">
                                            <?php echo htmlspecialchars($error); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($sukses): ?>
                                        <div id="alert-success" class="alert alert-success" role="alert">
                                            <?php echo htmlspecialchars($sukses); ?>
                                        </div>
                                    <?php endif; ?>
                                    <h4 class="card-title">KELOLA KAS MASUK</h4>
                                    <p class="card-description">Basic form elements</p>
                                    <form action="" class="forms-sample" method="POST">
                                        <div class="form-group">
                                            <label for="exampleInputName1">Nama</label>
                                            <input type="text" class="form-control" id="exampleInputName1" name="nama" placeholder="Name"
                                                value="<?= htmlspecialchars($nama ?? ''); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail3">Tanggal</label>
                                            <input type="date" class="form-control" id="exampleInputEmail3" name="tanggal"
                                                placeholder="Tanggal" value="<?= htmlspecialchars($tanggal ?? ''); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputPassword4">Pemasukan</label>
                                            <input type="text" class="form-control" id="exampleInputPassword4" name="pemasukan"
                                                placeholder="Pemasukan" value="<?= htmlspecialchars($pemasukan ?? ''); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputCity1">Jumlah</label>
                                            <input type="text" class="form-control" id="exampleInputCity1" name="jumlah" placeholder="Jumlah"
                                                value="<?= htmlspecialchars($jumlah ?? ''); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputCity1">Penerima</label>
                                            <input type="text" class="form-control" id="exampleInputCity1" name="penerima"
                                                placeholder="Penerima" value="<?= htmlspecialchars($penerima ?? ''); ?>">
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
                                    <h4 class="card-title">TABEL KAS</h4>
                                    <p class="card-description">Add class <code>.table</code></p>
                                    <form action="" method="GET">
                                        <div class="input-group mb-2 mr-sm-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Pilih tanggal</span>
                                            </div>
                                            <input type="date" class="form-control" name="filter_tanggal" id="filter_tanggal"
                                                value="<?= isset($_GET['filter_tanggal']) ? htmlspecialchars($_GET['filter_tanggal']) : ''; ?>">
                                        </div>
                                        <div class="input-group mb-2 mr-sm-2">
                                            <input type="hidden" id="nim" value="<?= htmlspecialchars($nim ?? ''); ?>">
                                        </div>
                                        <div class="input-group mb-2 mr-sm-2">
                                            <input type="text" class="form-control" placeholder="Cari nama atau penerima" name="cari"
                                                id="cari" value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : ''; ?>">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama</th>
                                                    <th>Tanggal</th>
                                                    <th>Pemasukan</th>
                                                    <th>Jumlah</th>
                                                    <th>Penerima</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $urut = 1;
                                                while ($r2 = $q2->fetch_assoc()) {
                                                    $id = $r2["id"];
                                                    $nama = $r2["nama"];
                                                    $tanggal = $r2["tanggal"];
                                                    $pemasukan = $r2["pemasukan"];
                                                    $jumlah = $r2["jumlah"];
                                                    $penerima = $r2["penerima"];
                                                ?>
                                                <tr>
                                                    <th scope="row"><?php echo $urut++; ?></th>
                                                    <td><?php echo htmlspecialchars($nama); ?></td>
                                                    <td><?php echo htmlspecialchars($tanggal); ?></td>
                                                    <td><?php echo htmlspecialchars($pemasukan); ?></td>
                                                    <td><?php echo htmlspecialchars($jumlah); ?></td>
                                                    <td><?php echo htmlspecialchars($penerima); ?></td>
                                                    <td>
                                                        <a href="manage-kas.php?op=edit&id=<?php echo $id; ?>"><button type="button"
                                                                class="badge badge-warning">Edit</button></a>
                                                        <a href="manage-kas.php?op=delete&id=<?php echo $id; ?>"
                                                            onclick="return confirm('Yakin akan menghapus data?')"><button type="button"
                                                                class="badge badge-danger">Hapus</button></a>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-contextual">
                                                <thead>
                                                    <tr class="table-warning">
                                                        <td>Total Kas Sesuai Filter: </td>
                                                        <td>Rp <?php echo number_format($totalFilteredKas, 2, ',', '.'); ?> </td>
                                                    </tr>
                                                    <tr class="table-danger">
                                                        <td> Total Kas Keseluruhan: </td>
                                                        <td> Rp <?php echo number_format($totalKas, 2, ',', '.'); ?> </td>
                                                    </tr>
                                                </thead>
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
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Your custom JavaScript -->
    <script>
    $(document).ready(function() {
        $('#alert-error, #alert-success').delay(5000).fadeOut(500);
    });
    </script>
</body>

</html>

<?php
ob_end_flush(); // Mengakhiri output buffering dan mengirim output
?>
<?php } ?>
