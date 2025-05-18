<?php

class Group
{
    private $pdo;

    public function __construct() {
        $cfg = require __DIR__ . '/../config/config.php';
        $db = $cfg['db'];
        $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";
        $this->pdo = new PDO($dsn, $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    public function all()
    {
        $stmt = $this->pdo->query("SELECT * FROM groups ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM groups WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name)
    {
        $stmt = $this->pdo->prepare("INSERT INTO groups (name) VALUES (?)");
        return $stmt->execute([$name]);
    }

    public function update($id, $name)
    {
        $stmt = $this->pdo->prepare("UPDATE groups SET name=? WHERE id=?");
        return $stmt->execute([$name, $id]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM groups WHERE id=?");
        return $stmt->execute([$id]);
    }

    /**
     * Liefert alle Gruppen, die einem bestimmten Content zugeordnet sind.
     * @param int $contentId
     * @return array
     */
    public function getGroupsByContent($contentId)
    {
        $stmt = $this->pdo->prepare(
            "SELECT g.id, g.name
             FROM groups g
             INNER JOIN content_group cg ON g.id = cg.group_id
             WHERE cg.content_id = ?"
        );
        $stmt->execute([$contentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Setzt die Gruppen-Zuordnung für einen Content-Eintrag (alle alten werden entfernt, neue gesetzt)
     * @param int $contentId
     * @param array $groupIds
     */
    public function setGroupsForContent($contentId, $groupIds)
    {
        // Alle alten Verknüpfungen löschen
        $stmt = $this->pdo->prepare("DELETE FROM content_group WHERE content_id=?");
        $stmt->execute([$contentId]);
        // Neue Verknüpfungen einfügen
        $stmt = $this->pdo->prepare("INSERT INTO content_group (content_id, group_id) VALUES (?, ?)");
        foreach ($groupIds as $gid) {
            $stmt->execute([$contentId, $gid]);
        }
    }

    /**
     * Liefert alle Gruppen, die einem bestimmten Benutzer zugeordnet sind.
     * @param int $userId
     * @return array
     */
    public function getGroupsByUser($userId)
    {
        $stmt = $this->pdo->prepare(
            "SELECT g.id, g.name
             FROM groups g
             INNER JOIN user_group ug ON g.id = ug.group_id
             WHERE ug.user_id = ?"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Setzt die Gruppen-Zuordnung für einen Benutzer (alle alten werden entfernt, neue gesetzt)
     * @param int $userId
     * @param array $groupIds
     */
    public function setGroupsForUser($userId, $groupIds)
    {
        // Alle alten Verknüpfungen löschen
        $stmt = $this->pdo->prepare("DELETE FROM user_group WHERE user_id=?");
        $stmt->execute([$userId]);
        // Neue Verknüpfungen einfügen
        $stmt = $this->pdo->prepare("INSERT INTO user_group (user_id, group_id) VALUES (?, ?)");
        foreach ($groupIds as $gid) {
            $stmt->execute([$userId, $gid]);
        }
    }
}