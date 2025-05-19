<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
?>
<?php include __DIR__ . '/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <h2 class="my-4">Passwort ändern</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <!-- CSRF-Schutz -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            <div class="mb-3">
                <label for="password" class="form-label">Neues Passwort</label>
                <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password">
            </div>
            <div class="mb-3">
                <label for="password_confirm" class="form-label">Neues Passwort wiederholen</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-key"></i> Passwort speichern
            </button>
            <a href="index.php?c=User&a=index" class="btn btn-secondary ms-2">Abbrechen</a>
        </form>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>