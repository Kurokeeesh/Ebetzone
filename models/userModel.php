<?php
require_once "models/pdoModel.php";
class UserModel {
    private $pdo;

    public function __construct() {
        $this->pdo = setDB();
    }

    public function login($login, $password) {
        // Récupérer l'utilisateur
        $req = "SELECT * FROM users WHERE login = ?";
        $stmt = $this->pdo->prepare($req);
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if (!$user) {
            return false;
        }

        if (password_verify($password, $user['mdp'])) {
            if (password_needs_rehash($user['mdp'], PASSWORD_DEFAULT)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $update = $this->pdo->prepare("UPDATE users SET mdp = ? WHERE id = ?");
                $update->execute([$newHash, $user['id']]);
                $user['mdp'] = $newHash;
            }

            return $user;
        }

        // Compatibilite temporaire avec les anciens mots de passe stockes en clair
        if ($password === $user['mdp']) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $update = $this->pdo->prepare("UPDATE users SET mdp = ? WHERE id = ?");
            $update->execute([$newHash, $user['id']]);
            $user['mdp'] = $newHash;
            return $user;
        }

        return false;
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByLogin($login) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE login = ?");
        $stmt->execute([$login]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser($username, $login, $password, $role = "user") {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (username, login, mdp, role, points) VALUES (?, ?, ?, ?, 0)"
        );

        return $stmt->execute([$username, $login, $hash, $role]);
    }

    public function getClassement() {
        $stmt = $this->pdo->query(
            "SELECT id, username, login, points
             FROM users
             WHERE role = 'user'
             ORDER BY points DESC, username ASC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function logout() {
        session_destroy();
    }
}
