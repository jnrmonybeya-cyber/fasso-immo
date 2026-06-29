<?php $pageTitle = 'Tableau de bord - Client'; ?>

<h2>👋 Bonjour, <?php echo htmlspecialchars($user_name); ?></h2>

<div class="dashboard-stats">
    <div class="stat-card">
        <h3>⭐ Favoris</h3>
        <div class="number"><?php echo $favoris_count ?? 0; ?></div>
    </div>
    <div class="stat-card">
        <h3>📅 Visites demandées</h3>
        <div class="number"><?php echo $visites_count ?? 0; ?></div>
    </div>
</div>

<h3>⭐ Mes favoris</h3>
<div class="annonces-list">
    <?php if (empty($favoris)): ?>
        <p style="padding: 20px; color: #7f8c8d; text-align: center;">Vous n'avez pas encore de favoris.</p>
    <?php else: ?>
        <?php foreach ($favoris as $fav): ?>
            <div class="annonce-item">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <?php if (!empty($fav['photo_path']) && file_exists(UPLOAD_DIR . $fav['photo_path'])): ?>
                        <img src="<?php echo APP_URL; ?>/public/uploads/<?php echo htmlspecialchars($fav['photo_path']); ?>" 
                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                    <?php else: ?>
                        <div style="width: 60px; height: 60px; background: #ecf0f1; border-radius: 5px; display: flex; align-items: center; justify-content: center;">🏠</div>
                    <?php endif; ?>
                    <div>
                        <strong><?php echo htmlspecialchars($fav['titre']); ?></strong><br>
                        <?php echo htmlspecialchars($fav['zone_geographique']); ?> - <?php echo number_format($fav['prix'], 0, ',', ' '); ?> FCFA
                    </div>
                </div>
                <div>
                    <a href="<?php echo APP_URL; ?>/public/index.php?route=visite_demande&id=<?php echo $fav['id']; ?>" class="btn-visite">📅 Demander visite</a>
                    <a href="<?php echo APP_URL; ?>/public/index.php?route=favori_remove&id=<?php echo $fav['id']; ?>" class="btn-delete">❌ Retirer</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<h3>📅 Mes demandes de visite</h3>
<div class="annonces-list">
    <?php if (empty($visites)): ?>
        <p style="padding: 20px; color: #7f8c8d; text-align: center;">Vous n'avez pas encore de demandes de visite.</p>
    <?php else: ?>
        <?php foreach ($visites as $visite): ?>
            <div class="annonce-item">
                <div>
                    <strong><?php echo htmlspecialchars($visite['titre']); ?></strong><br>
                    📅 Date: <?php echo htmlspecialchars($visite['date_visite']); ?> à <?php echo htmlspecialchars($visite['heure_visite']); ?><br>
                    💬 Message: <?php echo htmlspecialchars($visite['message'] ?? 'Aucun message'); ?>
                </div>
                <div>
                    <span class="status <?php echo $visite['statut']; ?>">
                        <?php echo ucfirst($visite['statut']); ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>