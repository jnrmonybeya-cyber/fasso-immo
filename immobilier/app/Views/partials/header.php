<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - <?php echo $pageTitle ?? 'Accueil'; ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <base href="<?php echo APP_URL; ?>/">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1>🏠 <?php echo APP_NAME; ?></h1>
            <div class="nav-links">
                <a href="<?php echo APP_URL; ?>/public/index.php?route=home">Accueil</a>
                <a href="<?php echo APP_URL; ?>/public/index.php?route=home">Annonces</a>
                <?php if (isset($user) && $user): ?>
                    <a href="<?php echo APP_URL; ?>/public/index.php?route=dashboard">Tableau de bord</a>
                    <a href="<?php echo APP_URL; ?>/public/index.php?route=logout">Déconnexion</a>
                <?php else: ?>
                    <a href="<?php echo APP_URL; ?>/public/index.php?route=login">Connexion</a>
                    <a href="<?php echo APP_URL; ?>/public/index.php?route=register">Inscription</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="container">