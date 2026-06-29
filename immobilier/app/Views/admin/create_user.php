<?php $pageTitle = 'Créer un compte'; ?>

<div class="dashboard-header">
    <div class="header-content">
        <h2><i class="fas fa-user-plus"></i> Créer un nouveau compte</h2>
        <p class="header-subtitle">Ajoutez un utilisateur à la plateforme</p>
    </div>
</div>

<?php if (!empty($errors['general'])): ?>
    <div class="message error"><?php echo htmlspecialchars($errors['general']); ?></div>
<?php endif; ?>

<div class="form-container" style="max-width: 700px;">
    <form method="POST" action="<?php echo APP_URL; ?>/public/index.php?route=admin_create_user">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <div class="form-row">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Nom *</label>
                <input type="text" name="nom" value="<?php echo htmlspecialchars($formData['nom']); ?>" required>
                <?php if (!empty($errors['nom'])): ?>
                    <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['nom']); ?></small>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-user"></i> Prénom *</label>
                <input type="text" name="prenom" value="<?php echo htmlspecialchars($formData['prenom']); ?>" required>
                <?php if (!empty($errors['prenom'])): ?>
                    <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['prenom']); ?></small>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label><i class="fas fa-envelope"></i> Email *</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($formData['email']); ?>" required>
            <?php if (!empty($errors['email'])): ?>
                <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['email']); ?></small>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label><i class="fas fa-phone"></i> Téléphone</label>
            <input type="tel" name="telephone" value="<?php echo htmlspecialchars($formData['telephone']); ?>">
        </div>
        
        <div class="form-group">
            <label><i class="fas fa-map-marker-alt"></i> Adresse</label>
            <textarea name="adresse" rows="3"><?php echo htmlspecialchars($formData['adresse']); ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Mot de passe *</label>
                <input type="password" name="password" required>
                <small>Minimum 6 caractères</small>
                <?php if (!empty($errors['password'])): ?>
                    <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['password']); ?></small>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Confirmer le mot de passe *</label>
                <input type="password" name="password_confirm" required>
                <?php if (!empty($errors['password_confirm'])): ?>
                    <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['password_confirm']); ?></small>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label><i class="fas fa-tag"></i> Type de compte *</label>
            <select name="type" required>
                <option value="client" <?php echo $formData['type'] == 'client' ? 'selected' : ''; ?>>Client</option>
                <option value="bailleur" <?php echo $formData['type'] == 'bailleur' ? 'selected' : ''; ?>>Bailleur</option>
                <option value="agent" <?php echo $formData['type'] == 'agent' ? 'selected' : ''; ?>>Agent</option>
            </select>
            <?php if (!empty($errors['type'])): ?>
                <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['type']); ?></small>
            <?php endif; ?>
        </div>
        
        <div style="display: flex; gap: 15px; margin-top: 20px;">
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Créer le compte
            </button>
            <a href="<?php echo APP_URL; ?>/public/index.php?route=dashboard" class="btn-cancel">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>

<style>
.form-container {
    background: white;
    padding: 35px;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    margin: 0 auto;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
}

.form-group label i {
    color: #3498db;
    margin-right: 6px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s;
    background: #f8f9fa;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #3498db;
    background: white;
    outline: none;
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: #7f8c8d;
    font-size: 12px;
}

.btn-submit {
    background: #27ae60;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-submit:hover {
    background: #1e8449;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
}

.btn-cancel {
    background: #e74c3c;
    color: white;
    padding: 12px 30px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 16px;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-cancel:hover {
    background: #c0392b;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-container {
        padding: 20px;
    }
}
</style>