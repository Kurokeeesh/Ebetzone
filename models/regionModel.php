<?php
require_once "models/pdoModel.php";

class RegionModel {
    private $pdo;

    public function __construct() {
        $this->pdo = setDB();
    }

    public function getAllRegions() {
        $stmt = $this->pdo->query("SELECT * FROM regions ORDER BY nom ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRegionByName($nom) {
        $stmt = $this->pdo->prepare("SELECT * FROM regions WHERE nom = ?");
        $stmt->execute([$nom]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createRegion($nom) {
        $stmt = $this->pdo->prepare("INSERT INTO regions (nom) VALUES (?)");
        return $stmt->execute([$nom]);
    }

    public function getRegionById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM regions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteRegion($id) {
        $stmt = $this->pdo->prepare("DELETE FROM regions WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
