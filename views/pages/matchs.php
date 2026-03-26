<?php include("views/components/header.php"); ?>
<?php
require_once("models/matchModel.php");
require_once("models/betModel.php");

$baseUrl = baseUrl();
$user = currentUser();
$matchModel = new MatchModel();
$betModel = new BetModel();
$matchsLive = $matchModel->getMatchsEnCours();
$matchsFuturs = $matchModel->getMatchsAVenir();
$userBets = [];

foreach ($betModel->getUserBets($user["id"]) as $bet) {
    $userBets[$bet["match_id"]] = $bet;
}
?>

<main class="matchs-container">
  <section class="section">
    <h2>🔴 Matchs en cours</h2>
    <?php if (empty($matchsLive)): ?>
      <p style="color:gray;">Aucun match en cours pour le moment.</p>
    <?php else: ?>
      <?php foreach ($matchsLive as $match): ?>
        <div class="match-card">
          <p><strong><?= htmlspecialchars($match['jeu']) ?> :</strong> <?= htmlspecialchars($match['equipe1']) ?> 🆚 <?= htmlspecialchars($match['equipe2']) ?></p>
          <span class="live">En direct</span>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>

  <section class="section">
    <h2>📅 Matchs à venir</h2>
    <?php if (empty($matchsFuturs)): ?>
      <p style="color:gray;">Aucun match programmé.</p>
    <?php else: ?>
      <?php foreach ($matchsFuturs as $match): ?>
        <?php $existingBet = $userBets[$match['id']] ?? null; ?>
        <div class="match-card">
          <div>
            <p><strong><?= htmlspecialchars($match['jeu']) ?> :</strong> <?= htmlspecialchars($match['equipe1']) ?> 🆚 <?= htmlspecialchars($match['equipe2']) ?></p>
            <span class="date"><?= date('d/m/Y H:i', strtotime($match['date_match'])) ?></span>
            <?php if ($existingBet): ?>
              <p>Votre pari actuel : <strong><?= htmlspecialchars($match[$existingBet['choix']]) ?></strong></p>
            <?php endif; ?>
          </div>

          <form method="post" action="<?= $baseUrl ?>/parier" style="margin-left: 20px;">
            <input type="hidden" name="match_id" value="<?= (int) $match['id'] ?>">
            <label>
              <input type="radio" name="choix" value="equipe1" <?= $existingBet && $existingBet['choix'] === 'equipe1' ? 'checked' : '' ?> required>
              <?= htmlspecialchars($match['equipe1']) ?>
            </label>
            <label>
              <input type="radio" name="choix" value="equipe2" <?= $existingBet && $existingBet['choix'] === 'equipe2' ? 'checked' : '' ?>>
              <?= htmlspecialchars($match['equipe2']) ?>
            </label>
            <button type="submit" class="btn-parier">
              <?= $existingBet ? 'Modifier mon pari' : 'Parier' ?>
            </button>
          </form>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>
</main>
<?php include("views/components/footer.php"); ?>
