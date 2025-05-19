<?php
// Fortschritts- und Statusvariablen
$success = false;
$error = '';
$stepMsg = '';
$tablesCreated = false;
$adminCreated = false;
$sampleCreated = false;
$configWritten = false;
$backupImported = false;

// Hilfsfunktion für Pflichtfeldprüfung
function field($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

// Passwort-Hash
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Beispieldaten (sinnvolle Werte)
function getSampleSQL() {
    return [
        "INSERT INTO `basis` (`language`) VALUES (1);",
        "INSERT INTO `groups` (`name`, `description`) VALUES
            ('Admins', 'Administratoren'),
            ('Redaktion', 'Redakteure'),
            ('Marketing', 'Marketing-Team');",
        "INSERT INTO `content` (`title`, `content`, `contentcolor`) VALUES
            ('Willkommen', 'Willkommen im CMS!', '#3498db'),
            ('Kontakt', 'Hier können Sie uns kontaktieren.', '#2ecc71');",
        "INSERT INTO `content_group` (`content_id`, `group_id`) VALUES
            (1, 1), (2, 2);",
        "INSERT INTO `user_group` (`user_id`, `group_id`) VALUES
            (1, 1);",
        "INSERT INTO `kontakt` (`name`) VALUES
            ('Max Mustermann'),
            ('Erika Beispiel');"
    ];
}

// Tabellenerstellung SQL (neue Grundstruktur)
$tableSQL = [
    "CREATE TABLE IF NOT EXISTS `basis` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `language` int(11) NOT NULL DEFAULT 1,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
    "CREATE TABLE IF NOT EXISTS `content` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `content` text NOT NULL,
        `contentcolor` varchar(7) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
    "CREATE TABLE IF NOT EXISTS `content_group` (
        `content_id` int(11) NOT NULL,
        `group_id` int(11) NOT NULL,
        PRIMARY KEY (`content_id`,`group_id`),
        KEY `group_id` (`group_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
    "CREATE TABLE IF NOT EXISTS `groups` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(50) NOT NULL,
        `description` text DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `name` (`name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
    "CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(50) NOT NULL,
        `password` varchar(255) NOT NULL,
        `email` varchar(191) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `role` tinyint(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
    "CREATE TABLE IF NOT EXISTS `user_group` (
        `user_id` int(11) NOT NULL,
        `group_id` int(11) NOT NULL,
        PRIMARY KEY (`user_id`,`group_id`),
        KEY `group_id` (`group_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
    "CREATE TABLE IF NOT EXISTS `kontakt` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(20) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
];

// Foreign Keys separat (Fehler werden protokolliert, Installation läuft weiter)
$foreignSQL = [
    "ALTER TABLE `content_group`
        ADD CONSTRAINT `content_group_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE,
        ADD CONSTRAINT `content_group_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE;",
    "ALTER TABLE `user_group`
        ADD CONSTRAINT `user_group_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
        ADD CONSTRAINT `user_group_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE;"
];

// Installationslogik
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbhost = field('dbhost');
    $dbname = field('dbname');
    $dbuser = field('dbuser');
    $dbpass = field('dbpass');
    $adminuser = field('adminuser');
    $adminpass = field('adminpass');
    $adminmail = field('adminmail');
    $insert_sample = isset($_POST['insert_sample']);
    $hasBackup = isset($_FILES['backup']) && $_FILES['backup']['error'] === UPLOAD_ERR_OK && $_FILES['backup']['size'] > 0;

    // Schritt 1: Pflichtfelder prüfen
    if (!$dbhost || !$dbname || !$dbuser || (!$hasBackup && (!$adminuser || !$adminpass))) {
        $error = "Bitte füllen Sie alle Pflichtfelder aus!";
        $stepMsg = "Eingaben prüfen";
    } else {
        try {
            $dsn = "mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4";
            $pdo = new PDO($dsn, $dbuser, $dbpass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $stepMsg = "Verbindung zur Datenbank erfolgreich.";

            // Foreign Key Checks deaktivieren (zur Sicherheit)
            $pdo->exec("SET foreign_key_checks = 0;");

            // Schritt 2: Neue Tabellenstruktur anlegen (Fehler sichtbar!)
            foreach ($tableSQL as $sql) {
                $pdo->exec($sql);
            }
            $tablesCreated = true;

            // Foreign Key Checks wieder aktivieren
            $pdo->exec("SET foreign_key_checks = 1;");

            // Schritt 2b: Foreign Keys anlegen (Fehler werden angezeigt, Installation läuft weiter)
            foreach ($foreignSQL as $sql) {
                try {
                    $pdo->exec($sql);
                } catch (PDOException $e) {
                    $stepMsg .= "<br>Warnung (FK): " . $e->getMessage();
                }
            }

            // Prüfe, ob 'users'-Tabelle existiert (Fehlerausgabe!)
            $check = $pdo->query("SHOW TABLES LIKE 'users'")->fetch();
            if (!$check) throw new Exception("Tabelle 'users' konnte nicht angelegt werden! Prüfe die CREATE TABLE-Syntax und MySQL-Version.");

            $stepMsg = "Tabellenstruktur wurde erstellt.";

            // Schritt 3: Backup importieren oder Admin + Beispieldaten einfügen
            if ($hasBackup) {
                $sqlContent = file_get_contents($_FILES['backup']['tmp_name']);
                // Splitte in einzelne Statements (sicherer für größere Dumps)
                $statements = preg_split('/;(\r?\n|\r)/', $sqlContent);
                foreach ($statements as $stmt) {
                    $stmt = trim($stmt);
                    if ($stmt && stripos($stmt, 'CREATE TABLE') === false && stripos($stmt, 'ALTER TABLE') === false) {
                        try {
                            $pdo->exec($stmt);
                        } catch(PDOException $e) {
                            $stepMsg .= "<br>Warnung (Import): " . $e->getMessage();
                        }
                    }
                }
                $backupImported = true;
                $stepMsg = "Backup wurde importiert.";
            } else {
                // Admin anlegen
                $stmt = $pdo->prepare("INSERT INTO `users` (`username`, `password`, `email`, `role`) VALUES (?, ?, ?, 0)");
                $stmt->execute([$adminuser, hashPassword($adminpass), $adminmail ?: null]);
                $adminCreated = true;
                $stepMsg = "Admin-Benutzer wurde angelegt.";

                // Beispieldaten einfügen?
                if ($insert_sample) {
                    foreach (getSampleSQL() as $sql) {
                        $pdo->exec($sql);
                    }
                    $sampleCreated = true;
                    $stepMsg = "Beispieldaten wurden eingefügt.";
                }
            }

            // Schritt 4: Konfigurationsdatei schreiben
            $configArray = [
                'db' => [
                    'host' => $dbhost,
                    'dbname' => $dbname,
                    'user' => $dbuser,
                    'pass' => $dbpass,
                    'charset' => 'utf8mb4'
                ]
            ];
            $configDir = dirname(__DIR__) . '/config';
            if (!is_dir($configDir)) {
                mkdir($configDir, 0755, true);
            }
            $configFile = $configDir . '/config.php';
            $configContent = "<?php\nreturn " . var_export($configArray, true) . ";\n";
            if (file_put_contents($configFile, $configContent) !== false) {
                $configWritten = true;
                $stepMsg = "Konfigurationsdatei wurde geschrieben.";
            } else {
                $error = "Die Konfiguration konnte nicht in <code>../config/config.php</code> geschrieben werden.";
            }

            $success = $configWritten && (($backupImported) || ($tablesCreated && $adminCreated && (!$insert_sample || $sampleCreated)));
        } catch (PDOException $e) {
            $error = "Fehler bei der Installation: " . $e->getMessage();
            $stepMsg = "Fehler beim Ausführen.";
        } catch (Exception $e) {
            $error = $e->getMessage();
            $stepMsg = "Fehler beim Ausführen.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>CMS Designer Installation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .progress { height: 24px; }
        .progress-bar { font-size: 1em; }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white h4">
                    CMS Designer Installation
                </div>
                <div class="card-body">
                    <?php
                    $progress = 0;
                    if ($success) $progress = 100;
                    elseif ($configWritten) $progress = 90;
                    elseif ($backupImported || $sampleCreated || $adminCreated) $progress = 75;
                    elseif ($tablesCreated) $progress = 50;
                    elseif ($stepMsg) $progress = 20;
                    ?>
                    <div class="mb-3">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped <?php echo $success ? 'bg-success' : 'bg-info' ?>" role="progressbar" style="width: <?= $progress ?>%" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
                                <?= $progress ?> %
                            </div>
                        </div>
                        <?php if($stepMsg): ?>
                            <div class="mt-2 text-secondary"><?= $stepMsg ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if($success): ?>
                        <div class="alert alert-success">
                            <h5 class="alert-heading">Installation erfolgreich!</h5>
                            <?php if($backupImported): ?>
                                Backup wurde erfolgreich eingespielt.<br>
                            <?php else: ?>
                                <?php if($tablesCreated): ?>Tabellen wurden erfolgreich erstellt.<br><?php endif; ?>
                                <?php if($adminCreated): ?>Admin-Benutzer wurde erfolgreich angelegt.<br><?php endif; ?>
                                <?php if($sampleCreated): ?>Beispieldaten wurden erfolgreich eingefügt.<br><?php endif; ?>
                            <?php endif; ?>
                            <?php if($configWritten): ?>Konfigurationsdatei <code>../config/config.php</code> wurde erfolgreich geschrieben.<br><?php endif; ?>
                        </div>
                        <a href="../public" class="btn btn-success mt-3">Zum Login</a>
                    <?php else: ?>
                        <?php if($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <form method="post" enctype="multipart/form-data" autocomplete="off" id="installForm">
                            <div class="mb-3">
                                <label for="dbhost" class="form-label">Datenbank-Host *</label>
                                <input type="text" class="form-control" id="dbhost" name="dbhost" required value="<?= htmlspecialchars(field('dbhost')) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="dbname" class="form-label">Datenbank-Name *</label>
                                <input type="text" class="form-control" id="dbname" name="dbname" required value="<?= htmlspecialchars(field('dbname')) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="dbuser" class="form-label">Datenbank-Benutzer *</label>
                                <input type="text" class="form-control" id="dbuser" name="dbuser" required value="<?= htmlspecialchars(field('dbuser')) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="dbpass" class="form-label">Datenbank-Passwort</label>
                                <input type="password" class="form-control" id="dbpass" name="dbpass">
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label for="backup" class="form-label">SQL-Daten-Backup (nur INSERTs, z.B. import.sql) einspielen</label>
                                <input type="file" class="form-control" id="backup" name="backup" accept=".sql,.txt">
                                <small class="form-text text-muted">
                                    Optional: Wähle eine SQL-Daten-Backup-Datei (nur INSERTs) für ein Update/Wiederherstellung. Ohne Auswahl wird eine neue Instanz installiert.
                                </small>
                            </div>
                            <div id="admin-fields"<?php if(isset($_FILES['backup']) && $_FILES['backup']['error'] === UPLOAD_ERR_OK && $_FILES['backup']['size'] > 0) echo ' style="display:none"'; ?>>
                                <div class="mb-3">
                                    <label for="adminuser" class="form-label">Admin-Benutzername *</label>
                                    <input type="text" class="form-control" id="adminuser" name="adminuser" value="<?= htmlspecialchars(field('adminuser')) ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="adminpass" class="form-label">Admin-Passwort *</label>
                                    <input type="password" class="form-control" id="adminpass" name="adminpass">
                                </div>
                                <div class="mb-3">
                                    <label for="adminmail" class="form-label">Admin-E-Mail</label>
                                    <input type="email" class="form-control" id="adminmail" name="adminmail" value="<?= htmlspecialchars(field('adminmail')) ?>">
                                </div>
                                <div class="form-check mb-3" id="sampleCheckDiv"<?php if(isset($_FILES['backup']) && $_FILES['backup']['error'] === UPLOAD_ERR_OK && $_FILES['backup']['size'] > 0) echo ' style="display:none"'; ?>>
                                    <input class="form-check-input" type="checkbox" value="1" id="insert_sample" name="insert_sample" <?= isset($_POST['insert_sample']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="insert_sample">
                                        Beispieldaten einfügen
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Installation starten</button>
                        </form>
                        <script>
                        // Admin-Felder und Beispieldaten-Checkbox ausblenden, wenn Backup gewählt wird
                        document.getElementById('backup').addEventListener('change', function(){
                            var adminFields = document.getElementById('admin-fields');
                            var sampleCheck = document.getElementById('sampleCheckDiv');
                            if(this.files.length>0 && this.files[0].size>0) {
                                adminFields.style.display = "none";
                                if (sampleCheck) sampleCheck.style.display = "none";
                            } else {
                                adminFields.style.display = "";
                                if (sampleCheck) sampleCheck.style.display = "";
                            }
                        });
                        </script>
                    <?php endif; ?>
                </div>
            </div>
            <div class="text-center text-muted mt-3" style="font-size: 0.95em;">
                &copy; <?= date('Y') ?> CMS Designer
            </div>
        </div>
    </div>
</div>
</body>
</html>
