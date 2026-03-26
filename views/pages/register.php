<?php $baseUrl = baseUrl(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription : eBetZone</title>
  <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/cnx.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/footer.css">
</head>
<body>
<?php
startSessionIfNeeded();
require_once 'controllers/authController.php';
$auth = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->registerUser();
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
    <h2>Inscription</h2>
    <form method="post" action="<?= $baseUrl ?>/inscription">
      <label for="username_register">Pseudo :</label>
      <input type="text" name="username" id="username_register" required>

      <label for="login_register">Login :</label>
      <input type="text" name="login" id="login_register" required>

      <label for="password_register">Mot de passe :</label>
      <input type="password" name="password" id="password_register" required>

      <button type="submit">Creer un compte</button>
    </form>

    <p style="margin-top: 16px; text-align: center;">
      Vous avez deja un compte ?
      <a href="<?= $baseUrl ?>/connexion">Connectez-vous</a>
    </p>
  </section>
</main>

<?php include("views/components/footer.php"); ?>
