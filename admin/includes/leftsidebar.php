<!-- partial:partials/_sidebar.html -->
<style>
        .sidebar-brand-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-brand-wrapper .brand-text {
            font-size: 24px; /* Atur ukuran teks sesuai kebutuhan */
            font-weight: bold; /* Atur ketebalan teks sesuai kebutuhan */
            margin-left: 10px; /* Jarak antara logo dan teks */
        }

        .sidebar-brand-wrapper .brand-logo {
            width: 50px; /* Sesuaikan ukuran gambar */
            height: auto; /* Pertahankan rasio aspek */
        }
    </style>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
<div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
        <span><img src="assets/images/logohimatif.png" alt="Himatif Logo" class="brand-logo" /></span>
        <span class="brand-text">HIMATIF</span>
    </div>
  <ul class="nav">
    <li class="nav-item profile">
      <div class="profile-desc">
        <div class="profile-pic">
          <div class="count-indicator">
            <img class="img-xs rounded-circle" src="assets/images/faces/face15.jpg" alt="">
            <span class="count bg-success"></span>
          </div>
          <div class="profile-name">
            <h5 class="mb-0 font-weight-normal">Henry Klein</h5>
            <span>Gold Member</span>
          </div>
        </div>
        <a href="#" id="profile-dropdown" data-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>
        <div class="dropdown-menu dropdown-menu-right sidebar-dropdown preview-list" aria-labelledby="profile-dropdown">
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
                <i class="mdi mdi-onepassword text-info"></i>
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
      <a class="nav-link" href="index.php">
        <span class="menu-icon">
          <i class="mdi mdi-speedometer"></i>
        </span>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    <li class="nav-item menu-items">
      <a class="nav-link" data-toggle="collapse" href="#kegiatan" aria-expanded="false" aria-controls="kegiatan">
        <span class="menu-icon">
          <i class="mdi mdi-calendar"></i>
        </span>
        <span class="menu-title">Kegiatan</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="kegiatan">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="pages/ui-featuress/kegiatanku.php">Jadwal Kegiatan</a>
          </li>
          <li class="nav-item"> <a class="nav-link" href="pages/ui-featuress/manage-kegiatan.php">Kelola Jadwal
              Kegiatan</a></li>
        </ul>
      </div>
    </li>
    <li class="nav-item menu-items">
      <a class="nav-link" data-toggle="collapse" href="#anggota" aria-expanded="false" aria-controls="anggota">
        <span class="menu-icon">
          <i class="mdi mdi-account-multiple"></i>
        </span>
        <span class="menu-title">Anggota</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="anggota">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="pages/anggota/anggota.php">Daftar Anggota</a></li>
          <li class="nav-item"> <a class="nav-link" href="pages/anggota/manage-anggota.php">Kelola Anggota</a></li>
        </ul>
      </div>
    </li>
    <li class="nav-item menu-items">
      <a class="nav-link" data-toggle="collapse" href="#keuangan" aria-expanded="false" aria-controls="keuangan">
        <span class="menu-icon">
          <i class="mdi mdi-cash"></i>
        </span>
        <span class="menu-title">Keuangan</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="keuangan">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="pages/laporan_kas/laporankas.php">Laporan Kas</a></li>
          <li class="nav-item"> <a class="nav-link" href="pages/laporan_kas/manage-kas.php">Kelola Kas Masuk</a>
          </li>
          <li class="nav-item"> <a class="nav-link" href="pages/laporan_kas/manage-kas-keluar.php">Kelola Kas
              Keluar</a></li>
          <li class="nav-item"> <a class="nav-link" href="pages/ui-featuress/laporan-tahunan.php">Laporan
              Tahunan</a></li>
        </ul>
      </div>
    </li>
    <li class="nav-item menu-items">
      <a class="nav-link" data-toggle="collapse" href="#himatif" aria-expanded="false" aria-controls="himatif">
        <span class="menu-icon">
          <i class="mdi mdi-folder"></i>
        </span>
        <span class="menu-title">Category</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="himatif">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link" href="pages/category/manage-categories.php">Manage Category</a>
          </li>
        </ul>
      </div>
    </li>
    <li class="nav-item menu-items">
      <a class="nav-link" data-toggle="collapse" href="#subcategory" aria-expanded="false" aria-controls="subcategory">
        <span class="menu-icon">
          <i class="mdi mdi-folder"></i>
        </span>
        <span class="menu-title">Sub Category</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="subcategory">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link" href="pages/subcategory/manage-subcategory.php">Manage Sub Category</a>
          </li>
        </ul>
      </div>
    </li>
    <li class="nav-item menu-items">
  <a class="nav-link" data-toggle="collapse" href="#post" aria-expanded="false" aria-controls="post">
    <span class="menu-icon">
      <i class="mdi mdi-folder"></i>
    </span>
    <span class="menu-title">Post</span>
    <i class="menu-arrow"></i>
  </a>
  <div class="collapse" id="post">
    <ul class="nav flex-column sub-menu">
      <li class="nav-item">
        <a class="nav-link" href="pages/post/add-post.php">Add Post</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="pages/post/manage-post.php">Manage Post</a>
      </li>
    </ul>
  </div>
</li>
  </ul>
</nav>
<!-- partial -->
