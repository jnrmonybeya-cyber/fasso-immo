<?php $pageTitle = 'Accueil'; ?>

<!-- Bannière d'accueil -->
<section class="hero-section">
    <div class="hero-content">
        <h1><i class="fas fa-home"></i> <?php echo APP_NAME; ?></h1>
        <p class="hero-subtitle">"Votre rêve immobilier, notre mission"</p>
        <p class="hero-description">Découvrez notre catalogue de biens immobiliers et trouvez la perle rare qui vous correspond.</p>
    </div>
</section>

<!-- Section des filtres -->
<section class="filters-section">
    <div class="filters">
        <h3><i class="icon-filter"></i> Rechercher un bien</h3>
        <form method="GET" action="<?php echo APP_URL; ?>/public/index.php">
            <input type="hidden" name="route" value="home">
            
            <div class="filter-group">
                <div class="filter-item">
                    <label><i class="icon-location"></i> Zone géographique</label>
                    <input type="text" name="zone" placeholder="Ex: Ouagadougou, Dapoya..." 
                           value="<?php echo isset($filters['zone']) ? htmlspecialchars($filters['zone']) : ''; ?>">
                </div>
                
                <div class="filter-item">
                    <label><i class="icon-type"></i> Type de bien</label>
                    <select name="type_bien">
                        <option value="">Tous les types</option>
                        <option value="location" <?php echo (isset($filters['type_bien']) && $filters['type_bien'] == 'location') ? 'selected' : ''; ?>>Location</option>
                        <option value="vente" <?php echo (isset($filters['type_bien']) && $filters['type_bien'] == 'vente') ? 'selected' : ''; ?>>Vente</option>
                    </select>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn-search"><i class="icon-search"></i> Rechercher</button>
                    <?php if (!empty($filters['zone']) || !empty($filters['type_bien'])): ?>
                        <a href="<?php echo APP_URL; ?>/public/index.php" class="btn-reset"><i class="icon-close"></i> Réinitialiser</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Résultats -->
<section class="results-section">
    <?php if (empty($annonces)): ?>
        <div class="empty-state">
            <i class="icon-empty"></i>
            <h3>Aucune annonce disponible</h3>
            <p>Nous n'avons pas trouvé de biens correspondant à vos critères.</p>
            <a href="<?php echo APP_URL; ?>/public/index.php" class="btn-primary">Voir toutes les annonces</a>
        </div>
    <?php else: ?>
        <div class="results-header">
            <h2><i class="icon-list"></i> <?php echo count($annonces); ?> bien(s) trouvé(s)</h2>
        </div>
        
        <div class="catalogue">
            <?php foreach ($annonces as $annonce): ?>
                <div class="card">
                    <!-- Image -->
                    <div class="card-image">
                        <?php if (!empty($annonce['photo_path']) && file_exists(UPLOAD_DIR . $annonce['photo_path'])): ?>
                            <img src="<?php echo APP_URL; ?>/public/uploads/<?php echo htmlspecialchars($annonce['photo_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($annonce['titre']); ?>">
                        <?php else: ?>
                            <div class="card-image-placeholder">
                                <i class="icon-property"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-badge <?php echo htmlspecialchars($annonce['type_bien']); ?>">
                            <?php if ($annonce['type_bien'] == 'location'): ?>
                                <i class="icon-rent"></i> Location
                            <?php elseif ($annonce['type_bien'] == 'vente'): ?>
                                <i class="icon-sale"></i> Vente
                            <?php else: ?>
                                <?php echo ucfirst(str_replace('_', ' ', $annonce['type_bien'])); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Contenu -->
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($annonce['titre']); ?></h3>
                        <p class="description"><?php echo htmlspecialchars(substr($annonce['description'], 0, 120)) . (strlen($annonce['description']) > 120 ? '...' : ''); ?></p>
                        
                        <div class="card-details">
                            <span class="location"><i class="icon-location-small"></i> <?php echo htmlspecialchars($annonce['zone_geographique']); ?></span>
                        </div>
                        
                        <div class="card-footer">
                            <span class="price"><i class="icon-price"></i> <?php echo number_format($annonce['prix'], 0, ',', ' '); ?> FCFA</span>
                            <?php if ($annonce['surface']): ?>
                                <span class="surface"><i class="icon-surface"></i> <?php echo htmlspecialchars($annonce['surface']); ?> m²</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-actions">
                            <a href="<?php echo APP_URL; ?>/public/index.php?route=annonce&id=<?php echo $annonce['id']; ?>" class="btn-details">
                                <i class="icon-eye"></i> Voir détails
                            </a>
                            
                            <?php if (isset($user) && $user && $user['type'] == 'client'): ?>
                                <?php if (isset($annonce['is_favori']) && $annonce['is_favori']): ?>
                                    <a href="<?php echo APP_URL; ?>/public/index.php?route=favori_remove&id=<?php echo $annonce['id']; ?>" class="btn-favori active">
                                        <i class="icon-heart-filled"></i> Retirer
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo APP_URL; ?>/public/index.php?route=favori_add&id=<?php echo $annonce['id']; ?>" class="btn-favori">
                                        <i class="icon-heart"></i> Favoris
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo APP_URL; ?>/public/index.php?route=visite_demande&id=<?php echo $annonce['id']; ?>" class="btn-visite">
                                    <i class="icon-calendar"></i> Visite
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- CSS pour la page d'accueil -->
<style>
    /* ===== Hero Section ===== */
    .hero-section {
        background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
        color: white;
        padding: 60px 40px;
        border-radius: 15px;
        margin-bottom: 40px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(52, 152, 219, 0.3);
    }
    
    .hero-content h1 {
        font-size: 48px;
        margin-bottom: 10px;
        font-weight: 700;
    }
    
    .hero-subtitle {
        font-size: 22px;
        font-style: italic;
        color: rgba(255,255,255,0.9);
        margin-bottom: 15px;
    }
    
    .hero-description {
        font-size: 16px;
        color: rgba(255,255,255,0.8);
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }
    
    /* ===== Filters Section ===== */
    .filters-section {
        margin-bottom: 40px;
    }
    
    .filters {
        background: white;
        padding: 25px 30px;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    }
    
    .filters h3 {
        color: #2c3e50;
        margin-bottom: 20px;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .filter-group {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 20px;
        align-items: end;
    }
    
    .filter-item label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #555;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .filter-item input,
    .filter-item select {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s;
        background: #f8f9fa;
    }
    
    .filter-item input:focus,
    .filter-item select:focus {
        border-color: #3498db;
        background: white;
        outline: none;
    }
    
    .filter-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .btn-search {
        background: #3498db;
        color: white;
        padding: 10px 25px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }
    
    .btn-search:hover {
        background: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
    }
    
    .btn-reset {
        background: #e74c3c;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }
    
    .btn-reset:hover {
        background: #c0392b;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
    }
    
    /* ===== Results Section ===== */
    .results-header {
        margin-bottom: 25px;
    }
    
    .results-header h2 {
        font-size: 20px;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    /* ===== Catalogue ===== */
    .catalogue {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 30px;
    }
    
    .card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    
    .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    }
    
    .card-image {
        width: 100%;
        height: 240px;
        overflow: hidden;
        background: #ecf0f1;
        position: relative;
        flex-shrink: 0;
    }
    
    .card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .card:hover .card-image img {
        transform: scale(1.05);
    }
    
    .card-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #ecf0f1, #bdc3c7);
        font-size: 60px;
        color: #95a5a6;
    }
    
    .card-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 6px;
        backdrop-filter: blur(4px);
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    .card-badge.location {
        background: rgba(52, 152, 219, 0.9);
    }
    
    .card-badge.vente {
        background: rgba(46, 204, 113, 0.9);
    }
    
    .card-badge.bureau {
        background: rgba(241, 196, 15, 0.9);
    }
    
    .card-badge.espace_vide {
        background: rgba(155, 89, 182, 0.9);
    }
    
    .card-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .card-content h3 {
        color: #2c3e50;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
        line-height: 1.3;
    }
    
    .card-content .description {
        color: #7f8c8d;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 15px;
        flex: 1;
    }
    
    .card-details {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 15px;
    }
    
    .card-details .location {
        color: #555;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #ecf0f1;
        margin-bottom: 15px;
    }
    
    .price {
        font-size: 20px;
        font-weight: 700;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .surface {
        color: #7f8c8d;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .card-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .card-actions a {
        flex: 1;
        text-align: center;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-width: 80px;
    }
    
    .btn-details {
        background: #ecf0f1;
        color: #2c3e50;
    }
    
    .btn-details:hover {
        background: #d5dbdb;
        transform: translateY(-2px);
    }
    
    .btn-favori {
        background: #e74c3c;
        color: white;
    }
    
    .btn-favori:hover {
        background: #c0392b;
        transform: translateY(-2px);
    }
    
    .btn-favori.active {
        background: #95a5a6;
    }
    
    .btn-favori.active:hover {
        background: #7f8c8d;
    }
    
    .btn-visite {
        background: #27ae60;
        color: white;
    }
    
    .btn-visite:hover {
        background: #1e8449;
        transform: translateY(-2px);
    }
    
    .btn-primary {
        background: #3498db;
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-block;
    }
    
    .btn-primary:hover {
        background: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(52, 152, 219, 0.4);
    }
    
    /* ===== Empty State ===== */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    }
    
    .empty-state i {
        font-size: 64px;
        color: #bdc3c7;
        display: block;
        margin-bottom: 20px;
    }
    
    .empty-state h3 {
        color: #2c3e50;
        font-size: 24px;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #7f8c8d;
        margin-bottom: 25px;
    }
    
    /* ===== SVG Icons ===== */
    .icon-filter::before {
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    content: "\f002"; /* fa-magnifying-glass */
}

.icon-location::before,
.icon-location-small::before {
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    content: "\f3c5"; /* fa-location-dot */
}

.icon-type::before,
.icon-property::before,
.icon-empty::before {
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    content: "\f015"; /* fa-house */
}

.icon-search::before {
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    content: "\f002";
}

.icon-close::before {
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    content: "\f00d"; /* fa-times */
}

.icon-list::before {
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    content: "\f03a"; /* fa-list */
}

.icon-rent::before {
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    content: "\f084"; /* fa-key */
}

.icon-sale::before {
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    content: "\f81d"; /* fa-sack-dollar */
}

.icon-price::before {
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    content: "\f53a"; /* fa-money-bill-wave */
}

.icon-surface::before {
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    content: "\f545"; /* fa-ruler-combined */
}

.icon-eye::before {
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    content: "\f06e"; /* fa-eye */
}

.icon-heart::before {
    font-family: "Font Awesome 6 Free";
    font-weight: 400;
    content: "\f004"; /* fa-heart (regular) */
}

.icon-heart-filled::before {
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    content: "\f004"; /* fa-heart (solid) */
}

.icon-calendar::before {
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    content: "\f073"; /* fa-calendar-days */
}
    
    /* ===== Responsive ===== */
    @media (max-width: 1024px) {
        .filter-group {
            grid-template-columns: 1fr 1fr;
        }
        .filter-actions {
            grid-column: span 2;
            justify-content: flex-start;
        }
    }
    
    @media (max-width: 768px) {
        .hero-section {
            padding: 40px 20px;
        }
        .hero-content h1 {
            font-size: 32px;
        }
        .hero-subtitle {
            font-size: 18px;
        }
        .hero-description {
            font-size: 14px;
        }
        
        .filters {
            padding: 20px;
        }
        .filter-group {
            grid-template-columns: 1fr;
        }
        .filter-actions {
            grid-column: span 1;
            flex-direction: column;
            width: 100%;
        }
        .filter-actions .btn-search,
        .filter-actions .btn-reset {
            width: 100%;
            justify-content: center;
        }
        
        .catalogue {
            grid-template-columns: 1fr;
        }
        
        .card-actions {
            flex-direction: column;
        }
        .card-actions a {
            width: 100%;
        }
        
        .card-footer {
            flex-direction: column;
            gap: 8px;
            align-items: flex-start;
        }
    }
    
    @media (max-width: 480px) {
        .hero-content h1 {
            font-size: 26px;
        }
        .hero-subtitle {
            font-size: 16px;
        }
        .card-image {
            height: 200px;
        }
        .price {
            font-size: 18px;
        }
    }
</style>