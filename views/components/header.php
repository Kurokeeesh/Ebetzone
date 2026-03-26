<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>eBetZone</title>
  <?php $baseUrl = baseUrl(); ?>
  <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/header.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/home.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/cnx.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/matchs.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/footer.css">
</head>
<body>
  <?php
  $user = currentUser();
  $flash = getFlashMessage();
  ?>
  <header>
    <h1>eBetZone 🎯</h1>
    <nav>
      <a href="<?= $baseUrl ?>/home">Accueil</a>
      <a href="<?= $baseUrl ?>/matchs">Matchs</a>
      <a href="<?= $baseUrl ?>/paris">Mes Paris</a>

    <?php if ($user && ($user['role'] ?? '') === 'admin'): ?>
      <a href="<?= $baseUrl ?>/admin">Administration</a>
    <?php endif; ?>

    <?php if ($user): ?>
      <span>Points : <?= (int) ($user['points'] ?? 0) ?></span>
      <a href="<?= $baseUrl ?>/logout">Déconnexion</a>
    <?php endif; ?>
    </nav>
  </header>
  <?php if ($flash): ?>
    <div class="flash-message flash-<?= htmlspecialchars($flash['type']) ?>" style="padding:12px 20px; text-align:center;">
      <?= htmlspecialchars($flash['message']) ?>
    </div>
  <?php endif; ?>
