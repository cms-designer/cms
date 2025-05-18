<?php
class User {
    private $pdo;
    public function __construct() {
        $cfg = require __DIR__ . '/../config/config.php';
        $db = $cfg['db'];
        $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";
        $this->pdo = new PDO($dsn, $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }
    public function findByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
	
	
public function create($username, $password, $email, $role = 1) {
    $stmt = $this->pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $username,
        password_hash($password, PASSWORD_DEFAULT),
        $email !== '' ? $email : null,
        $role
    ]);
}

public function update($id, $username, $email, $role = 1) {
    $stmt = $this->pdo->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
    $stmt->execute([
        $username,
        $email !== '' ? $email : null,
        $role,
        $id
    ]);
}
    public function getAll() {
        return $this->pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id=?");
        return $stmt->execute([$id]);
    }
	
	
	public function findById($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}





    public function assignGroup($userId, $groupId) {
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO user_groups (user_id, group_id) VALUES (?, ?)");
        return $stmt->execute([$userId, $groupId]);
    }
    public function removeGroup($userId, $groupId) {
        $stmt = $this->pdo->prepare("DELETE FROM user_groups WHERE user_id=? AND group_id=?");
        return $stmt->execute([$userId, $groupId]);
    }
    public function getGroups($userId) {
        $stmt = $this->pdo->prepare(
            "SELECT g.* FROM groups g 
            INNER JOIN user_group ug ON ug.group_id = g.id 
            WHERE ug.user_id = ?"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	
	public function find($id)
{
    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
	
	public function changePassword($id, $newPassword) {
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $this->pdo->prepare("UPDATE users SET password=? WHERE id=?");
    return $stmt->execute([$hash, $id]);
}
}