<?php 
$pageTitle = 'Tableau de bord - Manager'; 
?>

<!-- En-tête -->
<div class="dashboard-header">
    <div class="header-content">
        <h2><i class="fas fa-user-cog"></i> Bonjour, <?php echo htmlspecialchars($user_name); ?></h2>
        <p class="header-subtitle">Gérez l'ensemble de la plateforme immobilière</p>
    </div>
    <div class="header-actions">
        <a href="<?php echo APP_URL; ?>/public/index.php?route=admin_create_user" class="btn-primary">
            <i class="fas fa-user-plus"></i> Créer un compte
        </a>
    </div>
</div>

<!-- Statistiques -->
<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon" style="background: #3498db;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3>Clients</h3>
            <div class="number"><?php echo $users_stats['client'] ?? 0; ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #27ae60;">
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-info">
            <h3>Bailleurs</h3>
            <div class="number"><?php echo $users_stats['bailleur'] ?? 0; ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #f39c12;">
            <i class="fas fa-user-tie"></i>
        </div>
        <div class="stat-info">
            <h3>Agents</h3>
            <div class="number"><?php echo $users_stats['agent'] ?? 0; ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #e74c3c;">
            <i class="fas fa-home"></i>
        </div>
        <div class="stat-info">
            <h3>Annonces</h3>
            <div class="number"><?php echo $total_annonces ?? 0; ?></div>
        </div>
    </div>
</div>

<!-- Statut des annonces -->
<div class="stats-extra">
    <div class="stat-card small">
        <div class="stat-icon" style="background: #f39c12; width:40px; height:40px; font-size:16px;">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <h3>En attente</h3>
            <div class="number" style="font-size:24px;"><?php echo $annonces_stats['attente'] ?? 0; ?></div>
        </div>
    </div>
    <div class="stat-card small">
        <div class="stat-icon" style="background: #27ae60; width:40px; height:40px; font-size:16px;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <h3>Publiées</h3>
            <div class="number" style="font-size:24px;"><?php echo $annonces_stats['publie'] ?? 0; ?></div>
        </div>
    </div>
    <div class="stat-card small">
        <div class="stat-icon" style="background: #e74c3c; width:40px; height:40px; font-size:16px;">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-info">
            <h3>Retirées</h3>
            <div class="number" style="font-size:24px;"><?php echo $annonces_stats['retire'] ?? 0; ?></div>
        </div>
    </div>
</div>

<!-- Annonces en attente de validation -->
<h3 class="section-title">
    <i class="fas fa-clock" style="color: #f39c12;"></i> 
    Annonces en attente de validation
    <span class="badge-count"><?php echo $annonces_stats['attente'] ?? 0; ?></span>
</h3>
<div class="annonces-list">
    <?php
    // Récupérer les annonces en attente
    if (!isset($annonces_attente_list)) {
        $annonceModel = new App\Models\Annonce();
        $annoncesAttente = $annonceModel->getByStatus('attente');
    } else {
        $annoncesAttente = $annonces_attente_list;
    }
    ?>
    <?php if (empty($annoncesAttente)): ?>
        <div class="empty-state-small">
            <i class="fas fa-check-circle" style="color: #27ae60; font-size: 40px;"></i>
            <p>Aucune annonce en attente de validation.</p>
        </div>
    <?php else: ?>
        <?php foreach ($annoncesAttente as $annonce): ?>
            <div class="annonce-item">
                <div class="annonce-info">
                    <div class="annonce-image">
                        <?php 
                        // Récupérer la photo
                        $photoPath = '';
                        if (!empty($annonce['photo_path']) && file_exists(UPLOAD_DIR . $annonce['photo_path'])) {
                            $photoPath = APP_URL . '/public/uploads/' . $annonce['photo_path'];
                        } else {
                            try {
                                $photoModel = new App\Models\Photo();
                                $photo = $photoModel->getPrincipal($annonce['id']);
                                if ($photo && file_exists(UPLOAD_DIR . $photo['photo_path'])) {
                                    $photoPath = APP_URL . '/public/uploads/' . $photo['photo_path'];
                                }
                            } catch (Exception $e) {}
                        }
                        ?>
                        <?php if (!empty($photoPath)): ?>
                            <img src="<?php echo $photoPath; ?>" alt="<?php echo htmlspecialchars($annonce['titre']); ?>">
                        <?php else: ?>
                            <div class="no-image"><i class="fas fa-home"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="annonce-details">
                        <strong><?php echo htmlspecialchars($annonce['titre']); ?></strong>
                        <div class="annonce-meta">
                            <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($annonce['prenom'] . ' ' . $annonce['nom']); ?></span>
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($annonce['zone_geographique']); ?></span>
                            <span><i class="fas fa-tag"></i> <?php echo number_format($annonce['prix'], 0, ',', ' '); ?> FCFA</span>
                            <span class="status-badge waiting"><i class="fas fa-clock"></i> En attente</span>
                        </div>
                    </div>
                </div>
                <div class="annonce-actions">
                    <a href="<?php echo APP_URL; ?>/public/index.php?route=annonce_validate&id=<?php echo $annonce['id']; ?>" class="btn-validate">
                        <i class="fas fa-check"></i> Valider
                    </a>
                    <a href="<?php echo APP_URL; ?>/public/index.php?route=annonce_edit&id=<?php echo $annonce['id']; ?>" class="btn-edit-small">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Annonces publiées -->
<h3 class="section-title">
    <i class="fas fa-check-circle" style="color: #27ae60;"></i> 
    Annonces publiées
    <span class="badge-count"><?php echo $annonces_stats['publie'] ?? 0; ?></span>
</h3>
<div class="annonces-list">
    <?php
    // Récupérer les annonces publiées
    if (!isset($annonces_publiees_list)) {
        $annonceModel = new App\Models\Annonce();
        $annoncesPubliees = $annonceModel->getByStatus('publie');
    } else {
        $annoncesPubliees = $annonces_publiees_list;
    }
    ?>
    <?php if (empty($annoncesPubliees)): ?>
        <div class="empty-state-small">
            <i class="fas fa-home" style="color: #bdc3c7; font-size: 40px;"></i>
            <p>Aucune annonce publiée.</p>
        </div>
    <?php else: ?>
        <?php foreach ($annoncesPubliees as $annonce): ?>
            <div class="annonce-item">
                <div class="annonce-info">
                    <div class="annonce-image">
                        <?php 
                        $photoPath = '';
                        if (!empty($annonce['photo_path']) && file_exists(UPLOAD_DIR . $annonce['photo_path'])) {
                            $photoPath = APP_URL . '/public/uploads/' . $annonce['photo_path'];
                        } else {
                            try {
                                $photoModel = new App\Models\Photo();
                                $photo = $photoModel->getPrincipal($annonce['id']);
                                if ($photo && file_exists(UPLOAD_DIR . $photo['photo_path'])) {
                                    $photoPath = APP_URL . '/public/uploads/' . $photo['photo_path'];
                                }
                            } catch (Exception $e) {}
                        }
                        ?>
                        <?php if (!empty($photoPath)): ?>
                            <img src="<?php echo $photoPath; ?>" alt="<?php echo htmlspecialchars($annonce['titre']); ?>">
                        <?php else: ?>
                            <div class="no-image"><i class="fas fa-home"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="annonce-details">
                        <strong><?php echo htmlspecialchars($annonce['titre']); ?></strong>
                        <div class="annonce-meta">
                            <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($annonce['prenom'] . ' ' . $annonce['nom']); ?></span>
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($annonce['zone_geographique']); ?></span>
                            <span><i class="fas fa-tag"></i> <?php echo number_format($annonce['prix'], 0, ',', ' '); ?> FCFA</span>
                            <span class="status-badge published"><i class="fas fa-check-circle"></i> Publiée</span>
                        </div>
                    </div>
                </div>
                <div class="annonce-actions">
                    <a href="<?php echo APP_URL; ?>/public/index.php?route=annonce_retire&id=<?php echo $annonce['id']; ?>" class="btn-retire" onclick="return confirm('Retirer cette annonce ?')">
                        <i class="fas fa-times"></i> Retirer
                    </a>
                    <a href="<?php echo APP_URL; ?>/public/index.php?route=annonce_edit&id=<?php echo $annonce['id']; ?>" class="btn-edit-small">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Gestion des utilisateurs -->
<h3 class="section-title">
    <i class="fas fa-users" style="color: #3498db;"></i> 
    Gestion des utilisateurs
</h3>
<div class="annonces-list">
    <?php if (empty($users)): ?>
        <div class="empty-state-small">
            <i class="fas fa-user" style="color: #bdc3c7; font-size: 40px;"></i>
            <p>Aucun utilisateur trouvé.</p>
        </div>
    <?php else: ?>
        <?php foreach ($users as $user): ?>
            <div class="annonce-item">
                <div class="annonce-info">
                    <div class="user-avatar">
                        <?php if ($user['type_utilisateur'] == 'client'): ?>
                            <i class="fas fa-user" style="color: #3498db;"></i>
                        <?php elseif ($user['type_utilisateur'] == 'bailleur'): ?>
                            <i class="fas fa-building" style="color: #27ae60;"></i>
                        <?php elseif ($user['type_utilisateur'] == 'agent'): ?>
                            <i class="fas fa-user-tie" style="color: #f39c12;"></i>
                        <?php else: ?>
                            <i class="fas fa-user-cog" style="color: #e74c3c;"></i>
                        <?php endif; ?>
                    </div>
                    <div class="annonce-details">
                        <strong><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></strong>
                        <div class="annonce-meta">
                            <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></span>
                            <span><i class="fas fa-tag"></i> <?php echo ucfirst($user['type_utilisateur']); ?></span>
                            <?php if ($user['type_utilisateur'] == 'client' && !empty($user['agent_id'])): ?>
                                <span><i class="fas fa-user-tie"></i> Agent #<?php echo $user['agent_id']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="annonce-actions">
                    <?php if ($user['type_utilisateur'] == 'client'): ?>
                        <a href="<?php echo APP_URL; ?>/public/index.php?route=admin_assign_agent&id=<?php echo $user['id']; ?>" class="btn-assign">
                            <i class="fas fa-user-plus"></i> Assigner
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- CSS Amélioré -->
<style>
/* ===== Dashboard Header ===== */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 30px;
    background: white;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
}

.header-content h2 {
    font-size: 24px;
    color: #2c3e50;
    margin-bottom: 5px;
}

.header-content h2 i {
    color: #3498db;
    margin-right: 10px;
}

.header-subtitle {
    color: #7f8c8d;
    font-size: 14px;
}

.btn-primary {
    background: #3498db;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary:hover {
    background: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
}

/* ===== Statistics ===== */
.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-3px);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    flex-shrink: 0;
}

.stat-info h3 {
    color: #7f8c8d;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 2px;
}

.stat-info .number {
    font-size: 28px;
    font-weight: 700;
    color: #2c3e50;
}

.stats-extra {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-bottom: 30px;
}

.stat-card.small {
    padding: 15px 20px;
}

.stat-card.small .stat-info .number {
    font-size: 22px;
}

/* ===== Section Title ===== */
.section-title {
    color: #2c3e50;
    font-size: 18px;
    margin: 30px 0 15px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    font-size: 20px;
}

.badge-count {
    background: #ecf0f1;
    color: #2c3e50;
    padding: 2px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
    margin-left: 5px;
}

/* ===== Annonces List ===== */
.annonces-list {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
}

.annonce-item {
    padding: 15px 20px;
    border-bottom: 1px solid #ecf0f1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
    transition: background 0.3s;
}

.annonce-item:hover {
    background: #f8f9fa;
}

.annonce-item:last-child {
    border-bottom: none;
}

.annonce-info {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
    min-width: 200px;
}

.annonce-image {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
    background: #ecf0f1;
}

.annonce-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.annonce-image .no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #bdc3c7;
    font-size: 24px;
}

.user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #ecf0f1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.annonce-details {
    flex: 1;
}

.annonce-details strong {
    color: #2c3e50;
    font-size: 15px;
    display: block;
    margin-bottom: 5px;
}

.annonce-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 13px;
    color: #7f8c8d;
}

.annonce-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.annonce-meta i {
    font-size: 12px;
    color: #95a5a6;
}

/* ===== Status Badges ===== */
.status-badge {
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.status-badge.waiting {
    background: #fff3cd;
    color: #856404;
}

.status-badge.published {
    background: #d4edda;
    color: #155724;
}

.status-badge.retired {
    background: #f8d7da;
    color: #721c24;
}

/* ===== Actions ===== */
.annonce-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.annonce-actions a {
    padding: 6px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-validate {
    background: #27ae60;
    color: white;
}

.btn-validate:hover {
    background: #1e8449;
    transform: translateY(-2px);
}

.btn-retire {
    background: #e74c3c;
    color: white;
}

.btn-retire:hover {
    background: #c0392b;
    transform: translateY(-2px);
}

.btn-assign {
    background: #3498db;
    color: white;
}

.btn-assign:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

.btn-edit-small {
    background: #ecf0f1;
    color: #2c3e50;
    padding: 6px 10px !important;
}

.btn-edit-small:hover {
    background: #d5dbdb;
    transform: translateY(-2px);
}

/* ===== Empty States ===== */
.empty-state-small {
    text-align: center;
    padding: 30px 20px;
    color: #7f8c8d;
}

.empty-state-small p {
    margin-top: 10px;
    font-size: 14px;
}

/* ===== Responsive ===== */
@media (max-width: 1024px) {
    .dashboard-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        text-align: center;
        padding: 20px;
    }
    
    .header-content h2 {
        font-size: 20px;
    }
    
    .dashboard-stats {
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    
    .stat-card {
        padding: 15px;
    }
    
    .stat-card .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    
    .stat-info .number {
        font-size: 22px;
    }
    
    .annonce-item {
        flex-direction: column;
        align-items: stretch;
    }
    
    .annonce-info {
        flex-direction: column;
        text-align: center;
    }
    
    .annonce-meta {
        justify-content: center;
    }
    
    .annonce-actions {
        justify-content: center;
    }
    
    .stats-extra {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 480px) {
    .dashboard-stats {
        grid-template-columns: 1fr;
    }
    
    .stats-extra {
        grid-template-columns: 1fr;
    }
    
    .annonce-meta {
        flex-direction: column;
        align-items: center;
        gap: 5px;
    }
    
    .annonce-item {
        padding: 12px 15px;
    }
    
    .annonce-actions {
        width: 100%;
    }
    
    .annonce-actions a {
        flex: 1;
        justify-content: center;
    }
}
</style>