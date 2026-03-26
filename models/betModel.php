<?php
require_once "models/pdoModel.php";

class BetModel {
    private $pdo;

    public function __construct() {
        $this->pdo = setDB();
    }

    public function getUserBets($userId) {
        $stmt = $this->pdo->prepare(
            "SELECT paris.*, matchs.jeu, matchs.equipe1, matchs.equipe2, matchs.date_match, matchs.gagnant
             FROM paris
             INNER JOIN matchs ON matchs.id = paris.match_id
             WHERE paris.user_id = ?
             ORDER BY matchs.date_match DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserBetForMatch($userId, $matchId) {
        $stmt = $this->pdo->prepare("SELECT * FROM paris WHERE user_id = ? AND match_id = ?");
        $stmt->execute([$userId, $matchId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function placeBet($userId, $matchId, $choice) {
        $existingBet = $this->getUserBetForMatch($userId, $matchId);

        if ($existingBet) {
            $stmt = $this->pdo->prepare(
                "UPDATE paris SET choix = ?, mise = 1, resultat = 'en attente' WHERE id = ?"
            );
            return $stmt->execute([$choice, $existingBet["id"]]);
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO paris (user_id, match_id, choix, mise, resultat) VALUES (?, ?, ?, 1, 'en attente')"
        );
        return $stmt->execute([$userId, $matchId, $choice]);
    }

    public function settleBetsForMatch($matchId, $winner) {
        $stmt = $this->pdo->prepare("SELECT * FROM paris WHERE match_id = ?");
        $stmt->execute([$matchId]);
        $bets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($bets as $bet) {
            $isWin = $bet["choix"] === $winner;
            $result = $isWin ? "gagné" : "perdu";
            $pointsDelta = $isWin ? 1 : -1;

            $updateBet = $this->pdo->prepare("UPDATE paris SET resultat = ? WHERE id = ?");
            $updateBet->execute([$result, $bet["id"]]);

            $updateUser = $this->pdo->prepare("UPDATE users SET points = points + ? WHERE id = ?");
            $updateUser->execute([$pointsDelta, $bet["user_id"]]);
        }
    }
}
