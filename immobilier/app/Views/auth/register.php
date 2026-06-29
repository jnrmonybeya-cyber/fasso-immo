<?php $pageTitle = 'Inscription'; ?>
<div class="form-container">
    <h2>📝 Inscription</h2>
    
    <?php if (!empty($errors['general'])): ?>
        <div class="message error"><?php echo htmlspecialchars($errors['general']); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo APP_URL; ?>/public/index.php?route=register">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <div class="form-group">
            <label>Nom *</label>
            <input type="text" name="nom" value="<?php echo htmlspecialchars($formData['nom']); ?>" required>
            <?php if (!empty($errors['nom'])): ?>
                <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['nom']); ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Prénom *</label>
            <input type="text" name="prenom" value="<?php echo htmlspecialchars($formData['prenom']); ?>" required>
            <?php if (!empty($errors['prenom'])): ?>
                <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['prenom']); ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($formData['email']); ?>" required>
            <?php if (!empty($errors['email'])): ?>
                <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['email']); ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Téléphone</label>
            <input type="tel" name="telephone" value="<?php echo htmlspecialchars($formData['telephone']); ?>">
        </div>
        <div class="form-group">
            <label>Adresse</label>
            <textarea name="adresse" rows="3"><?php echo htmlspecialchars($formData['adresse']); ?></textarea>
        </div>
        <div class="form-group">
            <label>Mot de passe *</label>
            <input type="password" name="password" required>
            <small>Minimum 6 caractères</small>
            <?php if (!empty($errors['password'])): ?>
                <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['password']); ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Confirmer le mot de passe *</label>
            <input type="password" name="password_confirm" required>
            <?php if (!empty($errors['password_confirm'])): ?>
                <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['password_confirm']); ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Type de compte *</label>
            <select name="type" required>
                <option value="client" <?php echo $formData['type'] == 'client' ? 'selected' : ''; ?>>Client (acheteur/locataire)</option>
                <option value="bailleur" <?php echo $formData['type'] == 'bailleur' ? 'selected' : ''; ?>>Bailleur (propriétaire)</option>
            </select>
            <?php if (!empty($errors['type'])): ?>
                <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['type']); ?></small>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn-submit">S'inscrire</button>
    </form>
    
    <p style="margin-top: 20px; text-align: center;">
        Déjà un compte ? <a href="<?php echo APP_URL; ?>/public/index.php?route=login">Connectez-vous</a>
    </p>
</div>