<?php
session_start();
include('../../includes/config.php');
error_reporting(0);
if(($_SESSION['login'])==0)
  { 
header('location:../index.php');
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
    <link rel="shortcut icon" href="../../assets/images/logohimatif.png" />
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
                    <input type="text" class="form-control" placeholder="Cari nama atau penerima" name="cari" id="cari"
                      value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : ''; ?>">
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
                          <th scope="row"><?= $urut++; ?></th>
                          <td><?= htmlspecialchars($nama); ?></td>
                          <td><?= htmlspecialchars($tanggal); ?></td>
                          <td><?= htmlspecialchars($pemasukan); ?></td>
                          <td><?= htmlspecialchars($jumlah); ?></td>
                          <td><?= htmlspecialchars($penerima); ?></td>
                         
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>

                  <div class="table-responsive">
                    <table class="table table-bordered table-contextual">
                      <thead>
                        <tr class="table-warning">
                          <td>Total Kas Sesuai Filter: </td>
                          <td>Rp <?= number_format($totalFilteredKas, 2, ',', '.'); ?></td>
                        </tr>
                        <tr class="table-danger">
                          <td>Total Kas Keseluruhan: </td>
                          <td>Rp <?= number_format($totalKas, 2, ',', '.'); ?></td>
                        </tr>
                      </thead>
                    </table>
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
    $(document).ready(function () {
      $('#alert-error, #alert-success').delay(5000).fadeOut(500);
    });
  </script>
</body>
</html>

<?php
ob_end_flush(); // Mengakhiri output buffering dan mengirim output
?>
<?php } ?>