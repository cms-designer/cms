<?php include __DIR__ . '/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm mt-5">
            <div class="card-body text-center">
                <h2 class="mb-4"><?php echo LBL_CONSOLE; ?></h2>
                <div class="list-group">
				<!-- Nur für Admins -->
				<?php if (isset($_SESSION['role']) && $_SESSION['role'] == 0): ?>
                    <a href="index.php?c=User&a=index" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <span class="bi bi-people"></span>
                        <?php echo LBL_USERVIEW; ?>
                    </a>
                    <a href="index.php?c=Group&a=index" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <span class="bi bi-diagram-3"></span>
                        <?php echo LBL_GROUPVIEW; ?>
                    </a>
					
					       <?php  endif; ?>
                <!-- Ende nur für Admins -->
<a href="index.php?c=Content&a=index" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
    <span class="bi bi-journal-text"></span>
    <?php echo LBL_CONTENTVIEW; ?>
</a>
                    <form method="post" action="index.php?c=Auth&a=logout" class="mt-3">
                        <button type="submit" class="list-group-item list-group-item-action text-danger border-danger d-flex align-items-center gap-2" style="background: #fff;">
                            <span class="bi bi-box-arrow-right"></span>
                            <?php echo LBL_LOGOUT; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap Icons CDN (optional, für hübsche Icons) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<?php include __DIR__ . '/footer.php'; ?>