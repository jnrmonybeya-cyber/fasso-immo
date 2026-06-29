<?php 
$pageTitle = htmlspecialchars($annonce['titre']); 
?>

<!-- En-tête avec navigation -->
<div class="detail-header">
    <div class="detail-nav">
        <a href="<?php echo APP_URL; ?>/public/index.php?route=home" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour aux annonces
        </a>
        <?php if (isset($user) && $user && $user['type'] == 'client'): ?>
            <?php if (isset($annonce['is_favori']) && $annonce['is_favori']): ?>
                <a href="<?php echo APP_URL; ?>/public/index.php?route=favori_remove&id=<?php echo $annonce['id']; ?>" class="btn-favori-detail active">
                    <i class="fas fa-heart"></i> Retirer des favoris
                </a>
            <?php else: ?>
                <a href="<?php echo APP_URL; ?>/public/index.php?route=favori_add&id=<?php echo $annonce['id']; ?>" class="btn-favori-detail">
                    <i class="fas fa-heart"></i> Ajouter aux favoris
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Section principale -->
<div class="detail-container">
    <div class="detail-grid">
        <!-- Galerie d'images -->
        <div class="detail-gallery">
            <div class="main-image">
                <?php 
                $mainPhoto = '';
                foreach ($photos as $p) {
                    if ($p['is_principal']) {
                        $mainPhoto = $p['photo_path'];
                        break;
                    }
                }
                if (empty($mainPhoto) && !empty($photos)) {
                    $mainPhoto = $photos[0]['photo_path'];
                }
                ?>
                <?php if (!empty($mainPhoto) && file_exists(UPLOAD_DIR . $mainPhoto)): ?>
                    <img src="<?php echo APP_URL; ?>/public/uploads/<?php echo htmlspecialchars($mainPhoto); ?>" 
                         alt="<?php echo htmlspecialchars($annonce['titre']); ?>"
                         id="mainImage">
                <?php else: ?>
                    <div class="no-image-large">
                        <i class="fas fa-home"></i>
                        <p>Aucune image disponible</p>
                    </div>
                <?php endif; ?>
                
                <!-- Badge de statut -->
                <div class="detail-badge <?php echo $annonce['type_bien']; ?>">
                    <?php if ($annonce['type_bien'] == 'location'): ?>
                        <i class="fas fa-key"></i> À louer
                    <?php elseif ($annonce['type_bien'] == 'vente'): ?>
                        <i class="fas fa-hand-holding-usd"></i> À vendre
                    <?php elseif ($annonce['type_bien'] == 'bureau'): ?>
                        <i class="fas fa-briefcase"></i> Bureau
                    <?php else: ?>
                        <i class="fas fa-warehouse"></i> Espace vide
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Miniatures -->
            <?php if (count($photos) > 1): ?>
                <div class="thumbnails">
                    <?php foreach ($photos as $index => $photo): ?>
                        <div class="thumbnail <?php echo ($photo['is_principal'] || $index === 0) ? 'active' : ''; ?>" 
                             onclick="changeImage(this, '<?php echo APP_URL; ?>/public/uploads/<?php echo htmlspecialchars($photo['photo_path']); ?>')">
                            <img src="<?php echo APP_URL; ?>/public/uploads/<?php echo htmlspecialchars($photo['photo_path']); ?>" 
                                 alt="Photo <?php echo $index + 1; ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Informations principales -->
        <div class="detail-info">
            <h1 class="detail-title"><?php echo htmlspecialchars($annonce['titre']); ?></h1>
            
            <div class="detail-location">
                <i class="fas fa-map-marker-alt"></i> 
                <?php echo htmlspecialchars($annonce['zone_geographique']); ?>
            </div>
            
            <div class="detail-price">
                <span class="price-large"><?php echo number_format($annonce['prix'], 0, ',', ' '); ?> FCFA</span>
                <?php if ($annonce['type_bien'] == 'location'): ?>
                    <span class="price-period">/ mois</span>
                <?php endif; ?>
            </div>
            
            <div class="detail-meta">
                <div class="meta-item">
                    <i class="fas fa-arrows-alt"></i>
                    <span><?php echo $annonce['surface'] ? htmlspecialchars($annonce['surface']) . ' m²' : 'Non spécifiée'; ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-tag"></i>
                    <span><?php echo ucfirst(str_replace('_', ' ', $annonce['type_bien'])); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-eye"></i>
                    <span><?php echo $annonce['nb_vues'] ?? 0; ?> vues</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Publiée le <?php echo date('d/m/Y', strtotime($annonce['date_publication'] ?? $annonce['created_at'])); ?></span>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="detail-actions">
                <?php if (isset($user) && $user && $user['type'] == 'client'): ?>
                    <a href="<?php echo APP_URL; ?>/public/index.php?route=visite_demande&id=<?php echo $annonce['id']; ?>" class="btn-visite-detail">
                        <i class="fas fa-calendar-alt"></i> Demander une visite
                    </a>
                    <a href="#" class="btn-contact-detail" onclick="showContact()">
                        <i class="fas fa-phone"></i> Contacter le bailleur
                    </a>
                <?php elseif (!isset($user)): ?>
                    <div class="login-prompt">
                        <p><i class="fas fa-info-circle"></i> Connectez-vous pour contacter le bailleur ou demander une visite</p>
                        <a href="<?php echo APP_URL; ?>/public/index.php?route=login" class="btn-login-detail">
                            <i class="fas fa-sign-in-alt"></i> Se connecter
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Contact (caché par défaut) -->
            <div id="contactInfo" class="contact-info" style="display: none;">
                <h4><i class="fas fa-user"></i> Informations du bailleur</h4>
                <p><strong>Nom :</strong> <?php echo htmlspecialchars($annonce['prenom'] . ' ' . $annonce['nom']); ?></p>
                <p><strong>Email :</strong> <a href="mailto:<?php echo htmlspecialchars($annonce['email']); ?>"><?php echo htmlspecialchars($annonce['email']); ?></a></p>
                <?php if (!empty($annonce['telephone'])): ?>
                    <p><strong>Téléphone :</strong> <a href="tel:<?php echo htmlspecialchars($annonce['telephone']); ?>"><?php echo htmlspecialchars($annonce['telephone']); ?></a></p>
                <?php endif; ?>
                <button onclick="hideContact()" class="btn-close-contact">
                    <i class="fas fa-times"></i> Fermer
                </button>
            </div>
        </div>
    </div>
    
    <!-- Description -->
    <div class="detail-description">
        <h3><i class="fas fa-align-left"></i> Description</h3>
        <div class="description-content">
            <?php echo nl2br(htmlspecialchars($annonce['description'])); ?>
        </div>
    </div>
    
    <!-- Caractéristiques -->
    <div class="detail-features">
        <h3><i class="fas fa-list"></i> Caractéristiques</h3>
        <div class="features-grid">
            <div class="feature-item">
                <i class="fas fa-arrows-alt"></i>
                <span>Surface</span>
                <strong><?php echo $annonce['surface'] ? htmlspecialchars($annonce['surface']) . ' m²' : 'Non spécifiée'; ?></strong>
            </div>
            <div class="feature-item">
                <i class="fas fa-tag"></i>
                <span>Type</span>
                <strong><?php echo ucfirst(str_replace('_', ' ', $annonce['type_bien'])); ?></strong>
            </div>
            <div class="feature-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>Localisation</span>
                <strong><?php echo htmlspecialchars($annonce['zone_geographique']); ?></strong>
            </div>
            <div class="feature-item">
                <i class="fas fa-calendar-check"></i>
                <span>Date de publication</span>
                <strong><?php echo date('d/m/Y', strtotime($annonce['date_publication'] ?? $annonce['created_at'])); ?></strong>
            </div>
        </div>
    </div>
    
    <!-- Actions supplémentaires -->
    <div class="detail-bottom-actions">
        <?php if (isset($user) && $user): ?>
            <?php if ($user['type'] == 'client'): ?>
                <a href="<?php echo APP_URL; ?>/public/index.php?route=visite_demande&id=<?php echo $annonce['id']; ?>" class="btn-bottom">
                    <i class="fas fa-calendar-alt"></i> Demander une visite
                </a>
            <?php endif; ?>
            <?php if ($user['type'] == 'bailleur' && $user['id'] == $annonce['bailleur_id']): ?>
                <a href="<?php echo APP_URL; ?>/public/index.php?route=annonce_edit&id=<?php echo $annonce['id']; ?>" class="btn-bottom edit">
                    <i class="fas fa-edit"></i> Modifier l'annonce
                </a>
                <a href="<?php echo APP_URL; ?>/public/index.php?route=annonce_delete&id=<?php echo $annonce['id']; ?>" class="btn-bottom delete" onclick="return confirm('Supprimer cette annonce ?')">
                    <i class="fas fa-trash"></i> Supprimer
                </a>
            <?php endif; ?>
            <?php if ($user['type'] == 'agent' || $user['type'] == 'manager'): ?>
                <?php if ($annonce['statut'] == 'attente'): ?>
                    <a href="<?php echo APP_URL; ?>/public/index.php?route=annonce_validate&id=<?php echo $annonce['id']; ?>" class="btn-bottom validate">
                        <i class="fas fa-check"></i> Valider l'annonce
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($user['type'] == 'manager' && $annonce['statut'] == 'publie'): ?>
                <a href="<?php echo APP_URL; ?>/public/index.php?route=annonce_retire&id=<?php echo $annonce['id']; ?>" class="btn-bottom retire" onclick="return confirm('Retirer cette annonce ?')">
                    <i class="fas fa-times"></i> Retirer l'annonce
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript pour la galerie et le contact -->
<script>
function changeImage(element, src) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumbnail').forEach(el => el.classList.remove('active'));
    element.classList.add('active');
}

function showContact() {
    document.getElementById('contactInfo').style.display = 'block';
}

function hideContact() {
    document.getElementById('contactInfo').style.display = 'none';
}
</script>

<!-- CSS pour la page de détail -->
<style>
/* ===== Header ===== */
.detail-header {
    margin-bottom: 30px;
}

.detail-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    background: white;
    padding: 15px 25px;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
}

.btn-back {
    color: #2c3e50;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: color 0.3s;
}

.btn-back:hover {
    color: #3498db;
}

.btn-favori-detail {
    padding: 8px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #e74c3c;
    color: white;
}

.btn-favori-detail:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
}

.btn-favori-detail.active {
    background: #95a5a6;
}

/* ===== Container ===== */
.detail-container {
    max-width: 1200px;
    margin: 0 auto;
}

.detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-bottom: 40px;
}

/* ===== Gallery ===== */
.detail-gallery {
    position: sticky;
    top: 100px;
}

.main-image {
    width: 100%;
    height: 450px;
    border-radius: 12px;
    overflow: hidden;
    background: #ecf0f1;
    position: relative;
}

.main-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image-large {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #bdc3c7;
    font-size: 60px;
}

.no-image-large p {
    font-size: 16px;
    margin-top: 10px;
}

.detail-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    padding: 8px 18px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 700;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.detail-badge.location {
    background: rgba(52, 152, 219, 0.9);
}
.detail-badge.vente {
    background: rgba(46, 204, 113, 0.9);
}
.detail-badge.bureau {
    background: rgba(241, 196, 15, 0.9);
}
.detail-badge.espace_vide {
    background: rgba(155, 89, 182, 0.9);
}

.detail-badge i {
    margin-right: 8px;
}

.thumbnails {
    display: flex;
    gap: 10px;
    margin-top: 15px;
    flex-wrap: wrap;
}

.thumbnail {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.3s;
    opacity: 0.7;
}

.thumbnail:hover {
    opacity: 1;
    transform: scale(1.05);
}

.thumbnail.active {
    border-color: #3498db;
    opacity: 1;
}

.thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* ===== Info ===== */
.detail-title {
    font-size: 32px;
    color: #2c3e50;
    margin-bottom: 10px;
    line-height: 1.3;
}

.detail-location {
    font-size: 16px;
    color: #7f8c8d;
    margin-bottom: 20px;
}

.detail-location i {
    color: #3498db;
    margin-right: 8px;
}

.detail-price {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
}

.price-large {
    font-size: 36px;
    font-weight: 700;
    color: #2c3e50;
}

.price-period {
    font-size: 16px;
    color: #7f8c8d;
    font-weight: 400;
}

.detail-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 25px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.meta-item i {
    color: #3498db;
    width: 20px;
}

.meta-item span {
    color: #2c3e50;
    font-size: 14px;
}

/* ===== Actions ===== */
.detail-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.btn-visite-detail {
    flex: 1;
    background: #27ae60;
    color: white;
    padding: 14px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    min-width: 150px;
}

.btn-visite-detail:hover {
    background: #1e8449;
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(39, 174, 96, 0.3);
}

.btn-contact-detail {
    flex: 1;
    background: #3498db;
    color: white;
    padding: 14px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    min-width: 150px;
    cursor: pointer;
    border: none;
}

.btn-contact-detail:hover {
    background: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(52, 152, 219, 0.3);
}

.login-prompt {
    width: 100%;
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.login-prompt p {
    color: #7f8c8d;
    margin-bottom: 10px;
}

.btn-login-detail {
    display: inline-block;
    background: #3498db;
    color: white;
    padding: 10px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-login-detail:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

/* ===== Contact Info ===== */
.contact-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    border-left: 4px solid #27ae60;
}

.contact-info h4 {
    color: #2c3e50;
    margin-bottom: 15px;
}

.contact-info p {
    margin-bottom: 8px;
    color: #2c3e50;
}

.contact-info a {
    color: #3498db;
    text-decoration: none;
}

.contact-info a:hover {
    text-decoration: underline;
}

.btn-close-contact {
    margin-top: 10px;
    background: #e74c3c;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-close-contact:hover {
    background: #c0392b;
}

/* ===== Description ===== */
.detail-description {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.detail-description h3 {
    color: #2c3e50;
    margin-bottom: 15px;
    font-size: 20px;
}

.detail-description h3 i {
    color: #3498db;
    margin-right: 10px;
}

.description-content {
    color: #555;
    line-height: 1.8;
    font-size: 15px;
}

/* ===== Features ===== */
.detail-features {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.detail-features h3 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 20px;
}

.detail-features h3 i {
    color: #3498db;
    margin-right: 10px;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.feature-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
}

.feature-item i {
    font-size: 24px;
    color: #3498db;
    display: block;
    margin-bottom: 5px;
}

.feature-item span {
    display: block;
    color: #7f8c8d;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.feature-item strong {
    display: block;
    color: #2c3e50;
    font-size: 16px;
    margin-top: 5px;
}

/* ===== Bottom Actions ===== */
.detail-bottom-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 10px;
}

.btn-bottom {
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #3498db;
    color: white;
}

.btn-bottom:hover {
    transform: translateY(-2px);
}

.btn-bottom.edit {
    background: #f39c12;
}

.btn-bottom.edit:hover {
    background: #e67e22;
    box-shadow: 0 5px 15px rgba(243, 156, 18, 0.3);
}

.btn-bottom.delete {
    background: #e74c3c;
}

.btn-bottom.delete:hover {
    background: #c0392b;
    box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
}

.btn-bottom.validate {
    background: #27ae60;
}

.btn-bottom.validate:hover {
    background: #1e8449;
    box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
}

.btn-bottom.retire {
    background: #e74c3c;
}

.btn-bottom.retire:hover {
    background: #c0392b;
    box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
}

/* ===== Responsive ===== */
@media (max-width: 992px) {
    .detail-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .detail-gallery {
        position: static;
    }
    
    .main-image {
        height: 350px;
    }
}

@media (max-width: 768px) {
    .detail-nav {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }
    
    .btn-favori-detail {
        justify-content: center;
    }
    
    .detail-title {
        font-size: 26px;
    }
    
    .price-large {
        font-size: 28px;
    }
    
    .detail-meta {
        grid-template-columns: 1fr 1fr;
    }
    
    .detail-actions {
        flex-direction: column;
    }
    
    .btn-visite-detail,
    .btn-contact-detail {
        width: 100%;
        justify-content: center;
    }
    
    .features-grid {
        grid-template-columns: 1fr 1fr;
    }
    
    .detail-bottom-actions {
        flex-direction: column;
    }
    
    .btn-bottom {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .main-image {
        height: 250px;
    }
    
    .detail-meta {
        grid-template-columns: 1fr;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
    
    .thumbnail {
        width: 60px;
        height: 60px;
    }
    
    .detail-price {
        padding: 15px;
    }
    
    .price-large {
        font-size: 24px;
    }
}
</style>