<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
?>
<?php include __DIR__ . '/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title mb-4">
                    <?= isset($group) ? 'Gruppe bearbeiten' : 'Neue Gruppe anlegen' ?>
                </h2>
                <form method="post" autocomplete="off">
                    <!-- CSRF-Schutz -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            value="<?= isset($group['name']) ? htmlspecialchars($group['name']) : '' ?>"
                            required
                            autofocus
                        >
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check2"></i> Speichern
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap Icons CDN (optional, falls nicht bereits im header eingebunden) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<?php include __DIR__ . '/footer.php'; ?>