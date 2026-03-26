<?php include("views/components/header.php"); ?>
<?php
require_once("models/userModel.php");

$baseUrl = baseUrl();
$userModel = new UserModel();
$classement = $userModel->getClassement();
?>

<main>
  <section class="hero">
    <h2>Pariez sur vos equipes Overwatch preferées</h2>
    <p>-----------------</p>
    <div class="hero-buttons">
      <a href="<?= $baseUrl ?>/matchs" class="btn-matchs">Voir les matchs</a>
      <a href="<?= $baseUrl ?>/paris" class="btn-matchs">Mes paris</a>
    </div>
  </section>

  <section class="matchs">
    <h2>Classement des joueurs</h2>

    <?php if (empty($classement)): ?>
      <p>Aucun joueur classe pour le moment.</p>
    <?php else: ?>
      <?php foreach ($classement as $index => $joueur): ?>
        <div class="match">
          <p>
            <strong>#<?= $index + 1 ?></strong>
            <?= htmlspecialchars($joueur['username']) ?>
            <span style="color: gray;">(@<?= htmlspecialchars($joueur['login']) ?>)</span>
          </p>
          <p><strong><?= (int) $joueur['points'] ?> point(s)</strong></p>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>
</main>

<?php include("views/components/footer.php"); ?>
