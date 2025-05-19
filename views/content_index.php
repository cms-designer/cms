<?php include __DIR__ . '/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-lg-10 mt-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h2 class="mb-0"> <?php echo LBL_CONTENTLIST; ?></h2>
            <a href="index.php?c=Content&a=create" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> <?php echo LBL_CONTENTADD; ?>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle shadow-sm rounded">
                <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col"><?php echo LBL_CONTENTLIST; ?></th>
                        <th scope="col"><?php echo LBL_CONTENTDISCRIBE; ?></th>
                        <th scope="col"><?php echo LBL_CONTENTGROUP; ?></th>
                        <th scope="col"><?php echo LBL_CONTENTACTION; ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $contentModel = new Content();
                foreach ($contents as $row): 
                    // Hole die Gruppen für diesen Content (du brauchst eine passende Methode im Content Model!)
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
                            <a href="index.php?c=Content&a=edit&id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-pencil"></i> Bearbeiten
                            </a>
                            <a href="index.php?c=Content&a=delete&id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Sicher?')">
                                <i class="bi bi-trash"></i> Löschen
                            </a>
                            <a href="index.php?c=Content&a=assignGroups&id=<?= $row['id'] ?>" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-people"></i> Gruppe zuweisen
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