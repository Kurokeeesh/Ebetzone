<?php include("views/components/header.php"); ?>
<?php
require_once("models/matchModel.php");
require_once("models/regionModel.php");
require_once("models/teamModel.php");

$baseUrl = baseUrl();
$matchModel = new MatchModel();
$regionModel = new RegionModel();
$teamModel = new TeamModel();
$allMatchs = $matchModel->getAllMatchs();
$regions = $regionModel->getAllRegions();
$teams = $teamModel->getAllTeams();
?>

<main class="matchs-container">
  <section class="section admin-section">
    <h2>Administration des matchs</h2>

    <nav class="admin-nav">
      <a href="#admin-create-match">Creer un match</a>
      <a href="#admin-create-region">Creer une region</a>
      <a href="#admin-create-team">Creer une equipe</a>
      <a href="#admin-manage-matches">Voir les matchs</a>
    </nav>

    <h2 id="admin-create-match" class="admin-subtitle">Creer un match</h2>
    <form method="post" action="<?= $baseUrl ?>/admin-match-create" class="admin-form">
      <label for="team1_id">Equipe 1</label>
      <select name="team1_id" id="team1_id" required>
        <option value="">Choisir l'equipe 1</option>
        <?php foreach ($teams as $team): ?>
          <option value="<?= (int) $team['id'] ?>">
            <?= htmlspecialchars($team['nom']) ?> - <?= htmlspecialchars($team['jeu']) ?>
            <?php if (!empty($team['region_nom'])): ?>
              (<?= htmlspecialchars($team['region_nom']) ?>)
            <?php endif; ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label for="team2_id">Equipe 2</label>
      <select name="team2_id" id="team2_id" required>
        <option value="">Choisir l'equipe 2</option>
        <?php foreach ($teams as $team): ?>
          <option value="<?= (int) $team['id'] ?>">
            <?= htmlspecialchars($team['nom']) ?> - <?= htmlspecialchars($team['jeu']) ?>
            <?php if (!empty($team['region_nom'])): ?>
              (<?= htmlspecialchars($team['region_nom']) ?>)
            <?php endif; ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label for="date_match_admin">Date et heure du match</label>
      <input
        type="datetime-local"
        name="date_match"
        id="date_match_admin"
        min="<?= date('Y-m-d\TH:i') ?>"
        step="60"
        required
      >
      <button type="submit" <?= count($teams) < 2 ? 'disabled' : '' ?>>Ajouter le match</button>
    </form>

    <?php if (count($teams) < 2): ?>
      <p class="admin-closed-message">Ajoutez au moins deux equipes pour pouvoir creer un match.</p>
    <?php endif; ?>

    <h2 id="admin-create-region" class="admin-subtitle">Creer une region</h2>
    <form method="post" action="<?= $baseUrl ?>/admin-region-create" class="admin-form">
      <input type="text" name="region_nom" placeholder="Nom de la region" required>
      <button type="submit">Ajouter la region</button>
    </form>

    <?php if (!empty($regions)): ?>
      <div class="admin-inline-list">
        <?php foreach ($regions as $region): ?>
          <form method="post" action="<?= $baseUrl ?>/admin-region-delete" class="admin-inline-form" onsubmit="return confirm('Supprimer cette region ?');">
            <input type="hidden" name="region_id" value="<?= (int) $region['id'] ?>">
            <span class="date"><?= htmlspecialchars($region['nom']) ?></span>
            <button type="submit" class="admin-inline-delete">Supprimer</button>
          </form>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <h2 id="admin-create-team" class="admin-subtitle">Creer une equipe</h2>
    <form method="post" action="<?= $baseUrl ?>/admin-team-create" class="admin-form">
      <input type="text" name="team_nom" placeholder="Nom de l'equipe" required>
      <input type="text" name="team_jeu" placeholder="Jeu de l'equipe" required>
      <select name="region_id">
        <option value="">Choisir une region</option>
        <?php foreach ($regions as $region): ?>
          <option value="<?= (int) $region['id'] ?>"><?= htmlspecialchars($region['nom']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit">Ajouter l'equipe</button>
    </form>

    <?php if (!empty($teams)): ?>
      <div class="admin-list">
        <?php foreach ($teams as $team): ?>
          <div class="match-card admin-match-card">
            <div>
              <p><strong><?= htmlspecialchars($team['nom']) ?></strong></p>
              <p>Jeu : <?= htmlspecialchars($team['jeu']) ?></p>
            </div>
            <div class="admin-actions">
              <p class="admin-closed-message">
                Region : <?= htmlspecialchars($team['region_nom'] ?? 'Non definie') ?>
              </p>
              <form method="post" action="<?= $baseUrl ?>/admin-team-delete" onsubmit="return confirm('Supprimer cette equipe ?');">
                <input type="hidden" name="team_id" value="<?= (int) $team['id'] ?>">
                <button type="submit">Supprimer</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <h2 id="admin-manage-matches" class="admin-subtitle">Matchs existants</h2>
    <div class="admin-list">
      <?php if (empty($allMatchs)): ?>
        <p>Aucun match enregistre.</p>
      <?php else: ?>
        <?php foreach ($allMatchs as $match): ?>
          <?php
          $winnerName = $match['gagnant'];

          if ($match['gagnant'] === 'equipe1') {
              $winnerName = $match['equipe1'];
          } elseif ($match['gagnant'] === 'equipe2') {
              $winnerName = $match['equipe2'];
          }
          ?>
          <div class="match-card admin-match-card">
            <div>
              <p><strong><?= htmlspecialchars($match['jeu']) ?> :</strong> <?= htmlspecialchars($match['equipe1']) ?> 🆚 <?= htmlspecialchars($match['equipe2']) ?></p>
              <p>Date : <?= date('d/m/Y H:i', strtotime($match['date_match'])) ?></p>
              <p>Etat : <strong><?= htmlspecialchars($winnerName) ?></strong></p>
            </div>

            <div class="admin-actions">
              <?php if ($match['gagnant'] === 'en cours'): ?>
                <p class="admin-actions-title">Actions admin</p>
                <form method="post" action="<?= $baseUrl ?>/admin-match-close">
                  <input type="hidden" name="match_id" value="<?= (int) $match['id'] ?>">
                  <label for="winner_<?= (int) $match['id'] ?>">Choisir le gagnant</label>
                  <select name="winner" id="winner_<?= (int) $match['id'] ?>" required>
                    <option value="">Choisir le gagnant</option>
                    <option value="equipe1"><?= htmlspecialchars($match['equipe1']) ?></option>
                    <option value="equipe2"><?= htmlspecialchars($match['equipe2']) ?></option>
                  </select>
                  <button type="submit">Cloturer</button>
                </form>

                <form method="post" action="<?= $baseUrl ?>/admin-match-delete" onsubmit="return confirm('Supprimer ce match ?');">
                  <input type="hidden" name="match_id" value="<?= (int) $match['id'] ?>">
                  <button type="submit">Supprimer</button>
                </form>
              <?php else: ?>
                <p class="admin-closed-message">Match deja cloture. Plus aucune action disponible.</p>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php include("views/components/footer.php"); ?>
