<?php include __DIR__ . '/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <h2 class="my-4">Gruppen zuweisen: <?= htmlspecialchars($user['username']) ?></h2>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Gruppen</label>
                <?php foreach ($allGroups as $group): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="group<?= $group['id'] ?>" name="groups[]" value="<?= $group['id'] ?>"
                            <?= in_array($group['id'], $assignedGroups) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="group<?= $group['id'] ?>">
                            <?= htmlspecialchars($group['name']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-save"></i> Speichern
            </button>
            <a href="index.php?c=User&a=index" class="btn btn-secondary ms-2">Zurück</a>
        </form>
    </div>
</div>
<?php include __DIR__ . '/footer.php'; ?>