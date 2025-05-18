<?php 
 $show_menu = false; include __DIR__ . '/header.php'; ?>


<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4 mt-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Login</h2>
                <form method="post" autocomplete="off">
                    <div class="mb-3">
                        <label for="username" class="form-label">Benutzername</label>
                        <input
                            type="text"
                            class="form-control"
                            id="username"
                            name="username"
                            required
                            autofocus
                        >
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Passwort</label>
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            required
                        >
                    </div>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger py-2 my-3 text-center" role="alert">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap Icons CDN (optional, falls nicht im header eingebunden) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<?php include __DIR__ . '/footer.php'; ?>