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
        $sql1 = "SELECT * FROM kegiatan WHERE id = ?";
        $stmt1 = $con->prepare($sql1);
        $stmt1->bind_param('i', $id);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        $r1 = $result1->fetch_assoc();

        if ($r1) {
            $nama_kegiatan = $r1['nama_kegiatan'];
            $tanggal = $r1['tanggal'];
            $waktu = $r1['waktu'];
            $tempat = $r1['tempat'];
            $penyelenggara = $r1['penyelenggara'];
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
    $where[] = "(nama_kegiatan LIKE '%$cari%' OR penyelenggara LIKE '%$cari%')";
}
$whereSQL = '';
if (count($where) > 0) {
    $whereSQL = 'WHERE ' . implode(' AND ', $where);
}
$sql2 = "SELECT * FROM kegiatan $whereSQL ORDER BY id DESC";
$q2 = $con->query($sql2);

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
              <!-- includes/topheader -->
              <?php include('../includes/topheader.php');?>
      <!--TopHeader End-->
      <!-- partial:../../partials/_navbar.html -->

      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">TABEL KEGIATAN</h4>
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
                      <input type="text" class="form-control" placeholder="Cari nama kegiatan atau penyelenggara" name="cari"
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
                          <th>Nama Kegiatan</th>
                          <th>Tanggal</th>
                          <th>Waktu</th>
                          <th>Tempat</th>
                          <th>Penyelenggara</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $urut = 1;
                        while ($r2 = $q2->fetch_assoc()) {
                          $id = $r2["id"];
                          $nama_kegiatan = $r2["nama_kegiatan"];
                          $tanggal = $r2["tanggal"];
                          $waktu = $r2["waktu"];
                          $tempat = $r2["tempat"];
                          $penyelenggara = $r2["penyelenggara"];
                        ?>
                        <tr>
                          <th scope="row"><?php echo $urut++; ?></th>
                          <td><?php echo htmlspecialchars($nama_kegiatan); ?></td>
                          <td><?php echo htmlspecialchars($tanggal); ?></td>
                          <td><?php echo htmlspecialchars($waktu); ?></td>
                          <td><?php echo htmlspecialchars($tempat); ?></td>
                          <td><?php echo htmlspecialchars($penyelenggara); ?></td>
                        
                        </tr>
                        <?php } ?>
                      </tbody>
                    </table>

                    <div class="table-responsive">
                      <table class="table table-bordered table-contextual">
                        <thead>
                          <tr class="table-warning">
                            <td>Total Kegiatan Sesuai Filter: </td>
                            <td><?= $q2->num_rows; ?></td>
                          </tr>
                          <tr class="table-danger">
                            <td>Total Kegiatan Keseluruhan: </td>
                            <td><?php
                              $totalSql = "SELECT COUNT(*) AS total FROM kegiatan";
                              $totalResult = $con->query($totalSql);
                              $totalRow = $totalResult->fetch_assoc();
                              echo $totalRow['total'];
                            ?></td>
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