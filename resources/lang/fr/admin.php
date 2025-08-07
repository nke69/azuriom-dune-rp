<?php

return [
    // Navigation admin
    'nav' => [
        'title' => 'Dune RP',
        'houses' => 'Maisons',
        'characters' => 'Personnages',
        'events' => 'Événements',
        'settings' => 'Paramètres',
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

    // Gestion des maisons
    'houses' => [
        'title' => 'Gestion des Maisons',
        'create' => 'Créer une Maison',
        'edit' => 'Modifier la Maison',
        'delete_confirm' => 'Êtes-vous sûr de vouloir supprimer cette Maison ?',
        'created' => 'Maison créée avec succès',
        'updated' => 'Maison mise à jour avec succès',
        'deleted' => 'Maison supprimée avec succès',
        'spice_adjusted' => 'Réserves d\'épice ajustées avec succès',
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
        ],
        'spice' => [
            'adjust' => 'Ajuster l\'Épice',
            'add' => 'Ajouter',
            'remove' => 'Retirer',
            'amount' => 'Montant',
            'reason' => 'Raison',
        ],
    ],

    // Gestion des personnages
    'characters' => [
        'title' => 'Gestion des Personnages',
        'pending' => 'En Attente d\'Approbation',
        'approve' => 'Approuver',
        'reject' => 'Rejeter',
        'approved' => 'Personnage approuvé avec succès',
        'rejected' => 'Personnage rejeté avec succès',
        'updated' => 'Personnage mis à jour avec succès',
        'deleted' => 'Personnage supprimé avec succès',
        'delete_confirm' => 'Êtes-vous sûr de vouloir supprimer ce personnage ?',
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
        'complete' => 'Marquer comme Terminé',
        'cancel' => 'Annuler l\'Événement',
        'delete_confirm' => 'Êtes-vous sûr de vouloir supprimer cet événement ?',
        'created' => 'Événement créé avec succès',
        'updated' => 'Événement mis à jour avec succès',
        'deleted' => 'Événement supprimé avec succès',
        'completed' => 'Événement marqué comme terminé',
        'cancelled' => 'Événement annulé',
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

    // Messages génériques
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
];
