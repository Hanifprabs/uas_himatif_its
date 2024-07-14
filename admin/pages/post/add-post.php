<?php
session_start();
include('../../includes/config.php');
error_reporting(0);

$sukses = "";
$error = "";

// Cek hak akses
if (!isset($_SESSION['type']) || $_SESSION['type'] == "anggota") {
  echo "Hanya untuk pengurus";
  exit;
}

// Inisialisasi variabel
$postTitle = $categoryId = $subCategoryId = $postDetails = $postImage = '';
$id_user = $_SESSION['id']; // Ambil id_user dari sesi pengguna yang sedang login

// Ambil data untuk edit jika 'op' adalah 'edit'
$op = isset($_GET['op']) ? $_GET['op'] : '';
if ($op == 'edit' && isset($_GET['id'])) {
  $id = $_GET['id'];
  $sql1 = "SELECT * FROM tblposts WHERE id = ?";
  $stmt1 = $con->prepare($sql1);
  $stmt1->bind_param('i', $id);
  $stmt1->execute();
  $result1 = $stmt1->get_result();
  $r1 = $result1->fetch_assoc();

  if ($r1) {
    $postTitle = $r1['PostTitle'];
    $categoryId = $r1['CategoryId'];
    $subCategoryId = $r1['SubCategoryId'];
    $postDetails = $r1['PostDetails'];
    $postImage = $r1['PostImage'];
  } else {
    $error = "Data tidak ditemukan";
  }
}

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $op = $_POST['op']; // Ambil operasi (edit atau tambah baru)
  $postTitle = $_POST['postTitle'];
  $categoryId = $_POST['category'];
  $subCategoryId = $_POST['subCategory'];
  $postDetails = $_POST['postDetails'];

  // Proses unggahan gambar
  if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
    $targetDir = "../../../uploads/";
    $targetFile = $targetDir . basename($_FILES["img"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $uploadOk = 1;

    // Validasi file gambar
    $check = getimagesize($_FILES["img"]["tmp_name"]);
    if ($check === false) {
      $error = "File is not an image.";
      $uploadOk = 0;
    }

    // Validasi ukuran file
    if ($_FILES["img"]["size"] > 5000000) {
      $error = "Sorry, your file is too large.";
      $uploadOk = 0;
    }

    // Izinkan hanya format file tertentu
    $allowedFileTypes = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowedFileTypes)) {
      $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
      $uploadOk = 0;
    }

    // Generate nama unik untuk file gambar
    $postImage = uniqid() . "." . $imageFileType;
    $targetFile = $targetDir . $postImage;

    // Pindahkan file ke direktori target
    if ($uploadOk == 1 && move_uploaded_file($_FILES["img"]["tmp_name"], $targetFile)) {
      // File berhasil diunggah, simpan jalur file ke dalam kolom PostImage
    } else {
      $error = "Sorry, there was an error uploading your file.";
    }
  }

  if (empty($error)) {
    if ($op == 'edit') {
      // Update postingan
      $postId = $_POST['postId'];
      $sql = "UPDATE tblposts SET PostTitle=?, CategoryId=?, SubCategoryId=?, PostDetails=?, PostImage=?, id_user=? WHERE id=?";
      $stmt = $con->prepare($sql);
      $stmt->bind_param("siisssi", $postTitle, $categoryId, $subCategoryId, $postDetails, $postImage, $id_user, $postId);
    } else {
      // Buat postingan baru
      $sql = "INSERT INTO tblposts (PostTitle, CategoryId, SubCategoryId, PostDetails, PostImage, id_user) VALUES (?, ?, ?, ?, ?, ?)";
      $stmt = $con->prepare($sql);
      $stmt->bind_param("siisss", $postTitle, $categoryId, $subCategoryId, $postDetails, $postImage, $id_user);
    }

    // Eksekusi statement SQL
    if ($stmt->execute()) {
      $sukses = ($op == 'edit') ? "Post updated successfully." : "Post created successfully.";
    } else {
      $error = "Error: " . $stmt->error;
    }
  }
}

// Handle deletion
if ($op == 'delete' && isset($_GET['id'])) {
  $id = $_GET['id'];
  $sql_delete = "DELETE FROM tblposts WHERE id = ?";
  $stmt_delete = $con->prepare($sql_delete);
  $stmt_delete->bind_param('i', $id);
  $delete_result = $stmt_delete->execute();

  if ($delete_result) {
    $sukses = "Data berhasil dihapus";
  } else {
    $error = "Gagal menghapus data: " . $stmt_delete->error;
  }
}

// Fetch data with filters
$where = [];
if (isset($_GET['filter_tanggal']) && $_GET['filter_tanggal'] != '') {
  $filter_tanggal = $con->real_escape_string($_GET['filter_tanggal']);
  $where[] = "DATE(PostingDate) = '$filter_tanggal'";
}
if (isset($_GET['cari']) && $_GET['cari'] != '') {
  $cari = $con->real_escape_string($_GET['cari']);
  $where[] = "(PostTitle LIKE '%$cari%' OR PostDetails LIKE '%$cari%')";
}
$whereSQL = '';
if (count($where) > 0) {
  $whereSQL = 'WHERE ' . implode(' AND ', $where);
}
$sql2 = "SELECT * FROM tblposts $whereSQL ORDER BY id DESC";
$q2 = $con->query($sql2);

// Fetch categories for the dropdown
$sql_categories = "SELECT * FROM tblcategory";
$result_categories = $con->query($sql_categories);



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
            <h4 class="card-title"><?= $op == 'edit' ? 'Edit Post' : 'Create New Post'; ?></h4>
            <p class="card-description">
              <?= $op == 'edit' ? 'Edit the details of the post' : 'Fill in the details for the new post'; ?> </p>
            <form class="forms-sample" action="" method="POST" enctype="multipart/form-data">
              <?php if ($error): ?>
                <div id="alert-error" class="alert alert-danger" role="alert">
                  <?= htmlspecialchars($error); ?>
                </div>
              <?php endif; ?>
              <?php if ($sukses): ?>
                <div id="alert-success" class="alert alert-success" role="alert">
                  <?= htmlspecialchars($sukses); ?>
                </div>
              <?php endif; ?>
              <!-- Judul Postingan -->
              <div class="form-group">
                <label for="postTitle">Judul Postingan</label>
                <input type="text" class="form-control" id="postTitle" name="postTitle" placeholder="Enter title"
                  value="<?= htmlspecialchars($postTitle ?? ''); ?>" required>
              </div>
              <!-- Category -->
              <div class="form-group">
                <label for="category">Category</label>
                <select class="form-control" id="category" name="category" required>
                  <option value="">Select Category</option>
                  <?php while ($row = $result_categories->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['id']); ?>" <?= isset($categoryId) && $categoryId == $row['id'] ? 'selected' : ''; ?>>
                      <?= htmlspecialchars($row['CategoryName']); ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
              <!-- Sub Category -->
              <div class="form-group">
                <label for="subCategory">Sub Category</label>
                <select class="form-control" id="subCategory" name="subCategory" required>
                  <option value="">Select Sub-Category</option>
                  <?php
                  // Ambil data subkategori sesuai kategori yang dipilih
                  if (isset($categoryId)) {
                    $sql_subcategories = "SELECT * FROM tblsubcategory WHERE CategoryId = ?";
                    $stmt_subcategories = $con->prepare($sql_subcategories);
                    $stmt_subcategories->bind_param('i', $categoryId);
                    $stmt_subcategories->execute();
                    $result_subcategories = $stmt_subcategories->get_result();
                    while ($row_sub = $result_subcategories->fetch_assoc()):
                      ?>
                      <option value="<?= htmlspecialchars($row_sub['SubCategoryId']); ?>" <?= isset($subCategoryId) && $subCategoryId == $row_sub['SubCategoryId'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($row_sub['Subcategory']); ?>
                      </option>
                    <?php endwhile;
                  } ?>
                </select>
              </div>
              <!-- Post Details -->
              <div class="form-group">
                <label for="postDetails">Post Details</label>
                <textarea class="form-control" id="postDetails" name="postDetails" rows="4"
                  placeholder="Enter post details" required><?= htmlspecialchars($postDetails ?? ''); ?></textarea>
              </div>
              <!-- Feature Image -->
              <div class="form-group">
                <label>Feature Image</label>
                <input type="file" name="img" class="file-upload-default" id="imgUpload">
                <div class="input-group col-xs-12">
                  <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                  <span class="input-group-append">
                    <button class="file-upload-browse btn btn-primary" type="button"
                      onclick="document.getElementById('imgUpload').click();">Upload</button>
                  </span>
                </div>
                <?php if ($op == 'edit' && $postImage): ?>
                  <div class="mt-3">
                    <img src="<?= htmlspecialchars($postImage); ?>" alt="Post Image" class="img-fluid"
                      style="max-width: 100px;">
                  </div>
                <?php endif; ?>
              </div>
              <button type="submit" class="btn btn-primary">Submit</button>
              <button type="reset" class="btn btn-dark">Cancel</button>
            </form>
          </div>
        </div>
      </div>

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
            <!-- Tabel untuk menampilkan data kegiatan -->
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Judul Postingan</th>
                    <th>Category</th>
                    <th>Sub-Category</th>
                    <th>Post Details</th>
                    <th>Feature Image</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Mengambil data kegiatan dari database atau form input
                  $urut = 1;
                  while ($r2 = $q2->fetch_assoc()) {
                    $id = $r2["id"];
                    $postTitle = $r2["PostTitle"];
                    $category = $r2["CategoryId"];
                    $subCategory = $r2["SubCategoryId"];
                    $postDetails = $r2["PostDetails"];
                    $postImage = $r2["PostImage"];
                  ?>
                    <tr>
                      <th scope="row"><?php echo $urut++; ?></th>
                      <td><?php echo htmlspecialchars($postTitle); ?></td>
                      <td><?php echo htmlspecialchars($category); ?></td>
                      <td><?php echo htmlspecialchars($subCategory); ?></td>
                      <td><?php echo htmlspecialchars($postDetails); ?></td>
                      <td><img src="<?php echo htmlspecialchars($postImage); ?>" alt="Feature Image" class="img-fluid"
                          style="max-width: 100px;"></td>
                      <td>
                        <a href="manage-kegiatan.php?op=edit&id=<?php echo $id; ?>"><button type="button"
                            class="badge badge-warning">Edit</button></a>
                        <a href="manage-kegiatan.php?op=delete&id=<?php echo $id; ?>"
                          onclick="return confirm('Yakin akan menghapus data?')"><button type="button"
                            class="badge badge-danger">Hapus</button></a>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

            <!-- Tampilan total kegiatan sesuai filter dan keseluruhan -->
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
                        $totalSql = "SELECT COUNT(*) AS total FROM tblposts";
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

<!-- Required JS files -->
<!-- plugins:js -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/4.20.2/standard/ckeditor.js"></script>
<script>
  $(document).ready(function () {
    $('#alert-error, #alert-success').delay(5000).fadeOut(500);
    // Initialize CKEditor
    CKEDITOR.replace('postDetails');

    // Update Sub-Category dropdown based on Category selection
    $('#category').change(function () {
      var categoryId = $(this).val();
      $.ajax({
        url: 'fetch_subcategories.php',
        method: 'POST',
        data: { categoryId: categoryId },
        success: function (response) {
          $('#subCategory').html(response);
        }
      });
    });

    // Show filename on image upload
    document.getElementById('imgUpload').onchange = function () {
      var filename = this.value.split('\\').pop();
      this.nextElementSibling.querySelector('.file-upload-info').value = filename;
    };
  });
</script>

</body>

</html>

<?php
ob_end_flush(); // Mengakhiri output buffering dan mengirim output
?>