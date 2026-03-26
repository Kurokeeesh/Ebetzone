<?php
require_once "models/pdoModel.php";

class TeamModel {
    private $pdo;

    public function __construct() {
        $this->pdo = setDB();
    }

    public function getAllTeams() {
        $stmt = $this->pdo->query(
            "SELECT teams.*, regions.nom AS region_nom
             FROM teams
             LEFT JOIN regions ON regions.id = teams.region_id
             ORDER BY teams.nom ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTeamByName($nom) {
        $stmt = $this->pdo->prepare("SELECT * FROM teams WHERE nom = ?");
        $stmt->execute([$nom]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTeamById($id) {
        $stmt = $this->pdo->prepare(
            "SELECT teams.*, regions.nom AS region_nom
             FROM teams
             LEFT JOIN regions ON regions.id = teams.region_id
             WHERE teams.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createTeam($nom, $jeu, $regionId) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO teams (nom, jeu, region_id) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$nom, $jeu, $regionId ?: null]);
    }

    public function deleteTeam($id) {
        $stmt = $this->pdo->prepare("DELETE FROM teams WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
