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

// Fetch categories for dropdown
$sql_categories = "SELECT id, CategoryName FROM tblcategory";
$result_categories = $con->query($sql_categories);

// Fetch data for editing if 'op' is 'edit'
if ($op == 'edit') {
    $id = $_GET['id'] ?? '';
    if ($id) {
        $sql1 = "SELECT * FROM tblsubcategory WHERE SubCategoryId = ?";
        $stmt1 = $con->prepare($sql1);
        $stmt1->bind_param('i', $id);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        $r1 = $result1->fetch_assoc();

        if ($r1) {
            $CategoryId = $r1['CategoryId'];
            $Subcategory = $r1['Subcategory'];
            $SubCatDescription = $r1['SubCatDescription'];
        } else {
            $error = "Data tidak ditemukan";
        }
    } else {
        $error = "ID tidak ditemukan";
    }
}

// Handle form submission
if (isset($_POST["simpan"])) {
    $CategoryId = trim($_POST["CategoryId"] ?? '');
    $Subcategory = trim($_POST["Subcategory"] ?? '');
    $SubCatDescription = trim($_POST["SubCatDescription"] ?? '');

    // Validate the input data
    if (!$CategoryId || !$Subcategory || !$SubCatDescription) {
        $error = "Silakan masukkan semua data";
    } else {
        if ($op == 'edit' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $sql1 = "UPDATE tblsubcategory SET CategoryId = ?, Subcategory = ?, SubCatDescription = ?, UpdationDate = NOW() WHERE SubCategoryId = ?";
            $stmt1 = $con->prepare($sql1);
            $stmt1->bind_param('issi', $CategoryId, $Subcategory, $SubCatDescription, $id);
            $q1 = $stmt1->execute();

            if ($q1) {
                $sukses = "Data berhasil diupdate";
            } else {
                $error = "Data gagal diupdate: " . $stmt1->error;
                error_log($error, 3, 'error_log.txt');  // Log error ke file error_log.txt
            }
        } else {
            $sql1 = "INSERT INTO tblsubcategory (CategoryId, Subcategory, SubCatDescription, PostingDate, UpdationDate, Is_Active) VALUES (?, ?, ?, NOW(), NOW(), 1)";
            $stmt1 = $con->prepare($sql1);
            $stmt1->bind_param('iss', $CategoryId, $Subcategory, $SubCatDescription);
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
        $sql_delete = "DELETE FROM tblsubcategory WHERE SubCategoryId = ?";
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
    $where[] = "(Subcategory LIKE '%$cari%' OR SubCatDescription LIKE '%$cari%')";
}
$whereSQL = '';
if (count($where) > 0) {
    $whereSQL = 'WHERE ' . implode(' AND ', $where);
}
$sql2 = "SELECT * FROM tblsubcategory
         JOIN tblcategory ON tblsubcategory.CategoryId = tblcategory.id
         $whereSQL ORDER BY SubCategoryId DESC";
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
                        <h4 class="card-title">KELOLA SUB-KATEGORI</h4>
                        <form action="" class="forms-sample" method="POST">
                            <div class="form-group">
                                <label for="CategoryId">Category</label>
                                <select class="form-control" id="CategoryId" name="CategoryId">
                                    <?php while ($row = $result_categories->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($row['id']); ?>" <?= isset($CategoryId) && $CategoryId == $row['id'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($row['CategoryName']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="Subcategory">Sub-Category</label>
                                <input type="text" class="form-control" id="Subcategory" name="Subcategory" placeholder="Sub-Category"
                                    value="<?= htmlspecialchars($Subcategory ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="SubCatDescription">Sub-Category Description</label>
                                <textarea class="form-control" id="SubCatDescription" name="SubCatDescription" rows="4" placeholder="Sub-Category Description"><?= htmlspecialchars($SubCatDescription ?? ''); ?></textarea>
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
                        <h4 class="card-title">TABEL SUB-KATEGORI</h4>
                        <form action="" method="GET">
                            <div class="input-group mb-2 mr-sm-2">
                                <input type="text" class="form-control" placeholder="Cari subkategori atau deskripsi" name="cari"
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
                                        <th>Category</th>
                                        <th>Sub-Category</th>
                                        <th>Description</th>
                                        <th>Posting Date</th>
                                        <th>Updation Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $urut = 1;
                                    while ($r2 = $q2->fetch_assoc()) {
                                        $id = $r2["SubCategoryId"];
                                        $CategoryName = $r2["CategoryName"];
                                        $Subcategory = $r2["Subcategory"];
                                        $SubCatDescription = $r2["SubCatDescription"];
                                        $PostingDate = $r2["PostingDate"];
                                        $UpdationDate = $r2["UpdationDate"];
                                        ?>
                                        <tr>
                                            <th scope="row"><?php echo $urut++; ?></th>
                                            <td><?php echo htmlspecialchars($CategoryName); ?></td>
                                            <td><?php echo htmlspecialchars($Subcategory); ?></td>
                                            <td><?php echo htmlspecialchars($SubCatDescription); ?></td>
                                            <td><?php echo htmlspecialchars($PostingDate); ?></td>
                                            <td><?php echo htmlspecialchars($UpdationDate); ?></td>
                                            <td>
                                                <a href="manage-subcategory.php?op=edit&id=<?php echo $id; ?>"><button type="button"
                                                    class="badge badge-warning">Edit</button></a>
                                                <a href="manage-subcategory.php?op=delete&id=<?php echo $id; ?>"
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
                                            <td>Total Subkategori Sesuai Filter: </td>
                                            <td><?= $q2->num_rows; ?></td>
                                        </tr>
                                        <tr class="table-danger">
                                            <td>Total Subkategori Keseluruhan: </td>
                                            <td><?php
                                                $totalSql = "SELECT COUNT(*) AS total FROM tblsubcategory";
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
}
?>