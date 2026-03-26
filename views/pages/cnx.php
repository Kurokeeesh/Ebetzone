<?php $baseUrl = baseUrl(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion : eBetZone</title>
  <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/cnx.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/footer.css">
</head>
<body>
<?php
startSessionIfNeeded();
require_once 'controllers/authController.php';
$auth = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->loginUser();
}

$flash = getFlashMessage();
?>

<?php if ($flash): ?>
  <div class="flash-message flash-<?= htmlspecialchars($flash['type']) ?>" style="padding:12px 20px; text-align:center;">
    <?= htmlspecialchars($flash['message']) ?>
  </div>
<?php endif; ?>

<main class="connexion-container">
  <section class="login">
    <h2>Connexion</h2>
    <form method="post" action="<?= $baseUrl ?>/connexion">
      <label for="login">Login :</label>
      <input type="text" name="login" id="login" required>

      <label for="password">Mot de passe :</label>
      <input type="password" name="password" id="password" required>

      <button type="submit">Se connecter</button>
    </form>

    <p style="margin-top: 16px; text-align: center;">
      Vous n'avez pas de compte ?
      <a href="<?= $baseUrl ?>/inscription">Inscrivez-vous</a>
    </p>
  </section>
</main>

<?php include("views/components/footer.php"); ?>
