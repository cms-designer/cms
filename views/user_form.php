<?php include __DIR__ . '/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title mb-4">
                    <?= isset($user) ? 'Benutzer bearbeiten' : 'Neuen Benutzer anlegen' ?>
                </h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="post" autocomplete="off">
                    <div class="mb-3">
                        <label for="username" class="form-label">Benutzername <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control"
                            id="username"
                            name="username"
                            value="<?= isset($user['username']) ? htmlspecialchars($user['username']) : '' ?>"
                            required
                            autofocus
                        >
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-Mail (optional)</label>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            value="<?= isset($user['email']) ? htmlspecialchars($user['email']) : '' ?>"
                        >
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Rolle <span class="text-danger">*</span></label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="1" <?= (isset($user['role']) && $user['role'] == 1) ? 'selected' : '' ?>>Benutzer (Standard)</option>
                            <option value="0" <?= (isset($user['role']) && $user['role'] == 0) ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    <?php if (!isset($user)): ?>
                        <div class="mb-3">
                            <label for="password" class="form-label">Passwort <span class="text-danger">*</span></label>
                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                required
                            >
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check2"></i> Speichern
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap Icons CDN (optional, falls noch nicht im header eingebunden) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<?php include __DIR__ . '/footer.php'; ?>