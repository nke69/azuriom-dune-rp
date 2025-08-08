#!/bin/bash

# ==================================================
# Script d'installation du plugin Dune RP pour Azuriom
# Version: 1.1.0
# Auteur: nke69
# ==================================================

set -e  # Arrêter en cas d'erreur

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction d'affichage
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Vérifier si on est dans le bon dossier
if [ ! -f "artisan" ]; then
    log_error "Ce script doit être exécuté depuis la racine d'Azuriom"
    exit 1
fi

# En-tête
echo ""
echo "=================================================="
echo "   Installation du Plugin Dune RP pour Azuriom   "
echo "=================================================="
echo ""

# Étape 1: Vérification de l'environnement
log_info "Vérification de l'environnement..."

# Vérifier PHP
if ! command -v php &> /dev/null; then
    log_error "PHP n'est pas installé"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;")
log_success "PHP $PHP_VERSION détecté"

# Vérifier Composer
if ! command -v composer &> /dev/null; then
    log_error "Composer n'est pas installé"
    exit 1
fi

log_success "Composer détecté"

# Étape 2: Vérifier l'existence du plugin
PLUGIN_PATH="plugins/dune-rp"

if [ ! -d "$PLUGIN_PATH" ]; then
    log_error "Le plugin n'est pas trouvé dans $PLUGIN_PATH"
    log_info "Veuillez d'abord extraire le plugin dans le dossier plugins/"
    exit 1
fi

log_success "Plugin trouvé dans $PLUGIN_PATH"

# Étape 3: Installation des dépendances Composer
log_info "Installation des dépendances Composer..."
cd $PLUGIN_PATH
composer install --no-dev --optimize-autoloader
cd ../..
log_success "Dépendances installées"

# Étape 4: Exécution des migrations
log_info "Exécution des migrations..."
php artisan migrate --path=$PLUGIN_PATH/database/migrations --force
log_success "Migrations exécutées"

# Étape 5: Publication des assets
log_info "Publication des assets..."

# Créer le dossier public/plugins/dune-rp s'il n'existe pas
mkdir -p public/plugins/dune-rp

# Copier les assets
if [ -d "$PLUGIN_PATH/assets" ]; then
    cp -r $PLUGIN_PATH/assets/* public/plugins/dune-rp/
    log_success "Assets copiés"
else
    log_warning "Aucun dossier assets trouvé"
fi

# Étape 6: Création des liens symboliques (optionnel)
if [ -L "public/plugins/dune-rp" ]; then
    log_info "Lien symbolique déjà existant"
else
    if [ -d "public/plugins/dune-rp" ]; then
        rm -rf public/plugins/dune-rp
    fi
    ln -s ../../$PLUGIN_PATH/assets public/plugins/dune-rp
    log_success "Lien symbolique créé"
fi

# Étape 7: Nettoyage des caches
log_info "Nettoyage des caches..."
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
log_success "Caches nettoyés"

# Étape 8: Correction des permissions
log_info "Correction des permissions..."
chmod -R 755 $PLUGIN_PATH
chmod -R 775 storage
chmod -R 775 bootstrap/cache
log_success "Permissions corrigées"

# Étape 9: Synchronisation des permissions (si applicable)
log_info "Synchronisation des permissions..."
php artisan permission:sync 2>/dev/null || log_warning "Commande permission:sync non disponible"

# Étape 10: Optimisation
log_info "Optimisation de l'application..."
php artisan optimize
log_success "Application optimisée"

# Résumé
echo ""
echo "=================================================="
echo -e "${GREEN}   Installation terminée avec succès !${NC}"
echo "=================================================="
echo ""
echo "Prochaines étapes :"
echo "1. Activez le plugin dans l'interface d'administration"
echo "2. Configurez les permissions pour les rôles"
echo "3. Créez votre première Maison Noble"
echo ""
echo "Documentation : https://github.com/nke69/azuriom-dune-rp"
echo ""

# Vérifications finales
log_info "Vérifications finales..."

# Vérifier que les tables ont été créées
TABLE_COUNT=$(php artisan tinker --execute="echo DB::select('SHOW TABLES LIKE \'dune_rp_%\'');" 2>/dev/null | grep -c "dune_rp" || echo "0")

if [ "$TABLE_COUNT" -gt "0" ]; then
    log_success "Tables de base de données créées"
else
    log_warning "Aucune table trouvée - vérifiez les migrations"
fi

# Vérifier les fichiers critiques
CRITICAL_FILES=(
    "$PLUGIN_PATH/plugin.json"
    "$PLUGIN_PATH/src/Providers/DuneRpServiceProvider.php"
    "$PLUGIN_PATH/src/Providers/RouteServiceProvider.php"
)

for file in "${CRITICAL_FILES[@]}"; do
    if [ -f "$file" ]; then
        log_success "✓ $(basename $file)"
    else
        log_error "✗ $(basename $file) manquant"
    fi
done

echo ""
log_success "Installation complète !"
