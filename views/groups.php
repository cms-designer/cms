<?php include __DIR__ . '/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-lg-8 mt-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Gruppenliste</h2>
            <a href="index.php?c=Group&a=create" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Neue Gruppe anlegen
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle shadow-sm rounded">
                <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($groups as $group): ?>
                    <tr>
                        <td><?= htmlspecialchars($group['id']) ?></td>
                        <td><?= htmlspecialchars($group['name']) ?></td>
                        <td>
                            <a href="index.php?c=Group&a=edit&id=<?= $group['id'] ?>" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-pencil"></i> Bearbeiten
                            </a>
                            <a href="index.php?c=Group&a=delete&id=<?= $group['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Sicher?')">
                                <i class="bi bi-trash"></i> Löschen
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Bootstrap Icons CDN (optional, für Icons) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<?php include __DIR__ . '/footer.php'; ?>