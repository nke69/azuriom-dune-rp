<?php

return [
    // Navigation admin
    'nav' => [
        'title' => 'Dune RP',
        'houses' => 'Maisons',
        'characters' => 'Personnages',
        'events' => 'Événements',
        'settings' => 'Paramètres',
        'spice' => 'Transactions Épice',
    ],

    // Permissions
    'permissions' => [
        'houses' => [
            'manage' => 'Gérer les Maisons',
        ],
        'characters' => [
            'manage' => 'Gérer les Personnages',
        ],
        'events' => [
            'manage' => 'Gérer les Événements',
        ],
        'settings' => 'Gérer les Paramètres',
    ],

    // Messages génériques
    'all' => 'Tous',
    'all_statuses' => 'Tous les statuts',
    'search' => 'Rechercher',
    'filters' => 'Filtres',
    'status' => 'Statut',
    'actions' => 'Actions',
    'no_items' => 'Aucun élément trouvé',
    'confirm_delete' => 'Cette action est irréversible. Êtes-vous sûr ?',
    'created_at' => 'Créé le',
    'updated_at' => 'Mis à jour le',
    'select_player' => 'Sélectionner un joueur',
    'no_house' => 'Aucune maison',

    // Statistiques
    'stats' => [
        'total' => 'Total',
        'active' => 'Actifs',
        'inactive' => 'Inactifs',
        'pending' => 'En attente',
        'approved' => 'Approuvés',
        'public' => 'Publics',
    ],

    // Gestion des maisons
    'houses' => [
        'title' => 'Gestion des Maisons',
        'create' => 'Créer une Maison',
        'edit' => 'Modifier la Maison',
        'show' => 'Détails de la Maison',
        'details' => 'Détails',
        'members' => 'Membres',
        'no_members' => 'Aucun membre',
        'delete_confirm' => 'Êtes-vous sûr de vouloir supprimer cette Maison ?',
        'created' => 'Maison créée avec succès',
        'updated' => 'Maison mise à jour avec succès',
        'deleted' => 'Maison supprimée avec succès',
        'spice_adjusted' => 'Réserves d\'épice ajustées avec succès',
        'search_placeholder' => 'Rechercher une maison...',
        'no_houses' => 'Aucune maison trouvée',
        'no_houses_desc' => 'Commencez par créer votre première maison noble',
        'create_first' => 'Créer la première maison',
        'has_leader' => 'A un dirigeant',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'visual_identity' => 'Identité visuelle',
        'color_preview' => 'Aperçu de la couleur',
        'adjust_spice' => 'Ajuster l\'épice',
        'adjustment_type' => 'Type d\'ajustement',
        'add_spice' => 'Ajouter de l\'épice',
        'remove_spice' => 'Retirer de l\'épice',
        'set_spice' => 'Définir l\'épice',
        'amount' => 'Montant',
        
        'fields' => [
            'name' => 'Nom de la Maison',
            'motto' => 'Devise',
            'description' => 'Description',
            'leader' => 'Dirigeant',
            'homeworld' => 'Monde Natal',
            'color' => 'Couleur',
            'spice_reserves' => 'Réserves d\'Épice',
            'influence_points' => 'Points d\'Influence',
            'sigil' => 'Blason',
            'is_active' => 'Actif',
            'avatar' => 'Avatar',
            'biography' => 'Biographie',
            'birthworld' => 'Monde de naissance',
            'age' => 'Âge',
        ],
        
        'placeholders' => [
            'name' => 'Ex: Maison Atréides',
            'motto' => 'Ex: Il n\'y a pas de demande plus haute',
            'description' => 'Histoire et description de la maison...',
            'homeworld' => 'Ex: Caladan',
        ],
        
        'help' => [
            'description' => 'Markdown supporté. Maximum 5000 caractères.',
            'sigil' => 'Image JPG ou PNG, max 2MB',
            'color' => 'Couleur utilisée pour l\'affichage de la maison',
        ],
        
        'spice' => [
            'title' => 'Gestion de l\'Épice',
            'adjust' => 'Ajuster l\'Épice',
            'current' => 'Réserves actuelles',
            'add' => 'Ajouter',
            'remove' => 'Retirer',
            'set' => 'Définir',
            'amount' => 'Montant',
            'reason' => 'Raison',
            'type' => 'Type d\'ajustement',
        ],
        
        'stats' => [
            'total' => 'Total des maisons',
            'active' => 'Maisons actives',
            'total_members' => 'Membres totaux',
            'total_spice' => 'Épice totale',
        ],
    ],

    // Gestion des personnages
    'characters' => [
        'title' => 'Gestion des Personnages',
        'create' => 'Créer un Personnage',
        'edit' => 'Modifier le Personnage',
        'show' => 'Détails du Personnage',
        'pending' => 'En Attente d\'Approbation',
        'approve' => 'Approuver',
        'reject' => 'Rejeter',
        'approved' => 'Personnage approuvé avec succès',
        'rejected' => 'Personnage rejeté avec succès',
        'created' => 'Personnage créé avec succès',
        'updated' => 'Personnage mis à jour avec succès',
        'deleted' => 'Personnage supprimé avec succès',
        'delete_confirm' => 'Êtes-vous sûr de vouloir supprimer ce personnage ?',
        'confirm_delete' => 'Confirmer la suppression',
        'no_characters' => 'Aucun personnage trouvé',
        'no_house' => 'Sans maison',
        'search_placeholder' => 'Rechercher un personnage...',
        
        'fields' => [
            'player' => 'Joueur',
            'name' => 'Nom du Personnage',
            'title' => 'Titre',
            'house' => 'Maison',
            'status' => 'Statut',
            'created_at' => 'Créé le',
            'is_approved' => 'Approuvé',
            'is_public' => 'Public',
            'spice_addiction' => 'Addiction à l\'Épice',
            'abilities' => 'Capacités Spéciales',
            'biography' => 'Biographie',
            'birthworld' => 'Monde de naissance',
            'age' => 'Âge',
            'avatar' => 'Avatar',
        ],
        
        'filters' => [
            'all' => 'Tous les personnages',
            'pending' => 'En attente',
            'approved' => 'Approuvés',
            'rejected' => 'Rejetés',
        ],
    ],

    // Gestion des événements
    'events' => [
        'title' => 'Gestion des Événements',
        'create' => 'Créer un Événement',
        'edit' => 'Modifier l\'Événement',
        'show' => 'Détails de l\'Événement',
        'complete' => 'Marquer comme Terminé',
        'cancel' => 'Annuler l\'Événement',
        'delete_confirm' => 'Êtes-vous sûr de vouloir supprimer cet événement ?',
        'created' => 'Événement créé avec succès',
        'updated' => 'Événement mis à jour avec succès',
        'deleted' => 'Événement supprimé avec succès',
        'completed' => 'Événement marqué comme terminé',
        'cancelled' => 'Événement annulé',
        'confirm_complete' => 'Marquer cet événement comme terminé ?',
        'confirm_cancel' => 'Annuler cet événement ?',
        'view_public' => 'Voir la page publique',
        'search_placeholder' => 'Rechercher un événement...',
        
        'fields' => [
            'title' => 'Titre',
            'description' => 'Description',
            'organizer' => 'Organisateur',
            'organizer_house' => 'Maison Organisatrice',
            'event_date' => 'Date de l\'Événement',
            'location' => 'Lieu',
            'max_participants' => 'Participants Maximum',
            'spice_cost' => 'Coût en Épice',
            'reward_spice' => 'Récompense en Épice',
            'event_type' => 'Type d\'Événement',
            'status' => 'Statut',
            'is_public' => 'Public',
        ],
        
        'status' => [
            'planned' => 'Planifié',
            'ongoing' => 'En cours',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
        ],
        
        'types' => [
            'harvest' => 'Récolte',
            'meeting' => 'Réunion',
            'meeting_desc' => 'Réunion politique ou diplomatique',
            'battle' => 'Bataille',
            'battle_desc' => 'Combat ou affrontement militaire',
            'ceremony' => 'Cérémonie',
            'ceremony_desc' => 'Événement cérémoniel ou rituel',
            'trade' => 'Commerce',
            'trade_desc' => 'Négociation commerciale',
            'exploration' => 'Exploration',
        ],
        
        'stats' => [
            'total' => 'Total des événements',
            'planned' => 'Planifiés',
            'ongoing' => 'En cours',
            'completed' => 'Terminés',
            'cancelled' => 'Annulés',
        ],
    ],

    // Transactions d'épice
    'spice' => [
        'unit' => 'tonnes',
        'transactions' => 'Transactions d\'Épice',
        'recent' => 'Transactions récentes',
        'no_transactions' => 'Aucune transaction',
    ],

    // Logs d'actions
    'logs' => [
        'houses' => [
            'created' => 'Maison créée',
            'updated' => 'Maison mise à jour',
            'deleted' => 'Maison supprimée',
        ],
        'characters' => [
            'created' => 'Personnage créé',
            'updated' => 'Personnage mis à jour',
            'deleted' => 'Personnage supprimé',
            'approved' => 'Personnage approuvé',
            'rejected' => 'Personnage rejeté',
        ],
        'spice_transactions' => [
            'created' => 'Transaction d\'épice créée',
        ],
        'rp_events' => [
            'created' => 'Événement RP créé',
            'updated' => 'Événement RP mis à jour',
            'deleted' => 'Événement RP supprimé',
            'completed' => 'Événement RP terminé',
            'cancelled' => 'Événement RP annulé',
        ],
    ],

    // Messages génériques pour les opérations
    'messages' => [
        'confirm_delete' => 'Cette action est irréversible.',
        'no_items' => 'Aucun élément trouvé.',
        'search_placeholder' => 'Rechercher...',
        'actions' => 'Actions',
        'bulk_actions' => 'Actions en lot',
        'select_all' => 'Tout sélectionner',
        'export' => 'Exporter',
        'import' => 'Importer',
    ],
    
    // Erreurs
    'errors' => [
        'validation' => 'Erreur de validation',
        'not_found' => 'Élément non trouvé',
        'unauthorized' => 'Action non autorisée',
        'server_error' => 'Erreur serveur',
    ],
];
