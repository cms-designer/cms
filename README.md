# CMS Designer

**CMS Designer** ist ein schlankes, eigenentwickeltes Content-Management-System (CMS) zur einfachen Verwaltung von Inhalten, Benutzern und Gruppen. Es basiert auf einem durchdachten MVC-Ansatz und setzt moderne PHP-Technologien ein. 

## Technologien

- **PHP** (ab Version 7.4 empfohlen)
- **PDO** (Datenbankzugriff, MySQL/MariaDB)
- **MVC-Architektur** (Model-View-Controller)
- **Bootstrap 5** (Responsive UI)
- **Plain JavaScript** (für kleine UI-Dynamiken)
- **SQL-Export/-Import** (Updates, Migrationen, Backups)
- **Installations- und Update-Script** (install/install.php)

## Verzeichnisstruktur

```text
/
├── config/                # Konfigurationsdateien (z.B. config.php)
├── controllers/           # Controller-Klassen (Business Logic)
├── models/                # Model-Klassen (Datenbankzugriffe)
├── views/                 # View-Templates (HTML/PHP)
├── public/                # Öffentlicher Webroot (index.php, CSS, JS, Assets)
├── install/               # Installations- und Update-Skripte
│   └── install.php
├── README.md              # Diese Anleitung
```

**Hinweis:**  
Der Webserver muss auf das Verzeichnis `/public` als DocumentRoot zeigen.  
Das Installationsscript befindet sich in `/install/install.php`.

## Features

- **Benutzerverwaltung**: Admins, Redakteure, weitere Gruppen
- **Gruppenverwaltung**: Rollenzuweisung, Rechteverwaltung
- **Content-Management**: Seiten, Beiträge, Inhalte
- **CSV- und SQL-Export**: Datenbank-Backup für Migration, Update & Sicherheit
- **Update-Möglichkeit**: Bestehende Daten können in neuere Versionen übernommen werden (über reinen SQL-Datenexport/import)
- **Installationsscript**: Einfache Einrichtung & Update per Web-Installer
- **Modulare Erweiterbarkeit**: Neue Inhalte, Tabellen und Funktionen können einfach ergänzt werden

## Installationsanleitung

### 1. Vorbereitungen

- **Voraussetzung:** PHP (ab 7.4), MySQL/MariaDB, Webserver (z. B. Apache, nginx)
- **Dateien:** Alle Projektdateien via FTP/Webserver bereitstellen

### 2. Installation per Script (`install/install.php`)

1. **Aufruf im Browser:**  
   `https://<deine-domain>/install/install.php`
2. **Datenbankzugang eingeben:**  
   - Host, Name, Benutzer, Passwort
3. **(Optional) SQL-Datenimport:**  
   - Bei Update/Migration: Backup-Datei (reiner SQL-INSERT-Export, z.B. `import.sql`) auswählen
   - Bei Neuinstallation: Felder für Admin-Benutzer werden angezeigt
4. **Beispieldaten:**  
   - Optional kannst du Beispieldaten einfügen lassen (bei Neuinstallation)
5. **Abschluss:**  
   - Nach erfolgreicher Installation wird ein Link zum Login angezeigt (`/public/`)

### 3. Installation per phpMyAdmin (Experten)

- **Tabellenstruktur**: Die jeweils aktuelle Struktur findest du im Installer-Script oder als SQL-Datei im Projekt
- **Datenimport**: Backup (nur INSERTs) in die bestehende (neue) Datenbank einspielen
- **Konfiguration:**  
  - `/config/config.php` mit den Zugangsdaten befüllen (siehe Beispiel im Installer)

## Update-/Backup-Strategie

- **Backup:**  
  - Über das Admin-Panel kannst du jederzeit ein CSV- oder reines SQL-Daten-Backup herunterladen.
- **Update:**  
  - Nach Einspielen der neuen Dateien rufst du erneut `install/install.php` auf und lädst dein Daten-Backup hoch.  
  - Existierende Inhalte bleiben erhalten, neue Spalten/Tabellen werden ergänzt.

## Sicherheit & Hinweise

- Die Zugangsdaten werden in `/config/config.php` abgelegt.
- Das Installationsverzeichnis `/install` sollte nach der Einrichtung entfernt oder geschützt werden.
- Passwörter werden sicher mittels `password_hash()` gespeichert.

---

**Viel Spaß mit CMS Designer!**
  
Fragen, Wünsche oder Bugs? [GitHub Issues](https://github.com/cms-designer/cms-designer/issues)