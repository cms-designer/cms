<?php include __DIR__ . '/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <h2 class="my-4"><?= isset($entry) ? 'Content bearbeiten' : 'Neuen Content anlegen' ?></h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <!-- CSRF-Schutz -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            <div class="mb-3">
                <label for="title" class="form-label">Titel</label>
                <input type="text" class="form-control" id="title" name="title" maxlength="255"
                       value="<?= htmlspecialchars($entry['title'] ?? $_POST['title'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Inhalt</label>
                <textarea class="form-control" id="content" name="content" rows="8" required><?= htmlspecialchars($entry['content'] ?? $_POST['content'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-save"></i> Speichern
            </button>
            <a href="index.php?c=Content&a=index" class="btn btn-secondary ms-2">Abbrechen</a>
        </form>
    </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<?php include __DIR__ . '/footer.php'; ?>