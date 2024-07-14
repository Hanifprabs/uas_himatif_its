<?php 
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == "gagal") {
        echo '<div class="alert alert-danger">Pendaftaran Gagal NIM tidak terdaftar atau akun sudah diaktifkan</div>';
    } else if ($_GET['pesan'] == "logout") {
        echo "Anda telah berhasil logout";
    } else if ($_GET['pesan'] == "sukses") {
        echo '<div class="alert alert-success">Pendaftaran Berhasil Silahkan Kembali Ke Halaman Login</div>';
    } else if ($_GET['pesan'] == "belum_login") {
        echo "Anda harus login untuk mengakses halaman admin";
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
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="row w-100 m-0">
                <div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">
                    <div class="card col-lg-4 mx-auto">
                        <div class="card-body px-5 py-5">
                            <h3 class="card-title text-left mb-3">Register</h3>
                            <form class="form-horizontal" action="regc.php" method="post">
                                <div class="form-group">
                                    <label>NIM</label>
                                    <input class="form-control p_input" type="text" required="" name="nim" placeholder="NIM">
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input class="form-control p_input" type="password" id="password" name="password" required="" placeholder="Password" onkeyup='check();'>
                                </div>
                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <input class="form-control p_input" type="password" id="confirm_password" name="confirm_password" required="" placeholder="Confirm Password" onkeyup='check();'>
                                </div>
                                <div id="message" class="form-group"></div>
                                <div class="form-group d-flex align-items-center justify-content-between">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input"> Remember me
                                        </label>
                                    </div>
                                    <a href="#" class="forgot-pass">Forgot password</a>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-block enter-btn">Register</button>
                                </div>
                                <div class="d-flex">
                                    <button class="btn btn-facebook col mr-2">
                                        <i class="mdi mdi-facebook"></i> Facebook
                                    </button>
                                    <button class="btn btn-google col">
                                        <i class="mdi mdi-google-plus"></i> Google plus
                                    </button>
                                </div>
                                <p class="sign-up text-center">Already have an Account?<a href="login.php"> Sign In</a></p>
                                <p class="terms">By creating an account you are accepting our <a href="#">Terms & Conditions</a></p>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- content-wrapper ends -->
            </div>
            <!-- row ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
    <!-- endinject -->
    <script type="text/javascript">
        var check = function() {
            if (document.getElementById('password').value ==
                document.getElementById('confirm_password').value) {
                document.getElementById('message').style.color = 'green';
                document.getElementById('message').innerHTML = 'Password matches';
            } else {
                document.getElementById('message').style.color = 'red';
                document.getElementById('message').innerHTML = 'Password does not match';
            }
        }
    </script>
</body>
</html>
