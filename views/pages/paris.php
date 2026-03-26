<?php include("views/components/header.php"); ?>
<?php
require_once("models/betModel.php");

$betModel = new BetModel();
$bets = $betModel->getUserBets(currentUser()["id"]);
?>

<main class="matchs-container">
  <section class="section">
    <h2>Mes paris</h2>

    <?php if (empty($bets)): ?>
      <p>Vous n'avez encore place aucun pari.</p>
    <?php else: ?>
      <?php foreach ($bets as $bet): ?>
        <?php
        $pointsLabel = '';

        if ($bet['resultat'] === 'gagné') {
            $pointsLabel = '+1';
        } elseif ($bet['resultat'] === 'perdu') {
            $pointsLabel = '-1';
        }
        ?>
        <div class="match-card">
          <div>
            <p><strong><?= htmlspecialchars($bet['jeu']) ?> :</strong> <?= htmlspecialchars($bet['equipe1']) ?> 🆚 <?= htmlspecialchars($bet['equipe2']) ?></p>
            <p>Votre choix : <strong><?= htmlspecialchars($bet[$bet['choix']]) ?></strong></p>
            <p>Date du match : <?= date('d/m/Y H:i', strtotime($bet['date_match'])) ?></p>
          </div>
          <div>
            <p>
              Statut : <strong><?= htmlspecialchars($bet['resultat']) ?></strong>
              <?php if ($pointsLabel !== ''): ?>
                <span class="bet-points <?= $bet['resultat'] === 'gagné' ? 'bet-points-win' : 'bet-points-loss' ?>">
                  <?= $pointsLabel ?>
                </span>
              <?php endif; ?>
            </p>
            <?php if ($bet['gagnant'] !== 'en cours'): ?>
              <p>Resultat du match : <strong><?= htmlspecialchars($bet[$bet['gagnant']]) ?></strong></p>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>
</main>

<?php include("views/components/footer.php"); ?>
