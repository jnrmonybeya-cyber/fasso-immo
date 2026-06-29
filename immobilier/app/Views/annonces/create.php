<?php $pageTitle = 'Publier une annonce'; ?>

<h2><i class="fas fa-edit"></i> Publier une annonce</h2>

<?php if (!empty($errors['general'])): ?>
    <div class="message error"><?php echo htmlspecialchars($errors['general']); ?></div>
<?php endif; ?>

<?php if (isset($user) && $user && $user['type'] == 'bailleur'): ?>
    <div class="message info">
        ℹ<i class="fas fa-infos"></i> Votre annonce sera soumise à validation par un agent avant d'être publiée.
    </div>
<?php endif; ?>

<div class="form-container" style="max-width: 800px;">
    <form method="POST" action="<?php echo APP_URL; ?>/public/index.php?route=annonce_create" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <div class="form-group">
            <label>Titre *</label>
            <input type="text" name="titre" value="<?php echo htmlspecialchars($formData['titre'] ?? ''); ?>" required placeholder="Ex: Belle villa avec piscine">
            <?php if (!empty($errors['titre'])): ?>
                <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['titre']); ?></small>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Description *</label>
            <textarea name="description" rows="6" required placeholder="Décrivez votre bien en détail..."><?php echo htmlspecialchars($formData['description'] ?? ''); ?></textarea>
            <?php if (!empty($errors['description'])): ?>
                <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['description']); ?></small>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Type de bien *</label>
            <select name="type_bien" required>
                <option value="">-- Sélectionner --</option>
                <option value="location" <?php echo (isset($formData['type_bien']) && $formData['type_bien'] == 'location') ? 'selected' : ''; ?>>Location</option>
                <option value="vente" <?php echo (isset($formData['type_bien']) && $formData['type_bien'] == 'vente') ? 'selected' : ''; ?>>Vente</option>
                <!--option value="bureau" <php echo (isset($formData['type_bien']) && $formData['type_bien'] == 'bureau') ? 'selected' : ''; ?>>Bureau</option-->
                <!--option value="espace_vide" <php echo (isset($formData['type_bien']) && $formData['type_bien'] == 'espace_vide') ? 'selected' : ''; ?>>Espace vide</option-->
            </select>
            <?php if (!empty($errors['type_bien'])): ?>
                <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['type_bien']); ?></small>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Zone géographique *</label>
            <input type="text" name="zone" value="<?php echo htmlspecialchars($formData['zone'] ?? ''); ?>" required placeholder="Ex: Ouagadougou, Dapoya">
            <?php if (!empty($errors['zone'])): ?>
                <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['zone']); ?></small>
            <?php endif; ?>
        </div>
        
        <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label>Prix * (FCFA)</label>
                <input type="number" name="prix" step="0.01" value="<?php echo htmlspecialchars($formData['prix'] ?? ''); ?>" required placeholder="Ex: 5000000">
                <?php if (!empty($errors['prix'])): ?>
                    <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['prix']); ?></small>
                <?php endif; ?>
            </div>
            <div>
                <label>Surface (m²)</label>
                <input type="number" name="surface" value="<?php echo htmlspecialchars($formData['surface'] ?? ''); ?>" placeholder="Ex: 120">
                <?php if (!empty($errors['surface'])): ?>
                    <small style="color: #e74c3c;"><?php echo htmlspecialchars($errors['surface']); ?></small>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label>📷 Photos (JPEG, PNG, GIF, WEBP - max 5 Mo)</label>
            <input type="file" name="photos[]" accept="image/*" multiple>
            <small>Maintenez Ctrl (ou Cmd) pour sélectionner plusieurs photos</small>
            <small style="display: block; margin-top: 5px; color: #7f8c8d;">La première photo sera la photo principale de l'annonce.</small>
        </div>
        
        <div style="display: flex; gap: 15px; margin-top: 20px;">
            <button type="submit" class="btn-submit">📤 Publier l'annonce</button>
            <a href="<?php echo APP_URL; ?>/public/index.php?route=dashboard" class="btn-delete" style="padding: 12px 25px; text-decoration: none;">Annuler</a>
        </div>
    </form>
</div>

<style>
    .form-container {
        background: white;
        padding: 35px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin: 0 auto;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #2c3e50;
    }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        transition: border-color 0.3s;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
        border-color: #3498db;
        outline: none;
    }
    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }
    .form-group small {
        color: #7f8c8d;
        font-size: 12px;
        display: block;
        margin-top: 5px;
    }
    .form-group input[type="file"] {
        padding: 10px;
        background: #f8f9fa;
        border: 2px dashed #ddd;
        cursor: pointer;
    }
    .form-group input[type="file"]:hover {
        border-color: #3498db;
        background: #f0f4f8;
    }
    .message.info {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
</style>