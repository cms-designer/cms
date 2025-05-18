<?php
class Content {
    private $pdo;

    public function __construct() {
        $cfg = require __DIR__ . '/../config/config.php';
        $db = $cfg['db'];
        $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";
        $this->pdo = new PDO($dsn, $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    public function all() {
        $stmt = $this->pdo->query("SELECT * FROM content ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	
	

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM content WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($title, $content) {
        $stmt = $this->pdo->prepare("INSERT INTO content (title, content) VALUES (?, ?)");
        return $stmt->execute([$title, $content]);
    }

    public function update($id, $title, $content) {
        $stmt = $this->pdo->prepare("UPDATE content SET title=?, content=? WHERE id=?");
        return $stmt->execute([$title, $content, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM content WHERE id=?");
        return $stmt->execute([$id]);
    }
	
	public function getGroups($contentId) {
    $stmt = $this->pdo->prepare(
        "SELECT g.id, g.name
         FROM groups g
         INNER JOIN content_group cg ON g.id = cg.group_id
         WHERE cg.content_id = ?"
    );
    $stmt->execute([$contentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getByUserGroups($userId) {
    // Annahme: Tabelle content_groups (content_id, group_id) und user_groups (user_id, group_id)
    $sql = "
        SELECT c.*
        FROM content c
        JOIN content_group cg ON c.id = cg.content_id
        JOIN user_group ug ON cg.group_id = ug.group_id
        WHERE ug.user_id = ?
        GROUP BY c.id
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function setGroups($contentId, $groupIds) {
    // Erst alle alten Verknüpfungen löschen
    $stmt = $this->pdo->prepare("DELETE FROM content_group WHERE content_id=?");
    $stmt->execute([$contentId]);
    // Neue Verknüpfungen setzen
    $stmt = $this->pdo->prepare("INSERT INTO content_group (content_id, group_id) VALUES (?, ?)");
    foreach ($groupIds as $gid) {
        $stmt->execute([$contentId, $gid]);
    }
}
}