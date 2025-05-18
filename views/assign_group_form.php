<?php include __DIR__ . '/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-lg-8 mt-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title mb-4">Benutzer Gruppen zuweisen</h2>
                <form method="post" action="index.php?a=assignGroup" class="row g-3">
                    <div class="col-md-6">
                        <label for="user_id" class="form-label">Benutzer <span class="text-danger">*</span></label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">Bitte wählen...</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>">
                                    <?= htmlspecialchars($user['username']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="group_id" class="form-label">Gruppe <span class="text-danger">*</span></label>
                        <select class="form-select" id="group_id" name="group_id" required>
                            <option value="">Bitte wählen...</option>
                            <?php foreach ($groups as $group): ?>
                                <option value="<?= $group['id'] ?>">
                                    <?= htmlspecialchars($group['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-link"></i> Zuweisen
                        </button>
                    </div>
                </form>
                <a href="index.php?a=adminpanel" class="btn btn-outline-secondary btn-sm mt-3">
                    <i class="bi bi-arrow-left"></i> Zurück
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-3">Alle Gruppenberechtigungen</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Benutzer</th>
                                <th>Gruppe</th>
                                <th>Löschen</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $userModel = new User();
                        foreach ($users as $user) {
                            $userGroups = $userModel->getGroups($user['id']);
                            foreach ($userGroups as $group) {
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($group['name']) ?></td>
                                <td>
                                    <form method="post" action="index.php?a=removeUserGroup" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Wirklich entfernen?');">
                                            <i class="bi bi-trash"></i> Löschen
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap Icons CDN (optional, falls nicht im header eingebunden) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<?php include __DIR__ . '/footer.php'; ?>