<?php $pageTitle = 'Connexion'; ?>
<div class="form-container">
    <h2>🔐 Connexion</h2>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="message success">✅ Inscription réussie ! Connectez-vous.</div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="message <?php echo $_SESSION['flash_type'] ?? 'success'; ?>">
            <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
        </div>
        <?php unset($_SESSION['flash_message']); unset($_SESSION['flash_type']); ?>
    <?php endif; ?>
    
    <?php if (!empty($errors['general'])): ?>
        <div class="message error"><?php echo htmlspecialchars($errors['general']); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo APP_URL; ?>/public/index.php?route=login">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($formData['email']); ?>" required>
            <?php if (!empty($errors['email'])): ?>
                <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['email']); ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Mot de passe *</label>
            <input type="password" name="password" required>
            <?php if (!empty($errors['password'])): ?>
                <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['password']); ?></small>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn-submit">Se connecter</button>
    </form>
    
    <p style="margin-top: 20px; text-align: center;">
        Pas encore de compte ? <a href="<?php echo APP_URL; ?>/public/index.php?route=register">Inscrivez-vous</a>
    </p>
    
    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
        <h4>📋 Comptes de test :</h4>
        <p><strong>Manager :</strong> manager1@immobilier.com / 123456</p>
        <p><strong>Agent :</strong> agent@immobilier.com / 123456</p>
        <p><strong>Client :</strong> nemata@gmail.com / 1234</p>
        <p><strong>Bailleur :</strong> innocent@gmail.com / 1234</p>
        <p><small style="color: #7f8c8d;">⚠️ Utilisez ces comptes pour tester, ou créez-en un nouveau.</small></p>
    </div>
</div>