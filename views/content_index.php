<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
?>
<?php include __DIR__ . '/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-lg-10 mt-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Content-Liste</h2>
            <a href="index.php?c=Content&a=create" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Neuen Eintrag anlegen
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle shadow-sm rounded">
                <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Titel</th>
                        <th scope="col">Inhalt (Vorschau)</th>
                        <th scope="col">Gruppen</th>
                        <th scope="col">Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $contentModel = new Content();
                foreach ($contents as $row): 
                    // Hole die Gruppen für diesen Content
                    $groups = $contentModel->getGroups($row['id']);
                    $groupNames = array_map(function($g) { return htmlspecialchars($g['name']); }, $groups);
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= nl2br(htmlspecialchars(mb_strimwidth($row['content'], 0, 80, '...'))) ?></td>
                        <td>
                            <?php if (!empty($groupNames)): ?>
                                <span class="badge bg-secondary"><?= implode('</span> <span class="badge bg-secondary">', $groupNames) ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="index.php?c=Content&a=edit&id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm" title="Bearbeiten">
                                <i class="bi bi-pencil" alt="Bearbeiten"></i>
                            </a>
                            <!-- CSRF-geschützte Löschfunktion -->
                            <form action="index.php?c=Content&a=delete&id=<?= $row['id'] ?>" method="post" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Sicher?')" title="Löschen">
                                    <i class="bi bi-trash" alt="Löschen"></i>
                                </button>
                            </form>
                            <a href="index.php?c=Content&a=assignGroups&id=<?= $row['id'] ?>" class="btn btn-outline-info btn-sm" title="Gruppe zuweisen">
                                <i class="bi bi-people" alt="Gruppe zuweisen"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<?php include __DIR__ . '/footer.php'; ?>