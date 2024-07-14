<?php
session_start();
include ('includes/config.php');
error_reporting(0);
if (strlen($_SESSION['login']) == 0) {
  header('location:../login.php');
} else {

  // Ambil jumlah kegiatan dari database
  $result_kegiatan = mysqli_query($con, "SELECT COUNT(*) AS jumlah_kegiatan FROM kegiatan");
  $row_kegiatan = mysqli_fetch_assoc($result_kegiatan);
  $jumlah_kegiatan = $row_kegiatan['jumlah_kegiatan'];

  // Ambil jumlah kas dari database
  $result_kas = mysqli_query($con, "SELECT COUNT(*) AS jumlah_kas FROM kas_masuk");
  $row_kas = mysqli_fetch_assoc($result_kas);
  $jumlah_kas = $row_kas['jumlah_kas'];

  // Ambil jumlah anggota dari database
  $result_anggota = mysqli_query($con, "SELECT COUNT(*) AS jumlah_anggota FROM anggota");
  $row_anggota = mysqli_fetch_assoc($result_anggota);
  $jumlah_anggota = $row_anggota['jumlah_anggota'];

  ?>



  <!DOCTYPE html>
  <html lang="en">

  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Himatif</title>
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
    <link rel="stylesheet" href="assets/css/styleee.css">
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="assets/css/styleee.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/logohimatif.png" />
  </head>


  <body>
    <div class="container-scroller">
          <!-- ========== Left Sidebar Start ========== -->
    <?php include('includes/leftsidebar.php');?>
            <!-- Left Sidebar End -->

      <div class="container-fluid page-body-wrapper">
        <!-- includes/topheader -->
      <?php include('includes/topheader.php');?>
      <!--TopHeader End-->
      
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="row">
              <div class="col-sm-4 grid-margin">
                <div class="card" id="jadwal-kegiatan-card">
                  <div class="card-body">
                    <h5>Jadwal Kegiatan</h5>
                    <div class="row">
                      <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                          <h2 class="mb-0"><?php echo $jumlah_kegiatan; ?></h2>
                        </div>
                        <h6 class="text-muted font-weight-normal">Jumlah kegiatan</h6>
                      </div>
                      <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-calendar-clock text-warning ml-auto"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-4 grid-margin">
                <div class="card" id="tabel-kas-card">
                  <div class="card-body">
                    <h5>Tabel Kas</h5>
                    <div class="row">
                      <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                          <h2 class="mb-0"><?php echo $jumlah_kas; ?></h2>
                        </div>
                        <h6 class="text-muted font-weight-normal">Jumlah kas</h6>
                      </div>
                      <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-cash text-danger ml-auto"></i> <!-- Ikon diganti di sini -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-sm-4 grid-margin">
                <div class="card" id="anggota-card">
                  <div class="card-body">
                    <h5>Anggota</h5>
                    <div class="row">
                      <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                          <h2 class="mb-0"><?php echo $jumlah_anggota; ?></h2>
                        </div>
                        <h6 class="text-muted font-weight-normal">Jumlah anggota</h6>
                      </div>
                      <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-account-multiple text-info ml-auto"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <!-- Additional content can go here -->
            </div>
            <!-- content-wrapper ends -->
          </div>
          <script>
            // Menambahkan event listener pada kartu tabel kas
            document.getElementById("tabel-kas-card").addEventListener("click", function () {
              window.location.href = "pages/laporan_kas/laporankas.php";
            });

            // Menambahkan event listener pada kartu jadwal kegiatan
            document.getElementById("jadwal-kegiatan-card").addEventListener("click", function () {
              window.location.href = "pages/ui-featuress/kegiatanku.php";
            });

            // Menambahkan event listener pada kartu anggota
            document.getElementById("anggota-card").addEventListener("click", function () {
              window.location.href = "pages/anggota/anggota.php";
            });
          </script>
          <!-- partial:partials/_footer.html -->
          <footer class="footer">
            <!-- Footer content -->
          </footer>
      </div>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->

      <!-- page-body-wrapper ends -->

    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="assets/vendors/chart.js/Chart.min.js"></script>
    <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="assets/vendors/owl-carousel-2/owl.carousel.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="assets/js/dashboard.js"></script>
    <!-- End custom js for this page -->
  </body>

  </html><?php } ?>