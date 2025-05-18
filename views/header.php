<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>CMS Designer</title>
    <!-- Bootstrap 5 CDN -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            padding-top: 70px; /* Platz für die fixe Navbar */
        }
        .cms-menu .btn, .cms-menu .nav-link {
            color: #198754;
            background-color: #fff;
            border: 1.5px solid #adb5bd;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .cms-menu .btn:hover, .cms-menu .nav-link:hover, .cms-menu .btn.active, .cms-menu .nav-link.active, .cms-menu .btn:focus, .cms-menu .nav-link:focus {
            background-color: #e9fbe7;
            color: #157347;
            border-color: #198754;
        }
        .cms-menu .btn-logout {
            color: #fff !important;
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
        }
        .cms-menu .btn-logout:hover, .cms-menu .btn-logout:focus {
            background-color: #bb2d3b !important;
            border-color: #a71d2a !important;
            color: #fff !important;
        }
        /* Dropdown hover open */
        @media (min-width: 992px) {
            .cms-menu .dropdown:hover .dropdown-menu {
                display: block;
                margin-top: 0;
            }
        }
        @media (max-width: 991.98px) {
            .cms-menu {
                flex-direction: column;
                align-items: flex-start;
            }
            .cms-menu .btn, .cms-menu .nav-link {
                width: 100%;
                margin-right: 0;
                margin-bottom: 0.5rem;
            }
        }
        /* Optional: remove border from dropdown toggle */
        .cms-menu .dropdown-toggle {
            border: 1.5px solid #adb5bd;
        }
        .cms-menu .dropdown-menu {
            min-width: 200px;
        }
        .cms-menu .dropdown-item {
            color: #198754;
            font-weight: 500;
            transition: all 0.2s;
        }
        .cms-menu .dropdown-item:hover, .cms-menu .dropdown-item.active {
            background-color: #e9fbe7;
            color: #157347;
        }
    </style>
</head>
<body>
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>
<?php if (!isset($show_menu) || $show_menu !== false): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">CMSDesigner</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Menü ein-/ausblenden">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <div class="cms-menu d-flex align-items-center flex-wrap">
                <!-- Dropdown: Benutzer -->
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 0): ?>
                <div class="dropdown">
                    <a class="btn dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-gear" title="<?php echo LBL_USER; ?>"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="index.php?c=User&a=index"><?php echo LBL_USERVIEW; ?></a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="index.php?c=User&a=create"><?php echo LBL_USERADD; ?></a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="index.php?c=Group&a=index"><?php echo LBL_GROUPVIEW; ?></a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="index.php?c=Group&a=create"><?php echo LBL_GROUPADD; ?></a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                    </ul>
                </div>
                <?php  endif; ?>
                <!-- Dropdown: Content -->
                <div class="dropdown">
                    <a class="btn dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-journals" title="<?php echo LBL_CONTENT; ?>"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="index.php?c=Content&a=index"><?php echo LBL_CONTENTVIEW; ?></a>
                        </li>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 0): ?>
                        <li>
                            <a class="dropdown-item" href="index.php?c=Content&a=create"><?php echo LBL_CONTENTADD; ?></a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Benutzername und Icon -->
                <?php if (!empty($_SESSION['username'])): ?>
                <span class="d-flex align-items-center me-2 fw-semibold text-white">
                    <i class="bi bi-person-circle fs-5 me-1 text-white" title="Benutzer"></i>
                    <?= htmlspecialchars($_SESSION['username']) ?>
                </span>
                <?php endif; ?>
<!-- Sprache/Hamburger-Menü-Icon in einer gelben Schaltfläche -->
<a href="index.php?c=Language&a=index"
   class="btn btn-warning ms-2"
   title="<?php echo LBL_SETTINGS; ?>"
   style="font-size: 1.5rem; padding: 0.25rem 0.75rem; line-height: 1;">
    &#9776;
</a>
                <!-- Logout -->
                <form method="post" action="index.php?c=Auth&a=logout" class="d-inline m-0 p-0">
                    <button type="submit" class="btn btn-logout" title="<?php echo LBL_LOGOUT; ?>">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?>
<!-- Restlicher Header-Inhalt -->