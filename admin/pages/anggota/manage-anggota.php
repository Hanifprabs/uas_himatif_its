<?php
session_start();
include('../../includes/config.php');
error_reporting(0);

if ($_SESSION['type'] == "anggota") { 
    echo "hanya untuk pengurus";
    exit;
}
else {

$op = isset($_GET['op']) ? $_GET['op'] : '';
$sukses = "";
$error = "";

// Fetch data for editing if 'op' is 'edit'
if ($op == 'edit') {
    $id = $_GET['id'] ?? '';
    if ($id) {
        $sql1 = "SELECT * FROM anggota WHERE id = ?";
        $stmt1 = $con->prepare($sql1);
        $stmt1->bind_param('i', $id);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        $r1 = $result1->fetch_assoc();

        if ($r1) {
            $nama = $r1['nama'];
            $nim = $r1['nim'];
            $jenis_kelamin = $r1['jenis_kelamin'];
            $angkatan = $r1['angkatan'];
            $jabatan = $r1['jabatan'];
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
    $nim = trim($_POST["nim"] ?? '');
    $jenis_kelamin = trim($_POST["jenis_kelamin"] ?? '');
    $angkatan = trim($_POST["angkatan"] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');

    // Validate the input data
    if (!$nama || !$nim || !$jenis_kelamin || !$angkatan || !$jabatan) {
        $error = "Silakan masukkan semua data";
    } else {
        if ($op == 'edit' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $sql1 = "UPDATE anggota SET nama = ?, nim = ?, jenis_kelamin = ?, angkatan = ?, jabatan = ? WHERE id = ?";
            $stmt1 = $con->prepare($sql1);
            $stmt1->bind_param('sssssi', $nama, $nim, $jenis_kelamin, $angkatan, $jabatan, $id);
            $q1 = $stmt1->execute();

            if ($q1) {
                $sukses = "Data berhasil diupdate";
            } else {
                $error = "Data gagal diupdate: " . $stmt1->error;
                error_log($error, 3, 'error_log.txt');  // Log error ke file error_log.txt
            }
        } else {
            $sql1 = "INSERT INTO anggota (nama, nim, jenis_kelamin, angkatan, jabatan) VALUES (?, ?, ?, ?, ?)";
            $stmt1 = $con->prepare($sql1);
            $stmt1->bind_param('sssss', $nama, $nim, $jenis_kelamin, $angkatan, $jabatan);
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
        $sql_delete = "DELETE FROM anggota WHERE id = ?";
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
if (isset($_GET['cari']) && $_GET['cari'] != '') {
    $cari = $con->real_escape_string($_GET['cari']);
    $where[] = "(nama LIKE '%$cari%' OR nim LIKE '%$cari%' OR jabatan LIKE '%$cari%')";
}
$whereSQL = '';
if (count($where) > 0) {
    $whereSQL = 'WHERE ' . implode(' AND ', $where);
}
$sql2 = "SELECT * FROM anggota $whereSQL ORDER BY id DESC";
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
      <!-- partial:../../partials/_navbar.html -->
      <!-- includes/topheader -->
      <?php include('../includes/topheader.php');?>
      <!--TopHeader End-->
        <!-- main-panel starts -->
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
            <h4 class="card-title">KELOLA ANGGOTA</h4>
            <p class="card-description">Basic form elements</p>
            <form action="" class="forms-sample" method="POST">
              <div class="form-group">
                <label for="exampleInputName1">Nama</label>
                <input type="text" class="form-control" id="exampleInputName1" name="nama" placeholder="Nama"
                  value="<?= htmlspecialchars($nama ?? ''); ?>">
              </div>
              <div class="form-group">
                <label for="exampleInputEmail3">NIM</label>
                <input type="text" class="form-control" id="exampleInputEmail3" name="nim"
                  placeholder="NIM" value="<?= htmlspecialchars($nim ?? ''); ?>">
              </div>
              <div class="form-group">
                <label for="exampleInputPassword4">Jenis Kelamin</label>
                <select class="form-control" id="exampleInputPassword4" name="jenis_kelamin">
                  <option value="" disabled selected>Jenis Kelamin</option>
                  <option value="Laki-laki" <?= isset($jenis_kelamin) && $jenis_kelamin == 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                  <option value="Perempuan" <?= isset($jenis_kelamin) && $jenis_kelamin == 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                </select>
              </div>
              <div class="form-group">
                <label for="exampleInputCity1">Angkatan</label>
                <input type="text" class="form-control" id="exampleInputCity1" name="angkatan" placeholder="Angkatan"
                  value="<?= htmlspecialchars($angkatan ?? ''); ?>">
              </div>
              <div class="form-group">
                <label for="exampleInputCity1">Jabatan</label>
                <input type="text" class="form-control" id="exampleInputCity1" name="jabatan" placeholder="Jabatan"
                  value="<?= htmlspecialchars($jabatan ?? ''); ?>">
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
            <h4 class="card-title">TABEL ANGGOTA</h4>
            <p class="card-description">Add class <code>.table</code></p>
            <form action="" method="GET">
              <div class="input-group mb-2 mr-sm-2">
                <input type="text" class="form-control" placeholder="Cari nama, NIM, atau jabatan" name="cari"
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
                    <th>NIM</th>
                    <th>Jenis Kelamin</th>
                    <th>Angkatan</th>
                    <th>Jabatan</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $urut = 1;
                  while ($r2 = $q2->fetch_assoc()) {
                    $id = $r2["id"];
                    $nama = $r2["nama"];
                    $nim = $r2["nim"];
                    $jenis_kelamin = $r2["jenis_kelamin"];
                    $angkatan = $r2["angkatan"];
                    $jabatan = $r2["jabatan"];
                  ?>
                  <tr>
                    <th scope="row"><?php echo $urut++; ?></th>
                    <td><?php echo htmlspecialchars($nama); ?></td>
                    <td><?php echo htmlspecialchars($nim); ?></td>
                    <td><?php echo htmlspecialchars($jenis_kelamin); ?></td>
                    <td><?php echo htmlspecialchars($angkatan); ?></td>
                    <td><?php echo htmlspecialchars($jabatan); ?></td>
                    <td>
                      <a href="manage-anggota.php?op=edit&id=<?php echo $id; ?>"><button type="button"
                          class="badge badge-warning">Edit</button></a>
                      <a href="manage-anggota.php?op=delete&id=<?php echo $id; ?>"
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
                      <td>Total Anggota Sesuai Filter: </td>
                      <td><?= $q2->num_rows; ?></td>
                    </tr>
                    <tr class="table-danger">
                      <td>Total Anggota Keseluruhan: </td>
                      <td><?php
                        $totalSql = "SELECT COUNT(*) AS total FROM anggota";
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