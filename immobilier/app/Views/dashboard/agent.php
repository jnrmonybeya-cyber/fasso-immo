<?php $pageTitle = 'Tableau de bord - Agent'; ?>

<h2>👋 Bonjour, <?php echo htmlspecialchars($user_name); ?></h2>

<div class="dashboard-stats">
    <div class="stat-card">
        <h3>⏳ Annonces à valider</h3>
        <div class="number"><?php echo $attente_count ?? 0; ?></div>
    </div>
    <div class="stat-card">
        <h3>📅 Visites à valider</h3>
        <div class="number"><?php echo $demandes_count ?? 0; ?></div>
    </div>
    <div class="stat-card">
        <h3>🏠 Annonces publiées</h3>
        <div class="number"><?php echo $annonces_publiees ?? 0; ?></div>
    </div>
</div>

<div style="margin-bottom: 20px;">
    <a href="<?php echo APP_URL; ?>/public/index.php?route=annonce_create" class="btn-submit" style="width: auto;">➕ Ajouter une annonce</a>
</div>

<h3>⏳ Annonces en attente de validation</h3>
<div class="annonces-list">
    <?php if (empty($annonces_attente)): ?>
        <p style="padding: 20px; color: #7f8c8d; text-align: center;">✅ Aucune annonce en attente.</p>
    <?php else: ?>
        <?php foreach ($annonces_attente as $annonce): ?>
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
                        👤 Bailleur: <?php echo htmlspecialchars($annonce['prenom'] . ' ' . $annonce['nom']); ?><br>
                        📍 <?php echo htmlspecialchars($annonce['zone_geographique']); ?> - 💰 <?php echo number_format($annonce['prix'], 0, ',', ' '); ?> FCFA
                    </div>
                </div>
                <div>
                    <a href="<?php echo APP_URL; ?>/public/index.php?route=annonce_validate&id=<?php echo $annonce['id']; ?>" class="btn-edit" style="background: #27ae60; padding: 8px 15px;">✅ Valider</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<h3>📅 Demandes de visite en attente</h3>
<div class="annonces-list">
    <?php if (empty($demandes_visite)): ?>
        <p style="padding: 20px; color: #7f8c8d; text-align: center;">✅ Aucune demande en attente.</p>
    <?php else: ?>
        <?php foreach ($demandes_visite as $demande): ?>
            <div class="annonce-item">
                <div>
                    <strong>Visite pour: <?php echo htmlspecialchars($demande['titre']); ?></strong><br>
                    👤 Client: <?php echo htmlspecialchars($demande['prenom'] . ' ' . $demande['nom']); ?><br>
                    📅 Date: <?php echo htmlspecialchars($demande['date_visite'] . ' à ' . $demande['heure_visite']); ?><br>
                    💬 Message: <?php echo htmlspecialchars($demande['message'] ?? 'Aucun message'); ?>
                </div>
                <div>
                    <a href="<?php echo APP_URL; ?>/public/index.php?route=visite_validate&id=<?php echo $demande['id']; ?>" class="btn-edit" style="background: #27ae60; padding: 8px 15px;">✅ Valider</a>
                    <a href="<?php echo APP_URL; ?>/public/index.php?route=visite_refuse&id=<?php echo $demande['id']; ?>" class="btn-delete" style="padding: 8px 15px;">❌ Refuser</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>