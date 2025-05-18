<?php include __DIR__ . '/header.php'; ?>
<h2>Sprache auswählen</h2>
<form method="post">
    <label for="language">Sprache wählen:</label>
    <select name="language" id="language" class="form-select">
        <?php foreach ($languages as $id => $name): ?>
            <option value="<?= $id ?>" <?= $currentLanguage == $id ? 'selected' : '' ?>>
                <?= htmlspecialchars($name) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary mt-2">Speichern</button>
</form>
<?php if (isset($_SESSION['role']) && $_SESSION['role'] == 0): ?> <!-- Nur Admins können die Schaltflächen sehen!!! -->
<!-- Button für den CSV-Download -->
<form method="post" action="index.php?c=Settings&a=downloadDatabase">
    <button type="submit" class="btn btn-success mt-4">Datenbank als CSV herunterladen</button>
</form>
<form method="post" action="index.php?c=Settings&a=downloadDatabaseSQL">
    <button type="submit" class="btn btn-primary mt-2">Datenbank als SQL herunterladen</button>
</form>
<div class="mt-4 mb-2 fw-bold text-danger">
    Nur SQL-Daten für Import mit Installscript in neue Version:
</div>
<form method="post" action="index.php?c=Settings&a=downloadDatabaseSQLInsertsOnly">
    <button type="submit" class="btn btn-warning">Nur Daten-Backup (SQL INSERTs) herunterladen</button>
</form>
<?php endif; ?>
<?php include __DIR__ . '/footer.php'; ?>