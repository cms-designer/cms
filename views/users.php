<?php include __DIR__ . '/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-lg-10 mt-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Benutzerliste</h2>
            <a href="index.php?c=User&a=create" class="btn btn-success">
                <i class="bi bi-person-plus"></i> Neuen Benutzer anlegen
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle shadow-sm rounded">
                <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Benutzername</th>
                        <th scope="col">Email</th>
                        <th scope="col">Gruppen</th>
                        <th scope="col">Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $userModel = new User();
                foreach ($users as $user):
                    $groups = $userModel->getGroups($user['id']);
                    $groupNames = array_map(function($g) { return htmlspecialchars($g['name']); }, $groups);
                ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td>
                            <?= htmlspecialchars($user['username']) ?>
                            <?php if (isset($user['role']) && $user['role'] == 0): ?>
                                <span title="Admin" class="ms-1 text-danger"><i class="bi bi-shield-lock-fill"></i></span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <?php if (!empty($groupNames)): ?>
                                <span class="badge bg-secondary"><?= implode('</span> <span class="badge bg-secondary">', $groupNames) ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
<td>
    <a href="index.php?c=User&a=edit&username=<?= urlencode($user['username']) ?>" 
       class="btn btn-outline-primary btn-sm" 
       title="Bearbeiten">
        <i class="bi bi-pencil"></i>
    </a>
    <a href="index.php?c=User&a=delete&id=<?= $user['id'] ?>" 
       class="btn btn-outline-danger btn-sm" 
       title="Löschen"
       onclick="return confirm('Sicher?')">
        <i class="bi bi-trash"></i>
    </a>
    <a href="index.php?c=User&a=changePassword&id=<?= $user['id'] ?>" 
       class="btn btn-outline-warning btn-sm" 
       title="Passwort ändern">
        <i class="bi bi-key"></i>
    </a>
    <a href="index.php?c=User&a=assignGroups&id=<?= $user['id'] ?>" 
       class="btn btn-outline-info btn-sm" 
       title="Gruppe zuweisen">
        <i class="bi bi-people"></i>
    </a>
</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Bootstrap Icons CDN (optional, für hübsche Icons) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<?php include __DIR__ . '/footer.php'; ?>