<?php
require_once("models/pdoModel.php");

class MatchModel {
    private $pdo;

    public function __construct() {
        $this->pdo = setDB();
    }

    public function getMatchsEnCours() {
        $sql = "SELECT * FROM matchs WHERE gagnant = 'en cours' AND date_match <= NOW()";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMatchsAVenir() {
        $sql = "SELECT * FROM matchs WHERE date_match > NOW() AND gagnant = 'en cours' ORDER BY date_match ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMatchsTermines() {
        $sql = "SELECT * FROM matchs WHERE gagnant IN ('equipe1', 'equipe2') ORDER BY date_match DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllMatchs() {
        $sql = "SELECT * FROM matchs ORDER BY date_match DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMatchById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM matchs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createMatch($jeu, $equipe1, $equipe2, $dateMatch) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO matchs (jeu, equipe1, equipe2, date_match, gagnant) VALUES (?, ?, ?, ?, 'en cours')"
        );

        return $stmt->execute([$jeu, $equipe1, $equipe2, $dateMatch]);
    }

    public function deleteMatch($id) {
        $stmt = $this->pdo->prepare("DELETE FROM matchs WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function closeMatch($id, $winner) {
        $stmt = $this->pdo->prepare("UPDATE matchs SET gagnant = ? WHERE id = ?");
        return $stmt->execute([$winner, $id]);
    }

    public function teamIsUsedInMatch($teamName) {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM matchs WHERE equipe1 = ? OR equipe2 = ?"
        );
        $stmt->execute([$teamName, $teamName]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
