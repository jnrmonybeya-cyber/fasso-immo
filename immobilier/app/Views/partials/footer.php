    </div>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>🏠 <?php echo APP_NAME; ?></h3>
                <p class="slogan">"Votre rêve immobilier, notre mission"</p>
                <p>Depuis 2020, nous vous accompagnons dans tous vos projets immobiliers avec professionnalisme et transparence.</p>
            </div>
            <div class="footer-section">
                <h4>Liens rapides</h4>
                <ul>
                    <li><a href="<?php echo APP_URL; ?>/public/index.php">Accueil</a></li>
                    <li><a href="<?php echo APP_URL; ?>/public/index.php?route=home">Annonces</a></li>
                    <?php if (!isset($user) || !$user): ?>
                        <li><a href="<?php echo APP_URL; ?>/public/index.php?route=login">Connexion</a></li>
                        <li><a href="<?php echo APP_URL; ?>/public/index.php?route=register">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact</h4>
                <p>📧 contact@<?php echo strtolower(str_replace(' ', '', APP_NAME)); ?>.com</p>
                <p>📞 +226 66 74 81 82</p>
                <p>📍 Rue THOMAS SANKARA, Ouagadougou</p>
            </div>
            <div class="footer-section">
                <h4>Suivez-nous</h4>
                <div class="social-links">
                    <a href="#" class="social-link">📘</a>
                    <a href="#" class="social-link">📸</a>
                    <a href="#" class="social-link">🐦</a>
                    <a href="#" class="social-link">💼</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>