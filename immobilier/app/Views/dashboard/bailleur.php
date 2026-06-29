<?php $pageTitle = 'Tableau de bord - Bailleur'; ?>

<h2>👋 Bonjour, <?php echo htmlspecialchars($user_name); ?></h2>

<div class="dashboard-stats">
    <div class="stat-card">
        <h3>⏳ En attente</h3>
        <div class="number"><?php echo $stats['attente'] ?? 0; ?></div>
    </div>
    <div class="stat-card">
        <h3>✅ Publiées</h3>
        <div class="number"><?php echo $stats['publie'] ?? 0; ?></div>
    </div>
    <div class="stat-card">
        <h3>❌ Retirées</h3>
        <div class="number"><?php echo $stats['retire'] ?? 0; ?></div>
    </div>
</div>

<div style="margin-bottom: 20px;">
    <a href="<?php echo APP_URL; ?>/public/index.php?route=annonce_create" class="btn-submit" style="width: auto;">➕ Publier une annonce</a>
</div>

<h3>📋 Mes annonces</h3>
<div class="annonces-list">
    <?php if (empty($annonces)): ?>
        <p style="padding: 20px; color: #7f8c8d; text-align: center;">Vous n'avez pas encore publié d'annonces.</p>
    <?php else: ?>
        <?php foreach ($annonces as $annonce): ?>
            <div class="annonce-item">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <?php if (!empty($annonce['photo_path']) && file_exists(UPLOAD_DIR . $annonce['photo_path'])): ?>
                        <img src="<?php echo APP_URL; ?>/public/uploads/<?php echo htmlspecialchars($annonce['photo_path']); ?>" 
                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                    <?php else: ?>
                        <div style="width: 60px; height: 60px; background: #ecf0f1; border-radius: 5px; display: flex; align-items: center; justify-content: center;">🏠</div>
                    <?php endif; ?>
                    <div>
                        <strong><?php echo htmlspecialchars($annonce['titre']); ?></strong><br>
                        <?php echo htmlspecialchars($annonce['zone_geographique']); ?> - <?php echo number_format($annonce['prix'], 0, ',', ' '); ?> FCFA<br>
                        <span class="status <?php echo $annonce['statut']; ?>"><?php echo ucfirst($annonce['statut']); ?></span>
                    </div>
                </div>
                <div>
                    <a href="<?php echo APP_URL; ?>/public/index.php?route=annonce_edit&id=<?php echo $annonce['id']; ?>" class="btn-edit">✏️ Modifier</a>
                    <a href="<?php echo APP_URL; ?>/public/index.php?route=annonce_delete&id=<?php echo $annonce['id']; ?>" class="btn-delete" onclick="return confirm('Supprimer cette annonce ?')">🗑️ Supprimer</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>